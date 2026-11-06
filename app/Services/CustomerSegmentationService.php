<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerSegmentationService
{
    /**
     * Compute RFM metrics and segment for all customers or a specific customer.
     */
    public function getCustomerMetrics(?string $segmentFilter = null, ?string $search = null): Collection
    {
        // Query users with customer role or role null
        $query = User::query()
            ->where(function ($q) {
                $q->where('role', 'customer')
                  ->orWhereNull('role');
            })
            ->with(['orders' => function ($q) {
                $q->where('status', '!=', 'cancelled')->latest();
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $now = Carbon::now();
        $customers = $query->get()->map(function ($user) use ($now) {
            $orders = $user->orders;
            $frequency = $orders->count();
            $monetary = (float) $orders->sum('total');
            $latestOrder = $orders->first();
            
            $recencyDays = $latestOrder ? (int) $now->diffInDays($latestOrder->created_at) : null;

            $segment = $this->classifySegment($frequency, $monetary, $recencyDays);

            return (object) [
                'user'         => $user,
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'joined_at'    => $user->created_at,
                'frequency'    => $frequency,
                'monetary'     => $monetary,
                'recency_days' => $recencyDays,
                'latest_order' => $latestOrder?->created_at,
                'segment'      => $segment,
            ];
        });

        if ($segmentFilter && $segmentFilter !== 'all') {
            $customers = $customers->filter(fn ($item) => strtolower($item->segment['key']) === strtolower($segmentFilter))->values();
        }

        return $customers;
    }

    /**
     * Classify customer into an RFM segment.
     */
    public function classifySegment(int $frequency, float $monetary, ?int $recencyDays): array
    {
        if ($frequency === 0 || $recencyDays === null) {
            return [
                'key'         => 'prospect',
                'name'        => 'Prospect / Lead',
                'description' => 'Registered an account but has not yet placed an order',
                'badge_color' => 'bg-gray-100 text-gray-800 border-gray-200',
                'action'      => 'Send Welcome Incentive / 10% First Order Coupon',
            ];
        }

        // Champions: Bought recently, order often, spend a lot
        if ($recencyDays <= 45 && $frequency >= 3 && $monetary >= 3000) {
            return [
                'key'         => 'champions',
                'name'        => 'Champions',
                'description' => 'Top tier shoppers: high frequency, high spend, ordered recently',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'action'      => 'Early VIP Collection Access & Premium Gift Inclusions',
            ];
        }

        // Loyal Customers: High or medium frequency, healthy spend, relatively active
        if ($frequency >= 3 && $recencyDays <= 90) {
            return [
                'key'         => 'loyal',
                'name'        => 'Loyal Customers',
                'description' => 'Regular buyers with repeat purchases within the past 90 days',
                'badge_color' => 'bg-blue-100 text-blue-800 border-blue-200',
                'action'      => 'Upsell higher-value products & invite to VIP tier',
            ];
        }

        // Potential Loyalists: Recent shoppers with 1-2 orders
        if ($recencyDays <= 30 && $frequency >= 1) {
            return [
                'key'         => 'potential',
                'name'        => 'Potential Loyalists',
                'description' => 'Recent buyers with high promise to become regular shoppers',
                'badge_color' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                'action'      => 'Offer repeat purchase incentive / loyalty points',
            ];
        }

        // At Risk: High lifetime value but inactive for > 90 days
        if ($recencyDays > 90 && $monetary >= 2000) {
            return [
                'key'         => 'at_risk',
                'name'        => 'At Risk',
                'description' => 'Valuable past spenders who have not ordered in over 90 days',
                'badge_color' => 'bg-amber-100 text-amber-800 border-amber-200',
                'action'      => 'Send Win-Back SMS / Personalized Re-engagement offer',
            ];
        }

        // Hibernating: Inactive for > 90 days with low frequency
        if ($recencyDays > 90) {
            return [
                'key'         => 'hibernating',
                'name'        => 'Hibernating',
                'description' => 'Low spend, inactive for extended period (>90 days)',
                'badge_color' => 'bg-rose-100 text-rose-800 border-rose-200',
                'action'      => 'Seasonal clearance announcements or re-permission campaigns',
            ];
        }

        // Promising: Active between 31-90 days, 1-2 orders
        return [
            'key'         => 'promising',
            'name'        => 'Promising',
            'description' => 'Moderate recency, placed occasional orders',
            'badge_color' => 'bg-purple-100 text-purple-800 border-purple-200',
            'action'      => 'Recommend trending accessories & popular categories',
        ];
    }

    /**
     * Get aggregate segment statistics.
     */
    public function getSegmentDistribution(): array
    {
        $all = $this->getCustomerMetrics();
        $total = $all->count();

        $segments = [
            'champions'   => ['name' => 'Champions', 'count' => 0, 'spend' => 0.0],
            'loyal'       => ['name' => 'Loyal Customers', 'count' => 0, 'spend' => 0.0],
            'potential'   => ['name' => 'Potential Loyalists', 'count' => 0, 'spend' => 0.0],
            'promising'   => ['name' => 'Promising', 'count' => 0, 'spend' => 0.0],
            'at_risk'     => ['name' => 'At Risk', 'count' => 0, 'spend' => 0.0],
            'hibernating' => ['name' => 'Hibernating', 'count' => 0, 'spend' => 0.0],
            'prospect'    => ['name' => 'Prospect / Lead', 'count' => 0, 'spend' => 0.0],
        ];

        foreach ($all as $item) {
            $key = $item->segment['key'] ?? 'prospect';
            if (isset($segments[$key])) {
                $segments[$key]['count']++;
                $segments[$key]['spend'] += $item->monetary;
            }
        }

        foreach ($segments as $key => &$data) {
            $data['percentage'] = $total > 0 ? round(($data['count'] / $total) * 100, 1) : 0;
            $data['avg_spend'] = $data['count'] > 0 ? round($data['spend'] / $data['count'], 2) : 0;
        }

        return [
            'total_customers' => $total,
            'segments'        => $segments,
        ];
    }
}
