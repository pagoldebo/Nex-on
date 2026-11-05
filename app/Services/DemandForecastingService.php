<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DemandForecastingService
{
    /**
     * Analyze velocity and forecasting for all or filtered variants.
     */
    public function analyze(array $filters = []): array
    {
        $now = Carbon::now();
        $date7d  = $now->copy()->subDays(7);
        $date30d = $now->copy()->subDays(30);
        $date90d = $now->copy()->subDays(90);

        // Valid non-cancelled order IDs across the 90-day window
        $validOrders90d = Order::where('created_at', '>=', $date90d)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->pluck('id');

        // Retrieve sales grouped by product_variant_id and time windows
        $salesRaw = OrderItem::whereIn('order_id', $validOrders90d)
            ->whereNotNull('product_variant_id')
            ->select(
                'product_variant_id',
                'created_at',
                'qty'
            )
            ->get();

        $salesByVariant = [];
        foreach ($salesRaw as $item) {
            $vid = $item->product_variant_id;
            if (!isset($salesByVariant[$vid])) {
                $salesByVariant[$vid] = [
                    'qty_7d'  => 0,
                    'qty_30d' => 0,
                    'qty_90d' => 0,
                ];
            }

            $createdAt = Carbon::parse($item->created_at);
            $qty = (int) $item->qty;

            $salesByVariant[$vid]['qty_90d'] += $qty;
            if ($createdAt->gte($date30d)) {
                $salesByVariant[$vid]['qty_30d'] += $qty;
            }
            if ($createdAt->gte($date7d)) {
                $salesByVariant[$vid]['qty_7d'] += $qty;
            }
        }

        // Query product variants with relations
        $query = ProductVariant::with(['product.category', 'color', 'supplier']);

        if (!empty($filters['category_id'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        $variants = $query->get();

        $criticalCount   = 0;
        $warningCount    = 0;
        $healthyCount    = 0;
        $overstockCount  = 0;
        $deadStockCount  = 0;
        $deadStockCapital= 0.0;
        $totalTiedUpCapital = 0.0;
        $totalDailyVelocity = 0.0;

        $analyzedItems = [];

        foreach ($variants as $variant) {
            $vid = $variant->id;
            $stock = max(0, (int) $variant->stock);
            $cost = (float) ($variant->cost_price ?: ($variant->price * 0.5));
            $retail = (float) $variant->price;
            $capital = $stock * $cost;
            $totalTiedUpCapital += $capital;

            $s = $salesByVariant[$vid] ?? ['qty_7d' => 0, 'qty_30d' => 0, 'qty_90d' => 0];
            $sold7d  = $s['qty_7d'];
            $sold30d = $s['qty_30d'];
            $sold90d = $s['qty_90d'];

            // Weighted Velocity (Units/day): 50% 30-day + 30% 7-day + 20% 90-day
            $v7  = $sold7d / 7.0;
            $v30 = $sold30d / 30.0;
            $v90 = $sold90d / 90.0;

            $velocity = ($v30 > 0 || $v7 > 0 || $v90 > 0)
                ? round(($v30 * 0.5) + ($v7 * 0.3) + ($v90 * 0.2), 2)
                : 0.0;

            $totalDailyVelocity += $velocity;

            // Days until stockout
            if ($velocity > 0) {
                $daysLeft = (int) round($stock / $velocity);
            } else {
                $daysLeft = $stock > 0 ? 999 : 0;
            }

            // Lead time from supplier or standard 14 days
            $leadTime = (int) ($variant->supplier->lead_time_days ?? 14);
            $bufferDays = 30; // 30-day safety stock buffer

            // Recommended Reorder Qty
            $targetStock = (int) ceil($velocity * ($leadTime + $bufferDays));
            $recommendedReorder = max(0, $targetStock - $stock);

            // Risk categorization
            $isDeadStock = false;
            if ($stock > 0 && $sold30d === 0 && $sold90d === 0) {
                $risk = 'dead_stock';
                $isDeadStock = true;
                $deadStockCount++;
                $deadStockCapital += $capital;
            } elseif ($stock === 0) {
                $risk = 'out_of_stock';
                $criticalCount++;
            } elseif ($daysLeft <= 7) {
                $risk = 'critical';
                $criticalCount++;
            } elseif ($daysLeft <= 21) {
                $risk = 'warning';
                $warningCount++;
            } elseif ($daysLeft <= 60) {
                $risk = 'healthy';
                $healthyCount++;
            } else {
                $risk = 'overstocked';
                $overstockCount++;
            }

            $item = [
                'variant_id'          => $variant->id,
                'sku'                 => $variant->sku,
                'product_name'        => $variant->product->name ?? 'N/A',
                'category_name'       => $variant->product->category->name ?? 'Uncategorized',
                'color_name'          => $variant->color->name ?? '',
                'size'                => $variant->size,
                'supplier_name'       => $variant->supplier->name ?? 'Standard Supplier',
                'lead_time_days'      => $leadTime,
                'stock'               => $stock,
                'cost_price'          => $cost,
                'retail_price'        => $retail,
                'capital_tied_up'     => $capital,
                'units_sold_7d'       => $sold7d,
                'units_sold_30d'      => $sold30d,
                'units_sold_90d'      => $sold90d,
                'velocity_daily'      => $velocity,
                'days_left'           => $daysLeft,
                'risk'                => $risk,
                'is_dead_stock'       => $isDeadStock,
                'recommended_reorder' => $recommendedReorder,
            ];

            // Apply risk filter if requested
            if (empty($filters['risk']) || $filters['risk'] === 'all' || $filters['risk'] === $risk) {
                $analyzedItems[] = $item;
            }
        }

        // Sort items
        $sort = $filters['sort'] ?? 'risk_asc';
        $collection = collect($analyzedItems);

        $sortedItems = match ($sort) {
            'velocity_desc' => $collection->sortByDesc('velocity_daily')->values(),
            'stock_asc'     => $collection->sortBy('stock')->values(),
            'days_asc'      => $collection->sortBy('days_left')->values(),
            'capital_desc'  => $collection->sortByDesc('capital_tied_up')->values(),
            'reorder_desc'  => $collection->sortByDesc('recommended_reorder')->values(),
            default         => $collection->sortBy(function ($item) {
                $prio = ['out_of_stock' => 1, 'critical' => 2, 'warning' => 3, 'dead_stock' => 4, 'overstocked' => 5, 'healthy' => 6];
                return $prio[$item['risk']] ?? 9;
            })->values(),
        };

        // Stock Turnover calculation: 30-day annualized sales / average capital
        $monthlySoldRevenue = collect($analyzedItems)->sum(fn($i) => $i['units_sold_30d'] * $i['cost_price']);
        $turnoverRatio = $totalTiedUpCapital > 0 ? round(($monthlySoldRevenue * 12) / $totalTiedUpCapital, 2) : 0;

        return [
            'items'               => $sortedItems,
            'summary'             => [
                'total_skus'          => count($analyzedItems),
                'critical_count'      => $criticalCount,
                'warning_count'       => $warningCount,
                'healthy_count'       => $healthyCount,
                'overstock_count'     => $overstockCount,
                'dead_stock_count'    => $deadStockCount,
                'dead_stock_capital'  => $deadStockCapital,
                'total_tied_up'       => $totalTiedUpCapital,
                'total_daily_velocity'=> round($totalDailyVelocity, 1),
                'turnover_ratio'      => $turnoverRatio,
            ],
        ];
    }
}
