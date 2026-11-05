<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\OrderItemBatch;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class BatchInventoryService
{
    /**
     * Allocate stock from active batches using First-In, First-Out (FIFO) logic.
     */
    public function allocateFifo(OrderItem $orderItem): array
    {
        if (!$orderItem->product_variant_id) {
            return [];
        }

        $needed = (int) $orderItem->qty;
        if ($needed <= 0) {
            return [];
        }

        // Active batches ordered by earliest expiration date first
        $batches = ProductBatch::where('product_variant_id', $orderItem->product_variant_id)
            ->where('status', 'active')
            ->where('remaining_qty', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->lockForUpdate()
            ->get();

        $allocations = [];

        foreach ($batches as $batch) {
            if ($needed <= 0) {
                break;
            }

            $available = (int) $batch->remaining_qty;
            $take = min($needed, $available);

            $batch->remaining_qty -= $take;
            if ($batch->remaining_qty === 0) {
                $batch->status = 'depleted';
            }
            $batch->save();

            $allocation = OrderItemBatch::create([
                'order_item_id'    => $orderItem->id,
                'product_batch_id' => $batch->id,
                'qty_allocated'    => $take,
            ]);

            $allocations[] = $allocation;
            $needed -= $take;
        }

        return $allocations;
    }

    /**
     * Create a new product batch and optionally increment variant stock.
     */
    public function createBatch(int $variantId, array $data): ProductBatch
    {
        return DB::transaction(function () use ($variantId, $data) {
            $variant = ProductVariant::findOrFail($variantId);

            $qty = (int) ($data['initial_qty'] ?? 0);
            $batch = ProductBatch::create([
                'product_variant_id' => $variant->id,
                'batch_number'       => $data['batch_number'],
                'lot_number'         => $data['lot_number'] ?? null,
                'mfg_date'           => $data['mfg_date'] ?? null,
                'expiry_date'        => $data['expiry_date'],
                'initial_qty'        => $qty,
                'remaining_qty'      => $qty,
                'cost_price'         => $data['cost_price'] ?? $variant->cost_price,
                'status'             => 'active',
                'notes'              => $data['notes'] ?? null,
            ]);

            if (!empty($data['sync_variant_stock'])) {
                $variant->increment('stock', $qty);
            }

            return $batch;
        });
    }

    /**
     * Trace all orders and customers who received goods from a specific batch for recall audit.
     */
    public function traceBatch(int $batchId): array
    {
        $batch = ProductBatch::with([
            'variant.product.category',
            'variant.color',
            'allocations.orderItem.order'
        ])->findOrFail($batchId);

        $customerDispatches = [];
        $totalUnitsRecalled = 0;

        foreach ($batch->allocations as $alloc) {
            $orderItem = $alloc->orderItem;
            $order = $orderItem?->order;

            if ($order) {
                $customerDispatches[] = [
                    'order_id'        => $order->id,
                    'order_reference' => $order->reference,
                    'order_date'      => $order->created_at->format('Y-m-d H:i'),
                    'customer_name'   => $order->shipping_name,
                    'customer_phone'  => $order->shipping_phone,
                    'customer_email'  => $order->shipping_email,
                    'shipping_city'   => $order->shipping_city,
                    'shipping_state'  => $order->shipping_state,
                    'order_status'    => $order->status,
                    'qty_allocated'   => $alloc->qty_allocated,
                ];
                $totalUnitsRecalled += $alloc->qty_allocated;
            }
        }

        return [
            'batch'               => $batch,
            'customer_dispatches' => $customerDispatches,
            'total_dispatched'    => $totalUnitsRecalled,
        ];
    }
}
