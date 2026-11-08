<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminExpenseController extends Controller
{
    /**
     * Display the expenses ledger, analytics, and category breakdown.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', 'this_month');
        $categoryId = $request->input('category_id');
        $paymentMethod = $request->input('payment_method');
        $search = $request->input('search');

        // Determine date bounds
        [$startDate, $endDate] = match ($period) {
            'today'       => [now()->startOfDay(), now()->endOfDay()],
            'this_week'   => [now()->startOfWeek(), now()->endOfWeek()],
            'last_month'  => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year'   => [now()->startOfYear(), now()->endOfYear()],
            'all'         => [Carbon::parse('2020-01-01'), now()->endOfDay()],
            default       => [now()->startOfMonth(), now()->endOfMonth()], // 'this_month'
        };

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $startDate = Carbon::parse($request->input('from_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('to_date'))->endOfDay();
            $period = 'custom';
        }

        // Query filtered expenses
        $query = Expense::with(['category', 'outlet', 'creator'])
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('expense_number', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('vendor_gstin', 'like', "%{$search}%");
            });
        }

        $expenses = (clone $query)->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // High-level KPI aggregations for period
        $periodExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();
        $totalSpend = $periodExpenses->sum('amount');
        $totalGstItc = $periodExpenses->sum('tax_amount');
        
        // Recurring run-rate (monthly projection)
        $recurringMonthly = Expense::where('is_recurring', true)
            ->where('recurring_frequency', 'monthly')
            ->sum('amount');

        // Category breakdown for charts & pills
        $categoryBreakdown = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.id, expense_categories.name, expense_categories.color, SUM(expenses.amount) as total_amount, COUNT(expenses.id) as expense_count')
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->orderByDesc('total_amount')
            ->get();

        $topCategory = $categoryBreakdown->first();

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $outlets = Outlet::active()->orderBy('name')->get();

        return view('admin.finance.expenses', compact(
            'expenses',
            'categories',
            'outlets',
            'totalSpend',
            'totalGstItc',
            'recurringMonthly',
            'categoryBreakdown',
            'topCategory',
            'period',
            'categoryId',
            'paymentMethod',
            'search',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Store a newly created expense record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'outlet_id'           => 'nullable|exists:outlets,id',
            'title'               => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'tax_amount'          => 'nullable|numeric|min:0',
            'payment_method'      => 'required|in:cash,bank_transfer,upi,card,cheque',
            'expense_date'        => 'required|date',
            'vendor_name'         => 'nullable|string|max:150',
            'vendor_gstin'        => 'nullable|string|max:50',
            'reference_no'        => 'nullable|string|max:100',
            'receipt'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120',
            'is_recurring'        => 'nullable|boolean',
            'recurring_frequency' => 'nullable|in:monthly,quarterly,yearly',
            'notes'               => 'nullable|string|max:1000',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create([
            'expense_category_id' => $validated['expense_category_id'],
            'outlet_id'           => $validated['outlet_id'] ?? null,
            'title'               => $validated['title'],
            'amount'              => $validated['amount'],
            'tax_amount'          => $validated['tax_amount'] ?? 0,
            'payment_method'      => $validated['payment_method'],
            'expense_date'        => $validated['expense_date'],
            'vendor_name'         => $validated['vendor_name'] ?? null,
            'vendor_gstin'        => $validated['vendor_gstin'] ?? null,
            'reference_no'        => $validated['reference_no'] ?? null,
            'receipt_path'        => $receiptPath,
            'is_recurring'        => $request->boolean('is_recurring'),
            'recurring_frequency' => $request->boolean('is_recurring') ? ($validated['recurring_frequency'] ?? 'monthly') : null,
            'notes'               => $validated['notes'] ?? null,
            'created_by'          => auth()->id(),
        ]);

        return redirect()->route('admin.finance.expenses')
            ->with('success', 'Operating expense logged successfully.');
    }

    /**
     * Store a new custom expense category.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:expense_categories,name',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        ExpenseCategory::create([
            'name'        => $validated['name'],
            'color'       => $validated['color'] ?? '#6366f1',
            'description' => $validated['description'] ?? null,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.finance.expenses')
            ->with('success', 'Expense category created successfully.');
    }

    /**
     * Delete an expense record.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('admin.finance.expenses')
            ->with('success', 'Expense record deleted.');
    }
}

