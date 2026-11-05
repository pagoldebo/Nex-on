<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinanceExpensesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.finance@nexon.luxury',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $this->category = ExpenseCategory::create([
            'name'        => 'Packaging & Luxury Boxes',
            'slug'        => 'packaging-luxury-boxes',
            'color'       => '#10b981',
            'description' => 'Branded luxury ribbon and boxes',
            'is_active'   => true,
        ]);
    }

    public function test_expenses_index_loads_successfully(): void
    {
        Expense::create([
            'expense_category_id' => $this->category->id,
            'title'               => 'Showroom Lease Payment',
            'amount'              => 85000,
            'tax_amount'          => 15300,
            'payment_method'      => 'bank_transfer',
            'expense_date'        => now()->format('Y-m-d'),
            'vendor_name'         => 'Bandra Properties Pvt Ltd',
            'vendor_gstin'        => '27AABCP1234F1Z9',
            'created_by'          => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.finance.expenses'));

        $response->assertStatus(200);
        $response->assertSee('Operating Expenses &amp; Outflows', false);
        $response->assertSee('Showroom Lease Payment');
        $response->assertSee('85,000');
    }

    public function test_admin_can_log_new_expense_with_receipt(): void
    {
        Storage::fake('public');

        $receipt = UploadedFile::fake()->create('october_rent_invoice.pdf', 500, 'application/pdf');

        $payload = [
            'expense_category_id' => $this->category->id,
            'title'               => 'Festive Shopping Bags & Ribbon',
            'amount'              => 24500,
            'tax_amount'          => 4410,
            'payment_method'      => 'upi',
            'expense_date'        => now()->format('Y-m-d'),
            'vendor_name'         => 'Classic Packaging Co',
            'vendor_gstin'        => '27AAACP9988D1Z2',
            'reference_no'        => 'INV-PKG-9021',
            'receipt'             => $receipt,
            'is_recurring'        => true,
            'recurring_frequency' => 'monthly',
            'notes'               => 'Monthly delivery of 500 branded gift boxes',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.finance.expenses.store'), $payload);

        $response->assertRedirect(route('admin.finance.expenses'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'title'               => 'Festive Shopping Bags & Ribbon',
            'amount'              => 24500,
            'tax_amount'          => 4410,
            'is_recurring'        => true,
            'recurring_frequency' => 'monthly',
        ]);

        $created = Expense::where('title', 'Festive Shopping Bags & Ribbon')->first();
        $this->assertNotNull($created->receipt_path);
        Storage::disk('public')->assertExists($created->receipt_path);
    }

    public function test_admin_can_create_custom_expense_category(): void
    {
        $payload = [
            'name'        => 'Influencer PR & Gifting',
            'color'       => '#f43f5e',
            'description' => 'Celebrity gifting and PR styling packages',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.finance.expenses.categories.store'), $payload);

        $response->assertRedirect(route('admin.finance.expenses'));
        $this->assertDatabaseHas('expense_categories', [
            'name'  => 'Influencer PR & Gifting',
            'color' => '#f43f5e',
        ]);
    }

    public function test_admin_can_delete_expense(): void
    {
        Storage::fake('public');

        $expense = Expense::create([
            'expense_category_id' => $this->category->id,
            'title'               => 'Temporary Erroneous Entry',
            'amount'              => 1200,
            'payment_method'      => 'cash',
            'expense_date'        => now()->format('Y-m-d'),
            'created_by'          => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.finance.expenses.destroy', $expense));

        $response->assertRedirect(route('admin.finance.expenses'));
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}

