<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\ProductImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Upload Security Admin',
            'email'    => 'admin@upload-security.test',
            'phone'    => '9888877777',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);
    }

    /**
     * Test Category creation accepts valid image uploads (PNG/JPEG/WebP).
     */
    public function test_category_upload_accepts_valid_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('category-valid.png', 200, 200);

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name'  => 'Summer Lingerie',
            'image' => $file,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $category = Category::where('name', 'Summer Lingerie')->first();
        $this->assertNotNull($category);
        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);
    }

    /**
     * Test Category creation rejects executable files disguised as images.
     */
    public function test_category_upload_rejects_php_script(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('exploit.php', 10, 'application/x-php');

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name'  => 'Malicious Category',
            'image' => $file,
        ]);

        $response->assertSessionHasErrors(['image']);
        $this->assertDatabaseMissing('categories', ['name' => 'Malicious Category']);
    }

    /**
     * Test Category SVG upload sanitizes embedded scripts and XXE doctypes.
     */
    public function test_category_svg_upload_is_sanitized(): void
    {
        Storage::fake('public');

        $maliciousSvg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE svg [ <!ENTITY xxe "malicious_entity"> ]>
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" onload="alert('XSS')">
    <script>alert(document.cookie);</script>
    <foreignObject width="100" height="100">
        <body xmlns="http://www.w3.org/1999/xhtml"><script>alert(1)</script></body>
    </foreignObject>
    <circle cx="50" cy="50" r="40" stroke="green" stroke-width="4" fill="yellow" />
</svg>
SVG;

        $file = UploadedFile::fake()->createWithContent('vector.svg', $maliciousSvg);

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name'  => 'Vector Category',
            'image' => $file,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $category = Category::where('name', 'Vector Category')->first();
        $this->assertNotNull($category);
        $this->assertNotNull($category->image);

        $savedContent = Storage::disk('public')->get($category->image);
        $this->assertStringNotContainsString('<script', $savedContent);
        $this->assertStringNotContainsString('onload=', $savedContent);
        $this->assertStringNotContainsString('<!DOCTYPE', $savedContent);
        $this->assertStringNotContainsString('<!ENTITY', $savedContent);
        $this->assertStringContainsString('<circle', $savedContent);
    }

    /**
     * Test Product images reject non-raster formats (e.g. SVGs, PHP scripts).
     */
    public function test_product_images_reject_svg_and_scripts(): void
    {
        Storage::fake('public');

        $category = Category::create(['name' => 'Bras', 'slug' => 'bras']);

        $svgFile = UploadedFile::fake()->createWithContent('photo.svg', '<svg></svg>');

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name'        => 'Silk Lace Bralette',
            'category_id' => $category->id,
            'colors'      => [
                [
                    'name'   => 'Midnight Black',
                    'hex'    => '#000000',
                    'images' => [$svgFile],
                    'sizes'  => [
                        [
                            'size'  => 'S',
                            'price' => 1299,
                            'stock' => 10,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['colors.0.images.0']);
    }

    /**
     * Test ProductImageService rejects invalid or un-decodable bytes.
     */
    public function test_product_image_service_rejects_corrupted_bytes(): void
    {
        Storage::fake('public');

        $this->expectException(\InvalidArgumentException::class);

        ProductImageService::saveRawAsWebp('INVALID_NON_IMAGE_RAW_BINARY_DATA', 'products/test/image.webp', false);
    }

    /**
     * Test AdminSettingController ignores unwhitelisted file keys, preventing arbitrary file upload.
     */
    public function test_settings_update_ignores_unwhitelisted_file_keys(): void
    {
        Storage::fake('public');

        $maliciousFile = UploadedFile::fake()->create('payload.php', 20, 'application/x-php');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name'      => 'Nexon Atelier Secure',
            'unauthorized_key' => $maliciousFile,
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        // Setting must not be saved or stored
        $this->assertNull(Setting::get('unauthorized_key'));
        $this->assertEquals('Nexon Atelier Secure', Setting::get('store_name'));
    }

    /**
     * Test AdminSettingController prevents arbitrary setting wipe via unwhitelisted remove_* parameters.
     */
    public function test_settings_update_prevents_arbitrary_setting_wipe(): void
    {
        Setting::set('store_phone', '+91 9999988888');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'store_name'         => 'Nexon Atelier',
            'remove_store_phone' => '1',
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        // 'store_phone' must not be wiped out because it is not an allowed image setting
        $this->assertEquals('+91 9999988888', Setting::get('store_phone'));
    }

    /**
     * Test AdminExpenseController allows valid PDF receipts and rejects PHP/executable files.
     */
    public function test_expense_receipt_allows_pdf_and_rejects_php(): void
    {
        Storage::fake('public');

        $category = \App\Models\ExpenseCategory::create([
            'name'      => 'Packaging',
            'is_active' => true,
        ]);

        // 1. Valid PDF receipt
        $pdfReceipt = UploadedFile::fake()->create('bill.pdf', 100, 'application/pdf');

        $responsePdf = $this->actingAs($this->admin)->post(route('admin.finance.expenses.store'), [
            'expense_category_id' => $category->id,
            'title'               => 'Custom Box Orders',
            'amount'              => 5000,
            'payment_method'      => 'bank_transfer',
            'expense_date'        => now()->toDateString(),
            'receipt'             => $pdfReceipt,
        ]);

        $responsePdf->assertRedirect(route('admin.finance.expenses'));
        $responsePdf->assertSessionHas('success');

        $expense = \App\Models\Expense::where('title', 'Custom Box Orders')->first();
        $this->assertNotNull($expense);
        $this->assertNotNull($expense->receipt_path);
        Storage::disk('public')->assertExists($expense->receipt_path);

        // 2. Reject executable script
        $phpReceipt = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');

        $responsePhp = $this->actingAs($this->admin)->post(route('admin.finance.expenses.store'), [
            'expense_category_id' => $category->id,
            'title'               => 'Fake Expense',
            'amount'              => 100,
            'payment_method'      => 'cash',
            'expense_date'        => now()->toDateString(),
            'receipt'             => $phpReceipt,
        ]);

        $responsePhp->assertSessionHasErrors(['receipt']);
    }
}

