<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\Coupon;
use App\Models\LoyaltyAccount;
use App\Models\Order;
use App\Models\PushCampaign;
use App\Models\PushSubscription;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMarketingController extends Controller
{
    /**
     * Display consolidated marketing performance, ROI analytics, and channel metrics.
     */
    public function dashboard(Request $request): View
    {
        // 1. Coupon Performance & ROI
        $couponOrders = Order::whereNotNull('coupon_code')
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $totalCouponsUsed = $couponOrders->count();
        $totalDiscountGranted = (float) Order::whereNotIn('status', ['cancelled'])->sum('discount');
        $couponDrivenRevenue = (float) $couponOrders->sum('total');

        // Top Performing Coupons
        $coupons = Coupon::all()->map(function ($coupon) use ($couponOrders) {
            $matchingOrders = $couponOrders->where('coupon_code', $coupon->code);
            return [
                'id'             => $coupon->id,
                'code'           => $coupon->code,
                'type'           => $coupon->type,
                'value'          => $coupon->value,
                'is_active'      => $coupon->is_active,
                'uses_count'     => $matchingOrders->count(),
                'discount_given' => (float) $matchingOrders->sum('discount'),
                'revenue_driven' => (float) $matchingOrders->sum('total'),
            ];
        })->sortByDesc('uses_count')->values()->take(5);

        // 2. Newsletter Subscribers
        $totalSubscribers = Subscriber::where('is_active', true)->count();
        $newSubscribersThisMonth = Subscriber::where('is_active', true)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        // 3. Web Push Notifications
        $totalPushSubscribers = PushSubscription::where('is_active', true)->count();
        $pushCampaignsSent = PushCampaign::where('status', 'sent')->count();
        $totalPushDeliveries = (int) (PushCampaign::sum('sent_count') ?? 0);

        // 4. Abandoned Carts Recovery Performance
        $totalAbandoned = AbandonedCart::count();
        $totalRecovered = AbandonedCart::where('is_recovered', true)->count();
        $recoveryRate = $totalAbandoned > 0 ? round(($totalRecovered / $totalAbandoned) * 100, 1) : 0;
        $unrecoveredPipeline = (float) AbandonedCart::where('is_recovered', false)->sum('total_amount');
        $recoveredRevenue = (float) AbandonedCart::where('is_recovered', true)->sum('total_amount');

        // 5. Loyalty Program Engagement
        $loyaltyMembersCount = LoyaltyAccount::count();
        $totalPointsBalance = (int) LoyaltyAccount::sum('points_balance');
        $totalPointsRedeemed = (int) LoyaltyAccount::sum('points_redeemed_lifetime');

        // Channel Share Breakdown
        $posOrdersCount = Order::where('channel', 'pos')->whereNotIn('status', ['cancelled'])->count();
        $webOrdersCount = Order::where('channel', 'web')->whereNotIn('status', ['cancelled'])->count();
        $totalOrdersCount = $posOrdersCount + $webOrdersCount;

        $webShare = $totalOrdersCount > 0 ? round(($webOrdersCount / $totalOrdersCount) * 100, 1) : 0;
        $posShare = $totalOrdersCount > 0 ? round(($posOrdersCount / $totalOrdersCount) * 100, 1) : 0;

        return view('admin.marketing.dashboard', compact(
            'totalCouponsUsed',
            'totalDiscountGranted',
            'couponDrivenRevenue',
            'coupons',
            'totalSubscribers',
            'newSubscribersThisMonth',
            'totalPushSubscribers',
            'pushCampaignsSent',
            'totalPushDeliveries',
            'totalAbandoned',
            'totalRecovered',
            'recoveryRate',
            'unrecoveredPipeline',
            'recoveredRevenue',
            'loyaltyMembersCount',
            'totalPointsBalance',
            'totalPointsRedeemed',
            'webOrdersCount',
            'posOrdersCount',
            'webShare',
            'posShare'
        ));
    }
}

