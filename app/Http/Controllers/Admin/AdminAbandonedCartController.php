<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAbandonedCartController extends Controller
{
    /**
     * Display abandoned cart directory, recovery analytics, and drip funnel.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'unrecovered');
        $search = $request->input('search');

        $query = AbandonedCart::with(['user', 'recoveredOrder']);

        if ($status === 'unrecovered') {
            $query->where('is_recovered', false);
        } elseif ($status === 'recovered') {
            $query->where('is_recovered', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $carts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // High-level KPI aggregations
        $allCarts = AbandonedCart::all();
        $totalAbandoned = $allCarts->count();
        $totalRecovered = $allCarts->where('is_recovered', true)->count();
        $recoveryRate = $totalAbandoned > 0 ? round(($totalRecovered / $totalAbandoned) * 100, 1) : 0;
        $unrecoveredPipeline = $allCarts->where('is_recovered', false)->sum('total_amount');
        $recoveredRevenue = $allCarts->where('is_recovered', true)->sum('total_amount');

        return view('admin.marketing.abandoned-carts', compact(
            'carts',
            'status',
            'search',
            'totalAbandoned',
            'totalRecovered',
            'recoveryRate',
            'unrecoveredPipeline',
            'recoveredRevenue'
        ));
    }

    /**
     * Advance the automated multi-stage drip sequence for this dropped cart.
     */
    public function triggerDrip(AbandonedCart $cart): RedirectResponse
    {
        if ($cart->is_recovered) {
            return back()->with('info', 'Cart is already recovered.');
        }

        $nextStep = min(3, $cart->recovery_step + 1);
        $cart->recovery_step = $nextStep;
        $cart->last_reminded_at = now();
        $cart->save();

        $actionName = match ($nextStep) {
            1 => 'Stage 1 (1-Hour) Gentle Reminder Email dispatched with restore link.',
            2 => 'Stage 2 (24-Hour) SMS Reminder with 10% exclusive discount coupon dispatched.',
            3 => 'Stage 3 (72-Hour) Final Urgency Push Notification dispatched.',
            default => 'Reminder dispatched.',
        };

        return back()->with('success', $actionName);
    }

    /**
     * Manually mark an abandoned cart as recovered.
     */
    public function markRecovered(AbandonedCart $cart): RedirectResponse
    {
        $cart->update([
            'is_recovered' => true,
            'recovered_at' => now(),
        ]);

        return back()->with('success', "Cart for {$cart->customer_name} marked as successfully recovered.");
    }

    /**
     * Delete abandoned cart record.
     */
    public function destroy(AbandonedCart $cart): RedirectResponse
    {
        $cart->delete();

        return back()->with('success', 'Abandoned cart record removed.');
    }

    /**
     * Storefront 1-Click recovery route: restores items into session and redirects to checkout.
     */
    public function restore(string $hash): RedirectResponse
    {
        $cart = AbandonedCart::where('recovery_hash', $hash)->firstOrFail();

        // Restore items to session cart
        if (!empty($cart->cart_data) && is_array($cart->cart_data)) {
            session(['cart' => $cart->cart_data]);
        }

        return redirect()->route('cart')
            ->with('success', 'Your shopping bag has been completely restored! Please proceed to checkout.');
    }

    /**
     * Simulate an abandoned cart from catalog items to test the recovery workflow.
     */
    public function simulate(): RedirectResponse
    {
        $variant = ProductVariant::with(['product', 'color'])->first();

        $cartData = [
            $variant->id ?? 1 => [
                'name'     => $variant ? $variant->product->name : 'Mulberry Silk Slip Dress',
                'sku'      => $variant->sku ?? 'MYS-SILK-01',
                'size'     => $variant->size ?? 'M',
                'color'    => $variant && $variant->color ? $variant->color->name : 'Midnight Noir',
                'price'    => $variant->price ?? 4500,
                'qty'      => 1,
                'image'    => '',
            ]
        ];

        AbandonedCart::create([
            'customer_name' => 'Kavya Maran',
            'email'         => 'kavya.maran@sunnetwork.in',
            'phone'         => '+91 98400 12345',
            'cart_data'     => $cartData,
            'total_amount'  => $variant->price ?? 4500,
            'items_count'   => 1,
            'recovery_step' => 0,
        ]);

        return back()->with('success', 'Simulated abandoned checkout created for testing recovery drip triggers.');
    }
}

