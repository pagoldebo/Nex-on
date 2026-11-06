<?php

namespace Tests\Feature;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmPayrollTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.payroll@nexon.com',
            'role'  => 'admin',
        ]);

        $this->staff = User::factory()->create([
            'name'  => 'Priya Sharma',
            'email' => 'priya.sharma@nexon.com',
            'role'  => 'vendor',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    public function test_payroll_overview_console_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.hrm.payroll.overview'));

        $response->assertStatus(200);
        $response->assertSee('Payroll Overview & Expenditure', false);
        $response->assertSee('Run Monthly Payroll');
    }

    public function test_payroll_run_wizard_view_loads_eligible_staff(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.hrm.payroll.run'));

        $response->assertStatus(200);
        $response->assertSee('Run Monthly Payroll Wizard');
        $response->assertSee('Priya Sharma');
    }

    public function test_process_monthly_payroll_run_computes_salaries_and_creates_payslips(): void
    {
        $month = date('Y-m');

        $payload = [
            'period_month' => $month,
            'title'        => "Monthly Staff Payroll - {$month}",
            'items'        => [
                [
                    'user_id'             => $this->staff->id,
                    'designation'         => 'Senior Retail Associate',
                    'department'          => 'Retail Store Sales',
                    'working_days'        => 30,
                    'present_days'        => 30,
                    'basic_salary'        => 30000,
                    'hra'                 => 10000,
                    'transport_allowance' => 2500,
                    'performance_bonus'   => 5000,
                    'tds_tax'             => 1500,
                    'bank_name'           => 'HDFC Bank',
                    'bank_account_no'     => '50100987654321',
                    'bank_ifsc'           => 'HDFC0000123',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.hrm.payroll.run.process'), $payload);

        $payroll = Payroll::where('period_month', $month)->first();
        $this->assertNotNull($payroll);
        $response->assertRedirect(route('admin.hrm.payroll.review', $payroll));

        // Verify Gross: 30000 + 10000 + 2500 + 5000 = 47500
        $this->assertEquals(47500.00, (float) $payroll->total_gross);

        // Verify Payslip Item
        $item = $payroll->items->first();
        $this->assertNotNull($item);
        $this->assertEquals('Senior Retail Associate', $item->designation);

        // Deductions: PF (12% of 30000 = 3600), ESI (gross > 21000 => 0), TDS (1500) => Total = 5100
        $this->assertEquals(3600.00, (float) $item->pf_deduction);
        $this->assertEquals(0.00, (float) $item->esi_deduction);
        $this->assertEquals(1500.00, (float) $item->tds_tax);
        $this->assertEquals(5100.00, (float) $item->total_deductions);

        // Net: 47500 - 5100 = 42400
        $this->assertEquals(42400.00, (float) $item->net_salary);
        $this->assertEquals(42400.00, (float) $payroll->total_net);
    }

    public function test_payroll_review_and_status_authorization_workflow(): void
    {
        $payroll = Payroll::create([
            'payroll_code'     => 'PAY-202610',
            'period_month'     => '2026-10',
            'title'            => 'October 2026 Payroll',
            'start_date'       => '2026-10-01',
            'end_date'         => '2026-10-31',
            'total_gross'      => 40000,
            'total_net'        => 35000,
            'total_deductions' => 5000,
            'employees_count'  => 1,
            'status'           => 'draft',
        ]);

        PayrollItem::create([
            'payroll_id'       => $payroll->id,
            'user_id'          => $this->staff->id,
            'payslip_number'   => 'SLIP-202610-0001',
            'designation'      => 'Store Stylist',
            'department'       => 'Retail Store Sales',
            'basic_salary'     => 30000,
            'gross_salary'     => 40000,
            'total_deductions' => 5000,
            'net_salary'       => 35000,
            'status'           => 'draft',
        ]);

        // Review View
        $response = $this->actingAs($this->admin)->get(route('admin.hrm.payroll.review', $payroll));
        $response->assertStatus(200);
        $response->assertSee('Payroll Review &amp; Anomaly Ledger', false);

        // Approve
        $approveResponse = $this->actingAs($this->admin)->post(route('admin.hrm.payroll.status', $payroll), [
            'status' => 'approved',
        ]);
        $approveResponse->assertRedirect();
        $this->assertEquals('approved', $payroll->fresh()->status);
        $this->assertEquals($this->admin->id, $payroll->fresh()->approved_by);

        // Disburse
        $disburseResponse = $this->actingAs($this->admin)->post(route('admin.hrm.payroll.status', $payroll), [
            'status' => 'disbursed',
        ]);
        $disburseResponse->assertRedirect();
        $this->assertEquals('disbursed', $payroll->fresh()->status);
        $this->assertEquals('paid', $payroll->items->first()->fresh()->status);
    }

    public function test_export_bank_neft_batch_file(): void
    {
        $payroll = Payroll::create([
            'payroll_code'     => 'PAY-202610',
            'period_month'     => '2026-10',
            'title'            => 'October 2026 Payroll',
            'start_date'       => '2026-10-01',
            'end_date'         => '2026-10-31',
            'total_gross'      => 40000,
            'total_net'        => 35000,
            'status'           => 'approved',
        ]);

        PayrollItem::create([
            'payroll_id'       => $payroll->id,
            'user_id'          => $this->staff->id,
            'payslip_number'   => 'SLIP-202610-0001',
            'bank_account_no'  => '50100123456789',
            'bank_ifsc'        => 'HDFC0000123',
            'basic_salary'     => 30000,
            'gross_salary'     => 40000,
            'total_deductions' => 5000,
            'net_salary'       => 35000,
            'status'           => 'approved',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.hrm.payroll.bank-file', $payroll));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('neft_batch_PAY-202610.csv', $response->headers->get('content-disposition'));
    }

    public function test_payslips_directory_and_individual_print_voucher(): void
    {
        $payroll = Payroll::create([
            'payroll_code' => 'PAY-202610',
            'period_month' => '2026-10',
            'title'        => 'October 2026 Payroll',
            'start_date'   => '2026-10-01',
            'end_date'     => '2026-10-31',
            'total_gross'  => 40000,
            'total_net'    => 35000,
            'status'       => 'disbursed',
        ]);

        $item = PayrollItem::create([
            'payroll_id'       => $payroll->id,
            'user_id'          => $this->staff->id,
            'payslip_number'   => 'SLIP-202610-0001',
            'designation'      => 'Store Stylist',
            'department'       => 'Retail Store Sales',
            'basic_salary'     => 30000,
            'gross_salary'     => 40000,
            'total_deductions' => 5000,
            'net_salary'       => 35000,
            'status'           => 'paid',
        ]);

        // Directory
        $dirResponse = $this->actingAs($this->admin)->get(route('admin.hrm.payroll.payslips'));
        $dirResponse->assertStatus(200);
        $dirResponse->assertSee('SLIP-202610-0001');

        // Printable Voucher
        $printResponse = $this->actingAs($this->admin)->get(route('admin.hrm.payroll.payslip.print', $item));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Salary Payslip');
        $printResponse->assertSee('SLIP-202610-0001');
        $printResponse->assertSee('Priya Sharma');
    }
}
