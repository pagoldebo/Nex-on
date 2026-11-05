<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCustomerGroupController extends Controller
{
    /**
     * Display listing of customer groups and member statistics.
     */
    public function index(Request $request): View
    {
        $query = CustomerGroup::withCount('users');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $groups = $query->orderBy('name')->get();

        // Eligible customer users for assignment
        $customers = User::where(function ($q) {
            $q->where('role', 'customer')->orWhereNull('role');
        })->select('id', 'name', 'email')->orderBy('name')->get();

        return view('admin.customers.groups', compact('groups', 'customers'));
    }

    /**
     * Store a newly created customer group.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'code'           => 'nullable|string|max:50|unique:customer_groups,code',
            'description'    => 'nullable|string|max:500',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_spend'      => 'nullable|numeric|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        $code = !empty($validated['code']) 
            ? Str::slug($validated['code']) 
            : Str::slug($validated['name']);

        $baseCode = $code;
        $c = 1;
        while (CustomerGroup::where('code', $code)->exists()) {
            $code = "{$baseCode}-{$c}";
            $c++;
        }

        CustomerGroup::create([
            'name'           => $validated['name'],
            'code'           => $code,
            'description'    => $validated['description'] ?? null,
            'discount_type'  => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_spend'      => $validated['min_spend'] ?? 0,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Customer wholesale/VIP group created successfully.');
    }

    /**
     * Update an existing customer group.
     */
    public function update(Request $request, CustomerGroup $customerGroup): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'code'           => 'required|string|max:50|unique:customer_groups,code,' . $customerGroup->id,
            'description'    => 'nullable|string|max:500',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_spend'      => 'nullable|numeric|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        $customerGroup->update([
            'name'           => $validated['name'],
            'code'           => Str::slug($validated['code']),
            'description'    => $validated['description'] ?? null,
            'discount_type'  => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_spend'      => $validated['min_spend'] ?? 0,
            'is_active'      => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Customer group updated successfully.');
    }

    /**
     * Delete a customer group.
     */
    public function destroy(CustomerGroup $customerGroup): RedirectResponse
    {
        $customerGroup->delete();

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Customer group deleted successfully.');
    }

    /**
     * Assign customers to this group.
     */
    public function assignCustomers(Request $request, CustomerGroup $customerGroup): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $customerGroup->users()->syncWithoutDetaching($validated['user_ids']);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', count($validated['user_ids']) . ' customer(s) assigned to ' . $customerGroup->name . '.');
    }

    /**
     * Remove a customer from this group.
     */
    public function removeCustomer(CustomerGroup $customerGroup, User $user): RedirectResponse
    {
        $customerGroup->users()->detach($user->id);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', "Customer {$user->name} removed from {$customerGroup->name}.");
    }
}

