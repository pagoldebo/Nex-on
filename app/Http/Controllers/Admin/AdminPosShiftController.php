<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PosShift;
use App\Models\PosShiftMovement;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminPosShiftController extends Controller
{
    /**
     * Display listing of POS cashier shifts and drawer reconciliations.
     */
    public function index(Request $request): View
    {
        $query = PosShift::with(['cashier', 'movements']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->input('cashier_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('shift_code', 'like', "%{$search}%")
                  ->orWhereHas('cashier', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        $shifts = $query->latest('opened_at')->paginate(15)->withQueryString();

        // Recompute metrics on open shifts so live view is always current
        foreach ($shifts as $s) {
            if ($s->status === 'open') {
                $s->recomputeLiveMetrics();
            }
        }

        $openShiftsCount = PosShift::where('status', 'open')->count();
        $totalSalesToday = (float) PosShift::whereDate('opened_at', today())->sum('total_sales');
        $totalDrawerCash = (float) PosShift::where('status', 'open')->sum('expected_cash');
        $cashDiscrepancies = (float) PosShift::where('status', 'closed')->sum('cash_difference');

        $activeCashierShift = PosShift::where('cashier_id', Auth::id())->where('status', 'open')->latest()->first();
        $cashiers = User::whereIn('role', ['admin', 'staff'])->orderBy('name')->get();

        return view('admin.pos.shifts', compact(
            'shifts',
            'openShiftsCount',
            'totalSalesToday',
            'totalDrawerCash',
            'cashDiscrepancies',
            'activeCashierShift',
            'cashiers'
        ));
    }

    /**
     * Open a new cashier shift with an opening cash float.
     */
    public function openShift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_float' => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:255',
        ]);

        $cashierId = Auth::id();

        // Check if user already has an active open shift
        $existing = PosShift::where('cashier_id', $cashierId)->where('status', 'open')->first();
        if ($existing) {
            return redirect()->route('admin.pos.shifts.index')
                ->with('info', "You already have an active open shift (#{$existing->shift_code}). Please close it before opening a new one.");
        }

        $openingFloat = (float) $validated['opening_float'];

        $shift = PosShift::create([
            'cashier_id'         => $cashierId,
            'opened_at'          => now(),
            'status'             => 'open',
            'opening_float'      => $openingFloat,
            'expected_cash'      => $openingFloat,
            'cash_difference'    => 0,
            'cash_sales'         => 0,
            'card_sales'         => 0,
            'upi_sales'          => 0,
            'total_sales'        => 0,
            'total_orders_count' => 0,
            'notes'              => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.pos.shifts.index')
            ->with('success', "Cashier Shift #{$shift->shift_code} opened with starting float ₹" . number_format($openingFloat, 2));
    }

    /**
     * Show shift details, audit breakdown, orders, and cash movements.
     */
    public function show(PosShift $shift): View
    {
        $shift->load(['cashier', 'movements.user']);
        if ($shift->status === 'open') {
            $shift->recomputeLiveMetrics();
        }

        $orders = $shift->getOrdersQuery()->latest()->paginate(20);

        return view('admin.pos.shift-show', compact('shift', 'orders'));
    }

    /**
     * Record Cash-In or Cash-Out drawer movement.
     */
    public function recordMovement(Request $request, PosShift $shift): RedirectResponse
    {
        $validated = $request->validate([
            'type'   => 'required|in:cash_in,cash_out',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:150',
        ]);

        $shift->movements()->create([
            'user_id' => Auth::id(),
            'type'    => $validated['type'],
            'amount'  => (float) $validated['amount'],
            'reason'  => $validated['reason'],
        ]);

        $shift->recomputeLiveMetrics();

        $typeLabel = $validated['type'] === 'cash_in' ? 'Cash In (Pay-In)' : 'Cash Out (Pay-Out)';

        return redirect()->route('admin.pos.shifts.index')
            ->with('success', "Drawer movement recorded: {$typeLabel} of ₹" . number_format($validated['amount'], 2));
    }

    /**
     * Close shift with counted cash (Z-Report generation).
     */
    public function closeShift(Request $request, PosShift $shift): RedirectResponse
    {
        $validated = $request->validate([
            'counted_cash' => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        $countedCash = (float) $validated['counted_cash'];
        $shift->closeShift($countedCash, $validated['notes'] ?? null);

        $diff = (float) $shift->cash_difference;
        $varianceMsg = $diff === 0.0
            ? 'Cash balanced perfectly (0 variance).'
            : ($diff > 0 ? "Drawer Over by +₹" . number_format($diff, 2) : "Drawer Short by -₹" . number_format(abs($diff), 2));

        return redirect()->route('admin.pos.shifts.index')
            ->with('success', "Shift #{$shift->shift_code} locked & closed. {$varianceMsg}");
    }

    /**
     * Printable X-Report (Mid-shift) or Z-Report (End of Day).
     */
    public function printReport(PosShift $shift, Request $request): View
    {
        $shift->load(['cashier', 'movements']);
        if ($shift->status === 'open') {
            $shift->recomputeLiveMetrics();
        }

        $reportType = $request->input('type', $shift->status === 'open' ? 'x' : 'z');

        $storeInfo = [
            'name'    => Setting::get('store_name', config('app.name', 'NEXON LUXURY POS')),
            'phone'   => Setting::get('store_phone', '+91 98765 43210'),
            'address' => Setting::get('store_address', '101, Luxury Galleria, Indiranagar, Bengaluru'),
            'gstin'   => Setting::get('store_gstin', '29AAAAA0000A1Z5'),
        ];

        return view('admin.pos.shift-report', compact('shift', 'reportType', 'storeInfo'));
    }
}

