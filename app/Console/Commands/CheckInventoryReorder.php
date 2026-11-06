<?php

namespace App\Console\Commands;

use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckInventoryReorder extends Command
{
    protected $signature = 'inventory:check-reorder {--auto-po : Automatically generate draft Purchase Orders for low stock SKUs}';

    protected $description = 'Scan inventory for variants breaching reorder thresholds and draft supplier Purchase Orders';

    public function handle(): int
    {
        $this->info("Scanning inventory for safety stock breaches...");

        $breached = ProductVariant::with(['product', 'color', 'supplier'])
            ->whereRaw('stock <= COALESCE(reorder_threshold, 5)')
            ->orderBy('stock')
            ->get();

        if ($breached->isEmpty()) {
            $this->info("✓ All SKUs are within safe threshold boundaries. No replenishment needed.");
            return Command::SUCCESS;
        }

        $this->warn("⚠ Found {$breached->count()} SKU(s) breaching safety reorder thresholds:");

        $tableData = [];
        foreach ($breached as $v) {
            $tableData[] = [
                'SKU'           => $v->sku,
                'Product'       => $v->product->name . ' (' . ($v->color?->name ?? 'Std') . ' - ' . $v->size . ')',
                'Stock'         => $v->stock,
                'Threshold'     => $v->reorder_threshold ?? 5,
                'Suggested Qty' => $v->reorder_qty ?? 20,
                'Supplier'      => $v->supplier?->name ?? 'Default Supplier',
            ];
        }

        $this->table(['SKU', 'Product', 'Stock', 'Threshold', 'Suggested Qty', 'Supplier'], $tableData);

        if ($this->option('auto-po')) {
            $this->info("Drafting Purchase Orders...");
            $defaultSupplier = Supplier::firstOrCreate(
                ['code' => 'SUP-DEFAULT'],
                [
                    'name'           => 'Apex Textiles & Garments Ltd',
                    'contact_name'   => 'Rajesh Singhania',
                    'email'          => 'procurement@apextextiles.com',
                    'phone'          => '+91 98200 11223',
                    'lead_time_days' => 5,
                    'is_active'      => true,
                ]
            );

            $grouped = $breached->groupBy(fn ($v) => $v->supplier_id ?? $defaultSupplier->id);

            DB::transaction(function () use ($grouped, $defaultSupplier) {
                foreach ($grouped as $supplierId => $variants) {
                    $supplier = Supplier::find($supplierId) ?? $defaultSupplier;
                    $poNumber = PurchaseOrder::generatePoNumber() . '-' . rand(10, 99);

                    $po = PurchaseOrder::create([
                        'po_number'              => $poNumber,
                        'supplier_id'            => $supplier->id,
                        'status'                 => PurchaseOrder::STATUS_DRAFT,
                        'expected_delivery_date' => now()->addDays($supplier->lead_time_days ?? 7),
                        'notes'                  => 'Automated cron draft from scheduled threshold scan',
                    ]);

                    $total = 0;
                    foreach ($variants as $v) {
                        $qty = $v->reorder_qty > 0 ? $v->reorder_qty : 20;
                        $unitCost = (int) ($v->cost_price ?? round($v->price * 0.45));
                        $lineTotal = $qty * $unitCost;
                        $total += $lineTotal;

                        $po->items()->create([
                            'product_variant_id' => $v->id,
                            'ordered_qty'        => $qty,
                            'received_qty'       => 0,
                            'unit_cost'          => $unitCost,
                            'line_total'         => $lineTotal,
                        ]);
                    }

                    $po->total_amount = $total;
                    $po->save();

                    $this->info("✓ Generated Purchase Order: #{$po->po_number} for {$supplier->name} (Total: ₹" . number_format($total, 0) . ")");
                }
            });
        } else {
            $this->comment("Run with --auto-po flag or click 'Auto-Generate PO' in Admin Panel to automatically draft POs.");
        }

        return Command::SUCCESS;
    }
}
