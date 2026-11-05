<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Outlet;
use App\Models\OutletStock;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OutletInventoryService
{
    /**
     * Get stock for variant at specific outlet.
     */
    public function getStock(Outlet $outlet, ProductVariant $variant): int
    {
        $record = OutletStock::where('outlet_id', $outlet->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        return $record ? $record->stock : 0;
    }

    /**
     * Set or increment stock at an outlet.
     */
    public function adjustStock(Outlet $outlet, ProductVariant $variant, int $delta, ?string $binLocation = null): OutletStock
    {
        return DB::transaction(function () use ($outlet, $variant, $delta, $binLocation) {
            $record = OutletStock::firstOrNew([
                'outlet_id'          => $outlet->id,
                'product_variant_id' => $variant->id,
            ]);

            $newStock = max(0, ($record->stock ?? 0) + $delta);
            $record->stock = $newStock;

            if ($binLocation !== null) {
                $record->shelf_bin_location = $binLocation;
            }

            $record->save();

            return $record;
        });
    }

    /**
     * Create a new draft or pending stock transfer.
     */
    public function createTransfer(Outlet $from, Outlet $to, array $items, ?string $notes = null, ?User $user = null): StockTransfer
    {
        if ($from->id === $to->id) {
            throw new InvalidArgumentException("Source and destination outlet cannot be identical.");
        }

        return DB::transaction(function () use ($from, $to, $items, $notes, $user) {
            $transfer = StockTransfer::create([
                'transfer_no'    => StockTransfer::generateTransferNo(),
                'from_outlet_id' => $from->id,
                'to_outlet_id'   => $to->id,
                'status'         => 'draft',
                'notes'          => $notes,
                'dispatched_by'  => $user?->id,
            ]);

            foreach ($items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id'  => $transfer->id,
                    'product_variant_id' => $item['variant_id'],
                    'qty_sent'           => (int) $item['qty'],
                    'qty_received'       => 0,
                ]);
            }

            return $transfer->load('items.variant.product', 'fromOutlet', 'toOutlet');
        });
    }

    /**
     * Dispatch stock transfer: deducts stock from origin outlet and marks dispatched.
     */
    public function dispatchTransfer(StockTransfer $transfer, ?User $user = null): StockTransfer
    {
        if (!in_array($transfer->status, ['draft', 'pending'])) {
            throw new InvalidArgumentException("Only draft transfers can be dispatched.");
        }

        return DB::transaction(function () use ($transfer, $user) {
            foreach ($transfer->items as $item) {
                $originStock = $this->getStock($transfer->fromOutlet, $item->variant);
                if ($originStock < $item->qty_sent) {
                    throw new InvalidArgumentException("Insufficient stock at {$transfer->fromOutlet->name} for SKU: {$item->variant->sku}. Required: {$item->qty_sent}, Available: {$originStock}");
                }

                // Deduct from source outlet
                $this->adjustStock($transfer->fromOutlet, $item->variant, -$item->qty_sent);

                // Audit log
                InventoryLog::create([
                    'product_variant_id' => $item->product_variant_id,
                    'sku'                => $item->variant->sku,
                    'change_qty'         => -$item->qty_sent,
                    'new_stock'          => $this->getStock($transfer->fromOutlet, $item->variant),
                    'type'               => 'manual_adjustment',
                    'notes'              => "Dispatched on Transfer #{$transfer->transfer_no} to {$transfer->toOutlet->name}",
                    'created_by'         => $user?->id,
                ]);
            }

            $transfer->update([
                'status'        => 'dispatched',
                'dispatched_by' => $user?->id ?? $transfer->dispatched_by,
                'dispatched_at' => now(),
            ]);

            return $transfer;
        });
    }

    /**
     * Receive stock transfer: increments stock in destination outlet and records received quantities.
     */
    public function receiveTransfer(StockTransfer $transfer, array $receivedMap = [], ?User $user = null): StockTransfer
    {
        if (!in_array($transfer->status, ['dispatched', 'in_transit'])) {
            throw new InvalidArgumentException("Transfer must be dispatched before it can be received.");
        }

        return DB::transaction(function () use ($transfer, $receivedMap, $user) {
            foreach ($transfer->items as $item) {
                $qtyReceived = isset($receivedMap[$item->id]) 
                    ? (int) $receivedMap[$item->id] 
                    : $item->qty_sent;

                $item->update(['qty_received' => $qtyReceived]);

                // Increment destination outlet stock
                $this->adjustStock($transfer->toOutlet, $item->variant, $qtyReceived);

                // Audit log
                InventoryLog::create([
                    'product_variant_id' => $item->product_variant_id,
                    'sku'                => $item->variant->sku,
                    'change_qty'         => $qtyReceived,
                    'new_stock'          => $this->getStock($transfer->toOutlet, $item->variant),
                    'type'               => 'manual_adjustment',
                    'notes'              => "Received on Transfer #{$transfer->transfer_no} from {$transfer->fromOutlet->name}",
                    'created_by'         => $user?->id,
                ]);
            }

            $transfer->update([
                'status'      => 'received',
                'received_by' => $user?->id,
                'received_at' => now(),
            ]);

            return $transfer;
        });
    }
}
