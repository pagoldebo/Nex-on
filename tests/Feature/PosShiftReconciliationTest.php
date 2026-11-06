<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PosShift;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosShiftReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashier = User::factory()->create([
            'email' => 'cashier.pos@nexon.com',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    public function test_pos_shifts_index_loads_successfully()
    {
        $response = $this->actingAs($this->cashier)->get(route('admin.pos.shifts.index'));

        $response->assertStatus(200);
        $response->assertSee('POS Cashier Shifts &amp; Drawer Reconciliation', false);
    }

    public function test_cashier_can_open_shift_with_float()
    {
        $response = $this->actingAs($this->cashier)->post(route('admin.pos.shifts.open'), [
            'opening_float' => 1500.00,
            'notes'         => 'Front desk main terminal #1',
        ]);

        $response->assertRedirect(route('admin.pos.shifts.index'));

        $this->assertDatabaseHas('pos_shifts', [
            'cashier_id'    => $this->cashier->id,
            'status'        => 'open',
            'opening_float' => 1500.00,
            'expected_cash' => 1500.00,
        ]);

        $shift = PosShift::where('cashier_id', $this->cashier->id)->first();
        $this->assertStringStartsWith('SHIFT-', $shift->shift_code);
    }

    public function test_shift_recomputes_live_sales_and_movements()
    {
        $shift = PosShift::create([
            'cashier_id'    => $this->cashier->id,
            'opened_at'     => Carbon::now()->subHours(2),
            'status'        => 'open',
            'opening_float' => 1000.00,
            'expected_cash' => 1000.00,
        ]);

        // Place a POS Cash order during shift
        Order::create([
            'user_id'          => null,
            'pos_cashier_id'   => $this->cashier->id,
            'reference'        => 'POS-20261029-0001',
            'channel'          => 'pos',
            'status'           => 'delivered',
            'subtotal'         => 3500,
            'shipping_charge'  => 0,
            'cod_charge'       => 0,
            'discount'         => 0,
            'total'            => 3500,
            'payment_method'   => 'cash',
            'payment_status'   => 'paid',
            'shipping_name'    => 'Walk-in Shopper',
            'shipping_phone'   => '9999999999',
            'shipping_email'   => 'walkin@nexon.store',
            'shipping_address1'=> 'Counter',
            'shipping_city'    => 'Store',
            'shipping_state'   => 'Karnataka',
            'shipping_pincode' => '560001',
            'created_at'       => Carbon::now()->subHour(),
        ]);

        // Place a POS Card order during shift
        Order::create([
            'user_id'          => null,
            'pos_cashier_id'   => $this->cashier->id,
            'reference'        => 'POS-20261029-0002',
            'channel'          => 'pos',
            'status'           => 'delivered',
            'subtotal'         => 2000,
            'shipping_charge'  => 0,
            'cod_charge'       => 0,
            'discount'         => 0,
            'total'            => 2000,
            'payment_method'   => 'card',
            'payment_status'   => 'paid',
            'shipping_name'    => 'Walk-in Shopper 2',
            'shipping_phone'   => '9999999999',
            'shipping_email'   => 'walkin2@nexon.store',
            'shipping_address1'=> 'Counter',
            'shipping_city'    => 'Store',
            'shipping_state'   => 'Karnataka',
            'shipping_pincode' => '560001',
            'created_at'       => Carbon::now()->subMinutes(30),
        ]);

        $shift->recomputeLiveMetrics();

        $this->assertEquals(3500.0, (float) $shift->cash_sales);
        $this->assertEquals(2000.0, (float) $shift->card_sales);
        $this->assertEquals(5500.0, (float) $shift->total_sales);
        $this->assertEquals(2, $shift->total_orders_count);

        // Expected cash in drawer = 1000 float + 3500 cash sales = 4500
        $this->assertEquals(4500.0, (float) $shift->expected_cash);
    }

    public function test_cashier_can_record_cash_in_and_cash_out()
    {
        $shift = PosShift::create([
            'cashier_id'    => $this->cashier->id,
            'opened_at'     => now(),
            'status'        => 'open',
            'opening_float' => 1000.00,
            'expected_cash' => 1000.00,
        ]);

        // Cash In
        $response = $this->actingAs($this->cashier)->post(route('admin.pos.shifts.movement', $shift), [
            'type'   => 'cash_in',
            'amount' => 500.00,
            'reason' => 'Additional small coin change',
        ]);
        $response->assertRedirect(route('admin.pos.shifts.index'));

        // Cash Out
        $this->actingAs($this->cashier)->post(route('admin.pos.shifts.movement', $shift), [
            'type'   => 'cash_out',
            'amount' => 200.00,
            'reason' => 'Store tea & snacks for staff',
        ]);

        $shift->refresh();
        $this->assertCount(2, $shift->movements);
        // Expected cash = 1000 + 500 - 200 = 1300
        $this->assertEquals(1300.0, (float) $shift->expected_cash);
    }

    public function test_cashier_can_close_shift_and_generate_z_report_variance()
    {
        $shift = PosShift::create([
            'cashier_id'    => $this->cashier->id,
            'opened_at'     => now()->subHours(8),
            'status'        => 'open',
            'opening_float' => 1000.00,
            'expected_cash' => 1000.00,
        ]);

        // Close with counted cash 980 (Short by 20)
        $response = $this->actingAs($this->cashier)->post(route('admin.pos.shifts.close', $shift), [
            'counted_cash' => 980.00,
            'notes'        => 'Minor change discrepancy at end of day',
        ]);

        $response->assertRedirect(route('admin.pos.shifts.index'));

        $shift->refresh();
        $this->assertEquals('closed', $shift->status);
        $this->assertNotNull($shift->closed_at);
        $this->assertEquals(980.00, (float) $shift->counted_cash);
        $this->assertEquals(-20.00, (float) $shift->cash_difference);
    }

    public function test_print_x_and_z_report_thermal_slips()
    {
        $shift = PosShift::create([
            'cashier_id'    => $this->cashier->id,
            'opened_at'     => now()->subHours(4),
            'status'        => 'open',
            'opening_float' => 2000.00,
            'expected_cash' => 2000.00,
        ]);

        // X-Report
        $xResponse = $this->actingAs($this->cashier)->get(route('admin.pos.shifts.print', ['shift' => $shift, 'type' => 'x']));
        $xResponse->assertStatus(200);
        $xResponse->assertSee('X-REPORT (MID-SHIFT AUDIT)');
        $xResponse->assertSee($shift->shift_code);
        $xResponse->assertSee('2,000.00');

        // Z-Report
        $shift->closeShift(2000.00);
        $zResponse = $this->actingAs($this->cashier)->get(route('admin.pos.shifts.print', ['shift' => $shift, 'type' => 'z']));
        $zResponse->assertStatus(200);
        $zResponse->assertSee('Z-REPORT (SHIFT CLOSE RECONCILIATION)');
        $zResponse->assertSee('VARIANCE (OVER/SHORT)');
    }
}

