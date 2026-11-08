<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPersonnel;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDeliveryPersonnelController extends Controller
{
    /**
     * Display all drivers and logistics fleet.
     */
    public function index(Request $request): View
    {
        $fleet = DeliveryPersonnel::withCount([
            'orders as active_orders_count' => fn($q) => $q->whereIn('delivery_status', ['assigned', 'out_for_delivery']),
            'orders as completed_orders_count' => fn($q) => $q->where('delivery_status', 'delivered'),
        ])
        ->when($request->query('status'), fn($q, $s) => $q->where('status', $s))
        ->when($request->query('search'), fn($q, $term) => $q->where(function ($sub) use ($term) {
            $sub->where('name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('vehicle_number', 'like', "%{$term}%");
        }))
        ->latest()
        ->paginate(15);

        $kpis = [
            'total_drivers'    => DeliveryPersonnel::count(),
            'active_drivers'   => DeliveryPersonnel::where('status', 'active')->count(),
            'out_for_delivery' => Order::where('delivery_status', 'out_for_delivery')->count(),
            'total_delivered'  => Order::where('delivery_status', 'delivered')->count(),
        ];

        return view('admin.fleet.index', compact('fleet', 'kpis'));
    }

    /**
     * Store new delivery personnel.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'phone'          => 'required|string|max:30|unique:delivery_personnel,phone',
            'email'          => 'nullable|email|max:150',
            'vehicle_number' => 'nullable|string|max:50',
            'vehicle_type'   => 'required|in:bike,van,car,scooter',
            'status'         => 'required|in:active,inactive',
        ]);

        DeliveryPersonnel::create($validated);

        return back()->with('success', "Delivery driver {$validated['name']} registered successfully!");
    }

    /**
     * Update driver details.
     */
    public function update(Request $request, DeliveryPersonnel $personnel): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'phone'          => 'required|string|max:30|unique:delivery_personnel,phone,' . $personnel->id,
            'email'          => 'nullable|email|max:150',
            'vehicle_number' => 'nullable|string|max:50',
            'vehicle_type'   => 'required|in:bike,van,car,scooter',
            'status'         => 'required|in:active,inactive',
        ]);

        $personnel->update($validated);

        return back()->with('success', "Driver {$personnel->name} updated successfully.");
    }

    /**
     * Assign order to a delivery personnel.
     */
    public function assignOrder(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'delivery_personnel_id' => 'required|exists:delivery_personnel,id',
        ]);

        $personnel = DeliveryPersonnel::findOrFail($request->input('delivery_personnel_id'));

        $order->update([
            'delivery_personnel_id' => $personnel->id,
            'delivery_status'       => 'assigned',
        ]);

        return back()->with('success', "Order #{$order->reference} assigned to {$personnel->name}.");
    }

    /**
     * Mobile driver portal for checking assigned drop-offs and updating proof.
     */
    public function driverPortal(Request $request): View
    {
        $phone = $request->query('phone') ?? session('driver_phone');
        $driver = null;
        $orders = collect();

        if ($phone) {
            $driver = DeliveryPersonnel::where('phone', $phone)->first();
            if ($driver) {
                session(['driver_phone' => $phone]);
                $orders = Order::with('items.variant.product')
                    ->where('delivery_personnel_id', $driver->id)
                    ->whereIn('delivery_status', ['assigned', 'out_for_delivery'])
                    ->latest()
                    ->get();
            }
        }

        return view('storefront.driver.portal', compact('driver', 'orders', 'phone'));
    }

    /**
     * Driver updates order status with proof of delivery.
     */
    public function driverUpdateStatus(Request $request, Order $order): RedirectResponse
    {
        // Object-level authorization: ensure the requesting driver is assigned to this order, or the user is an admin
        $driverPhone = session('driver_phone');
        $driver = $driverPhone ? DeliveryPersonnel::where('phone', $driverPhone)->first() : null;
        $isAdmin = Auth::check() && Auth::user()->isAdmin();

        if (! $isAdmin && (! $driver || $order->delivery_personnel_id !== $driver->id)) {
            abort(403, 'Unauthorized. This delivery order is not assigned to your driver account.');
        }

        $action = $request->input('action'); // 'start' (out_for_delivery) or 'complete' (delivered)

        if ($action === 'start') {
            $order->update([
                'delivery_status' => 'out_for_delivery',
                'status'          => 'shipped',
                'shipped_at'      => $order->shipped_at ?? now(),
            ]);
            return back()->with('success', "Order #{$order->reference} marked as Out for Delivery!");
        }

        if ($action === 'complete') {
            $request->validate([
                'proof_signature' => 'nullable|string|max:150',
                'proof_notes'     => 'nullable|string|max:500',
            ]);

            $order->update([
                'delivery_status' => 'delivered',
                'status'          => 'delivered',
                'delivered_at'    => now(),
                'proof_signature' => $request->input('proof_signature', 'Digital Customer Sign-off'),
                'proof_notes'     => $request->input('proof_notes'),
            ]);

            return back()->with('success', "Order #{$order->reference} marked as Successfully Delivered!");
        }

        return back()->with('error', 'Invalid driver delivery action.');
    }
}
