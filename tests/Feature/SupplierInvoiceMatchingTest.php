<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierInvoiceMatchingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Supplier $supplier;
    protected PurchaseOrder $purchaseOrder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.supplier.invoices@nexon.luxury',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $this->supplier = Supplier::create([
            'name'       => 'Varanasi Weavers Silk Guild',
            'code'       => 'VNS-WEAVERS',
            'email'      => 'info@varanasiweavers.in',
            'gst_number' => '09AAAFV1234A1Z5',
            'is_active'  => true,
        ]);

        $category = Category::create(['name' => 'Silk Saris', 'slug' => 'silk-saris']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Banarasi Katan Silk', 'slug' => 'banarasi-katan-silk']);
        $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Crimson', 'hex' => '#dc2626']);
        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'BAN-KAT-001',
            'size'             => 'Free Size',
            'price'            => 18000,
            'stock'            => 10,
        ]);

        $this->purchaseOrder = PurchaseOrder::create([
            'po_number'    => 'PO-2026-0001',
            'supplier_id'  => $this->supplier->id,
            'created_by'   => $this->admin->id,
            'status'       => 'received',
            'total_amount' => 50000,
        ]);

        $this->purchaseOrder->items()->create([
            'product_variant_id' => $variant->id,
            'ordered_qty'        => 5,
            'received_qty'       => 5,
            'unit_cost'          => 10000,
            'line_total'         => 50000,
        ]);
    }

    public function test_supplier_invoices_index_loads_successfully()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.supplier-invoices.index'));

        $response->assertStatus(200);
        $response->assertSee('Supplier Invoices &amp; 3-Way Matching', false);
        $response->assertSee('Varanasi Weavers Silk Guild');
    }

    public function test_admin_can_record_supplier_invoice_with_matched_status()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.supplier-invoices.store'), [
            'purchase_order_id' => $this->purchaseOrder->id,
            'supplier_id'       => $this->supplier->id,
            'invoice_number'    => 'VNS-INV-101',
            'invoice_date'      => now()->toDateString(),
            'due_date'          => now()->addDays(30)->toDateString(),
            'subtotal'          => 42372.88,
            'tax_amount'        => 7627.12,
            'total_amount'      => 50000.00, // Matches PO total exactly!
            'notes'             => 'Received with consignment slip',
        ]);

        $response->assertRedirect(route('admin.supplier-invoices.index'));
        $this->assertDatabaseHas('supplier_invoices', [
            'invoice_number'  => 'VNS-INV-101',
            'matching_status' => 'matched',
            'po_variance'     => 0.00,
            'payment_status'  => 'unpaid',
        ]);
    }

    public function test_admin_records_supplier_invoice_detects_price_variance()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.supplier-invoices.store'), [
            'purchase_order_id' => $this->purchaseOrder->id,
            'supplier_id'       => $this->supplier->id,
            'invoice_number'    => 'VNS-INV-102-VARIANCE',
            'invoice_date'      => now()->toDateString(),
            'due_date'          => now()->addDays(30)->toDateString(),
            'subtotal'          => 46000.00,
            'tax_amount'        => 8280.00,
            'total_amount'      => 54280.00, // PO total was 50000! Variance = +4280.00
            'notes'             => 'Higher unit price billed by vendor',
        ]);

        $response->assertRedirect(route('admin.supplier-invoices.index'));
        $this->assertDatabaseHas('supplier_invoices', [
            'invoice_number'  => 'VNS-INV-102-VARIANCE',
            'matching_status' => 'variance_detected',
            'po_variance'     => 4280.00,
        ]);
    }

    public function test_admin_can_record_payment_against_invoice()
    {
        $invoice = SupplierInvoice::create([
            'purchase_order_id' => $this->purchaseOrder->id,
            'supplier_id'       => $this->supplier->id,
            'invoice_number'    => 'VNS-INV-103',
            'invoice_date'      => now()->toDateString(),
            'due_date'          => now()->addDays(30)->toDateString(),
            'subtotal'          => 40000,
            'tax_amount'        => 0,
            'total_amount'      => 40000,
            'amount_paid'       => 0,
            'payment_status'    => 'unpaid',
        ]);

        // Partial payment
        $response = $this->actingAs($this->admin)->post(route('admin.supplier-invoices.payment', $invoice), [
            'amount'                => 15000.00,
            'payment_method'        => 'bank_transfer',
            'transaction_reference' => 'UTR-HDFC-9911',
            'paid_at'               => now()->toDateString(),
            'notes'                 => 'Advance tranche 1',
        ]);

        $response->assertRedirect(route('admin.supplier-invoices.index'));
        $invoice->refresh();
        $this->assertEquals(15000.00, (float) $invoice->amount_paid);
        $this->assertEquals('partial', $invoice->payment_status);

        // Full payment
        $this->actingAs($this->admin)->post(route('admin.supplier-invoices.payment', $invoice), [
            'amount'                => 25000.00,
            'payment_method'        => 'bank_transfer',
            'transaction_reference' => 'UTR-HDFC-9922',
            'paid_at'               => now()->toDateString(),
        ]);

        $invoice->refresh();
        $this->assertEquals(40000.00, (float) $invoice->amount_paid);
        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertCount(2, $invoice->payments);
    }

    public function test_admin_can_delete_supplier_invoice()
    {
        $invoice = SupplierInvoice::create([
            'purchase_order_id' => $this->purchaseOrder->id,
            'supplier_id'       => $this->supplier->id,
            'invoice_number'    => 'VNS-INV-TO-DELETE',
            'invoice_date'      => now()->toDateString(),
            'due_date'          => now()->addDays(30)->toDateString(),
            'subtotal'          => 1000,
            'tax_amount'        => 0,
            'total_amount'      => 1000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.supplier-invoices.destroy', $invoice));

        $response->assertRedirect(route('admin.supplier-invoices.index'));
        $this->assertDatabaseMissing('supplier_invoices', ['id' => $invoice->id]);
    }
}
