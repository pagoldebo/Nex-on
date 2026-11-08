<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $status = $request->input('status', 'all');

        $query = User::withCount('orders')
            ->withSum('orders', 'total')
            ->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->whereNull('deactivated_at');
        } elseif ($status === 'deactivated') {
            $query->whereNotNull('deactivated_at');
        }

        $customers = $query->paginate(15)->withQueryString();

        $stats = [
            'total_customers'       => User::count(),
            'active_customers'      => User::whereNull('deactivated_at')->count(),
            'deactivated_customers' => User::whereNotNull('deactivated_at')->count(),
            'ordering_customers'    => User::has('orders')->count(),
            'total_lifetime_value'  => \App\Models\Order::whereNotNull('user_id')->sum('total'),
        ];

        return view('admin.customers.index', compact('customers', 'search', 'status', 'stats'));
    }

    public function show(User $customer): View
    {
        $customer->loadCount('orders');
        $customer->loadSum('orders', 'total');
        
        $orders = $customer->orders()->with('items')->latest()->paginate(10);
        $addresses = $customer->addresses()->get();

        return view('admin.customers.show', compact('customer', 'orders', 'addresses'));
    }

    /**
     * Toggle customer active / deactivated status.
     */
    public function toggleStatus(User $customer): RedirectResponse
    {
        if ($customer->isDeactivated()) {
            $customer->deactivated_at = null;
            $msg = "Customer account for {$customer->name} has been activated.";
        } else {
            $customer->deactivated_at = now();
            $msg = "Customer account for {$customer->name} has been deactivated.";
        }

        $customer->save();

        return back()->with('success', $msg);
    }

    /**
     * Admin reset of a customer's password.
     */
    public function updatePassword(Request $request, User $customer): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $customer->password = Hash::make($validated['password']);
        $customer->save();

        return back()->with('success', "Password for {$customer->name} has been reset successfully.");
    }
}
