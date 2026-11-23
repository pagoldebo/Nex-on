<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Services\HtmlSanitizerService;
use App\Services\ProductImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Class AdminSettingController
 *
 * Back-office controller for global store configuration, theme visuals, and security policies.
 * Dynamically persists key-value settings, validates webhooks against SSRF,
 * enforces strict image upload whitelisting (Domain 5), and rejects SVG files to prevent stored XSS (M-01).
 *
 * @package App\Http\Controllers\Admin
 */
class AdminSettingController extends Controller
{
    /**
     * Renders the master settings page with loaded templates and active settings.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $settings    = Setting::getAll();
        $categories  = Category::orderBy('name')->get();
        $activeTheme = \App\Models\Template::active();
        $templates   = \App\Models\Template::all();

        return view('admin.settings.index', compact('settings', 'categories', 'activeTheme', 'templates'));
    }

    /**
     * Validates and updates store settings, uploaded banner images, and webhook endpoints.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $allowedImageKeys = [
            'site_logo',
            'footer_logo',
            'vyra_hero_image',
            'buttercup_hero_image',
            'buttercup_slide_1_image',
            'buttercup_slide_2_image',
            'buttercup_slide_3_image',
            'buttercup_slide_4_image',
            'buttercup_slide_5_image',
            'lumiere_hero_image',
            'saaya_hero_image',
            'saaya_slide_1_image',
            'saaya_slide_2_image',
            'saaya_slide_3_image',
            'saaya_slide_4_image',
            'saaya_slide_5_image',
            'saaya_story_image',
            'vyra_story_image',
            'buttercup_story_image',
            'lumiere_story_image',
            'organic_hero_image',
            'organic_story_image',
            'organic_ad1_image',
            'organic_ad2_image',
            'organic_ad3_image',
            'hero_banner_image',
        ];

        $request->validate([
            'site_logo'               => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:5120',
            'footer_logo'             => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:5120',
            'vyra_hero_image'         => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif,mp4,webm,mov,ogg|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif,video/mp4,video/webm,video/quicktime,video/ogg|max:20480',
            'buttercup_hero_image'    => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'buttercup_slide_1_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'buttercup_slide_2_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'buttercup_slide_3_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'buttercup_slide_4_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'buttercup_slide_5_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'lumiere_hero_image'      => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'saaya_hero_image'        => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'saaya_slide_1_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'saaya_slide_2_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'saaya_slide_3_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'saaya_slide_4_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'saaya_slide_5_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'saaya_story_image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'vyra_story_image'        => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'buttercup_story_image'   => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'lumiere_story_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'organic_hero_image'      => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'organic_story_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'organic_ad1_image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'organic_ad2_image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'organic_ad3_image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'hero_banner_image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,avif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/avif|max:10240',
            'tax_percentage'          => 'nullable|numeric|min:0|max:100',
            'tax_name'                => 'nullable|string|max:50',
            'tax_number'              => 'nullable|string|max:100',
            'tax_number_label'        => 'nullable|string|max:100',
            'tax_type'                => 'nullable|string|in:inclusive,exclusive',
            'tax_country_preset'      => 'nullable|string|max:50',
        ]);

        // Validate webhook URLs against SSRF (mitigates internal network scanning & cloud metadata access)
        if ($request->filled('order_webhook_urls')) {
            $webhookUrls = array_filter(array_map('trim', explode(',', (string) $request->input('order_webhook_urls'))));
            foreach ($webhookUrls as $webhookUrl) {
                try {
                    \App\Services\SsrfProtectionService::validateUrl($webhookUrl);
                } catch (\InvalidArgumentException $e) {
                    return back()->withInput()->withErrors([
                        'order_webhook_urls' => "SSRF Security Validation Failed: {$e->getMessage()}",
                    ]);
                }
            }
        }

        // Process whitelisted image removals only (prevents arbitrary setting wipes)
        foreach ($request->all() as $inputKey => $value) {
            if (str_starts_with($inputKey, 'remove_') && ($value === '1' || $value === 1)) {
                $targetKey = substr($inputKey, 7);
                if ($targetKey === 'logo') {
                    $targetKey = 'site_logo';
                }
                if (in_array($targetKey, $allowedImageKeys, true)) {
                    $oldImg = Setting::get($targetKey);
                    if ($oldImg) {
                        $clean = ltrim($oldImg, '/');
                        if (str_starts_with($clean, 'images/')) {
                            $clean = substr($clean, 7);
                        }
                        if (Storage::disk('public')->exists($clean)) {
                            Storage::disk('public')->delete($clean);
                        }
                        $publicImagesDir = realpath(public_path('images'));
                        $publicFile = public_path('images/' . $clean);
                        $realFile = realpath($publicFile);
                        if ($realFile && $publicImagesDir && str_starts_with($realFile, $publicImagesDir) && !is_dir($realFile)) {
                            @unlink($realFile);
                        }
                    }
                    Setting::set($targetKey, '');
                    if ($targetKey === 'vyra_hero_image') {
                        Setting::set('vyra_hero_video', '');
                    }
                    if ($targetKey === 'saaya_hero_image') {
                        Setting::set('saaya_slide_1_image', '');
                    }
                    if ($targetKey === 'saaya_slide_1_image') {
                        Setting::set('saaya_hero_image', '');
                    }
                    if ($targetKey === 'buttercup_hero_image') {
                        Setting::set('buttercup_slide_1_image', '');
                    }
                    if ($targetKey === 'buttercup_slide_1_image') {
                        Setting::set('buttercup_hero_image', '');
                    }
                }
            }
        }

        // Process whitelisted file uploads only (prevents arbitrary file uploads)
        foreach ($allowedImageKeys as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $oldImg = Setting::get($fileKey);
                $uploadedFile = $request->file($fileKey);

                if ($fileKey === 'site_logo' || $fileKey === 'footer_logo') {
                    // Logo is stored as branding/logo.png or branding/footer_logo.png, converted to safe raster PNG (rejects SVG per M-01)
                    $targetFilename = $fileKey === 'site_logo' ? 'logo.png' : 'footer_logo.png';
                    $targetPath = 'branding/' . $targetFilename;

                    if ($oldImg && $oldImg !== $targetPath && Storage::disk('public')->exists($oldImg)) {
                        Storage::disk('public')->delete($oldImg);
                    }

                    $raw = file_get_contents($uploadedFile->getRealPath());
                    $saved = false;
                    if (function_exists('imagecreatefromstring')) {
                        $im = @imagecreatefromstring($raw);
                        if ($im !== false) {
                            imagepalettetotruecolor($im);
                            imagealphablending($im, false);
                            imagesavealpha($im, true);
                            ob_start();
                            imagepng($im);
                            $pngData = ob_get_clean();
                            imagedestroy($im);
                            if ($pngData !== false && strlen($pngData) > 0) {
                                Storage::disk('public')->put($targetPath, $pngData);
                                $saved = true;
                            }
                        }
                    }
                    if (!$saved) {
                        $uploadedFile->storeAs('branding', $targetFilename, 'public');
                    }
                    Setting::set($fileKey, $targetPath);
                } else {
                    if ($oldImg) {
                        $cleanOld = ltrim($oldImg, '/');
                        if (str_starts_with($cleanOld, 'images/')) {
                            $cleanOld = substr($cleanOld, 7);
                        }
                        if (Storage::disk('public')->exists($cleanOld)) {
                            Storage::disk('public')->delete($cleanOld);
                        }
                        $publicImagesDir = realpath(public_path('images'));
                        $publicOldFile = public_path('images/' . $cleanOld);
                        $realOldFile = realpath($publicOldFile);
                        if ($realOldFile && $publicImagesDir && str_starts_with($realOldFile, $publicImagesDir) && !is_dir($realOldFile)) {
                            @unlink($realOldFile);
                        }
                    }
                    $isSlider = str_starts_with($fileKey, 'vyra_hero')
                        || str_starts_with($fileKey, 'saaya_slide')
                        || str_starts_with($fileKey, 'buttercup_slide')
                        || $fileKey === 'saaya_hero_image'
                        || $fileKey === 'buttercup_hero_image';
                    $targetFolder = $isSlider ? 'slider' : 'banners';
                    $storedPath = ProductImageService::storeUpload($uploadedFile, $targetFolder);
                    Setting::set($fileKey, $storedPath);
                    if ($fileKey === 'saaya_slide_1_image') {
                        Setting::set('saaya_hero_image', $storedPath);
                    }
                    if ($fileKey === 'saaya_hero_image') {
                        Setting::set('saaya_slide_1_image', $storedPath);
                    }
                    if ($fileKey === 'buttercup_slide_1_image') {
                        Setting::set('buttercup_hero_image', $storedPath);
                    }
                    if ($fileKey === 'buttercup_hero_image') {
                        Setting::set('buttercup_slide_1_image', $storedPath);
                    }
                }
            }
        }

        $allFileKeys = array_keys($request->allFiles());
        $removeKeys  = array_filter(array_keys($request->all()), fn($k) => str_starts_with($k, 'remove_'));
        $data        = $request->except(array_merge(['_token', '_method'], $allFileKeys, $removeKeys));

        // Checkbox fallbacks for boolean toggles
        if (! isset($data['enable_cod'])) {
            $data['enable_cod'] = '0';
        }
        if (! isset($data['maintenance_mode'])) {
            $data['maintenance_mode'] = '0';
        }
        if (! isset($data['pwa_prompt_enabled'])) {
            $data['pwa_prompt_enabled'] = '0';
        }
        if (! isset($data['ticker_enabled'])) {
            $data['ticker_enabled'] = '0';
        }

        $richContentKeys = [
            'return_custom_content',
            'shipping_custom_content',
            'sizeguide_additional_info',
            'ticker_item_1',
            'ticker_item_2',
            'ticker_item_3',
            'ticker_item_4',
            'ticker_item_5',
            'product_delivery_info',
            'product_returns_info',
            'organic_delivery_info',
            'organic_returns_info',
        ];

        $blockedSettingKeys = [
            'gateway_razorpay_key_secret',
            'gateway_razorpay_webhook_secret',
            'gateway_stripe_secret_key',
            'gateway_stripe_webhook_secret',
            'gateway_phonepe_salt_key',
            'gateway_paypal_secret',
            'sms_twilio_token',
            'sms_msg91_authkey',
            'sms_fast2sms_api_key',
        ];

        $allowedPrefixes = [
            'vyra_', 'buttercup_', 'lumiere_', 'saaya_', 'organic_',
            'shipping_', 'return_', 'privacy_', 'terms_', 'about_', 'contact_',
            'sizeguide_', 'pwa_prompt_', 'hero_', 'footer_', 'ticker_', 'faq_',
            'tax_', 'currency_',
        ];

        $allowedSettingKeys = [
            'store_name', 'support_email', 'support_phone', 'support_whatsapp', 'store_address',
            'gstin', 'store_state', 'invoice_footer', 'free_shipping_threshold', 'shipping_cost',
            'enable_cod', 'cod_charge', 'tax_percentage', 'announcement_bar', 'ticker_enabled',
            'tax_enabled', 'tax_name', 'tax_number', 'tax_number_label', 'tax_type', 'tax_country_preset',
            'multi_currency_enabled', 'base_currency', 'enabled_currencies', 'currency_rates',
            'pwa_prompt_enabled', 'pwa_prompt_title', 'pwa_prompt_description', 'pwa_prompt_delay',
            'footer_bio', 'footer_badge1', 'footer_badge2',
            'product_delivery_info', 'product_returns_info', 'organic_delivery_info', 'organic_returns_info',
            'return_custom_content', 'shipping_custom_content',
            'order_webhook_urls', 'tally_sales_ledger',
        ];

        // Ensure tax_enabled checkbox state is handled
        if ($request->has('tax_section_submitted')) {
            $data['tax_enabled'] = $request->has('tax_enabled') ? '1' : '0';
        }
        if (isset($data['tax_number']) && !empty($data['tax_number'])) {
            $data['gstin'] = $data['tax_number'];
        }

        // Ensure multi_currency_enabled checkbox and array options are handled
        if ($request->has('multi_currency_section_submitted')) {
            $data['multi_currency_enabled'] = $request->has('multi_currency_enabled') ? '1' : '0';
            if ($request->has('base_currency')) {
                $data['base_currency'] = strtoupper((string) $request->input('base_currency', 'INR'));
            }
            if ($request->has('enabled_currencies') && is_array($request->input('enabled_currencies'))) {
                $data['enabled_currencies'] = json_encode(array_values(array_map('strtoupper', $request->input('enabled_currencies'))));
            }
            if ($request->has('currency_rates') && is_array($request->input('currency_rates'))) {
                $cleanedRates = [];
                foreach ($request->input('currency_rates') as $cur => $rate) {
                    $cleanedRates[strtoupper((string) $cur)] = (float) $rate;
                }
                $data['currency_rates'] = json_encode($cleanedRates);
            }
        }

        $isSuperAdmin = \Illuminate\Support\Facades\Auth::user()?->isSuperAdmin();

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                if (in_array($key, $blockedSettingKeys, true)) {
                    continue; // Gateway & SMS secrets must be configured via their dedicated admin controllers
                }

                // Check allowlist
                $isAllowed = in_array($key, $allowedSettingKeys, true);
                if (! $isAllowed) {
                    foreach ($allowedPrefixes as $prefix) {
                        if (str_starts_with($key, $prefix)) {
                            $isAllowed = true;
                            break;
                        }
                    }
                }

                if (! $isAllowed) {
                    continue; // Ignore un-whitelisted keys
                }

                // Guard outbound webhook exfiltration: only super-admin can change webhook endpoints
                if ($key === 'order_webhook_urls') {
                    if (! $isSuperAdmin) {
                        continue;
                    }
                    if (!empty($value)) {
                        $urls = array_map('trim', explode(',', (string) $value));
                        foreach ($urls as $u) {
                            if (!empty($u)) {
                                \App\Services\SsrfProtectionService::validateUrl($u);
                            }
                        }
                    }
                }

                if (in_array($key, $richContentKeys, true)) {
                    $value = \App\Services\HtmlSanitizerService::clean($value);
                }

                // Link settings must be stored relative (e.g. '/search'), never pinned to the
                // host the admin panel happened to be open on (e.g. 'https://nexon.test/search'),
                // otherwise the link keeps pointing at that host after the store moves domain.
                if (str_ends_with($key, '_url') && is_string($value)) {
                    $value = $this->relativizeInternalUrl($value);
                }

                Setting::set($key, $value);
            }
        }

        // Keep universal product accordion settings and legacy keys in sync
        if (isset($data['product_delivery_info'])) {
            Setting::set('organic_delivery_info', $data['product_delivery_info']);
        } elseif (isset($data['organic_delivery_info']) && !Setting::get('product_delivery_info')) {
            Setting::set('product_delivery_info', $data['organic_delivery_info']);
        }

        if (isset($data['product_returns_info'])) {
            Setting::set('organic_returns_info', $data['product_returns_info']);
        } elseif (isset($data['organic_returns_info']) && !Setting::get('product_returns_info')) {
            Setting::set('product_returns_info', $data['organic_returns_info']);
        }

        // Synchronize PWA Manifest & App Icons with uploaded branding logo
        \App\Services\PwaManifestService::sync();

        return redirect()->route('admin.settings.index')->with('success', 'Store settings, brand identity, and hero banners updated successfully.');
    }

    /**
     * Bulk adjust prices across an entire category or all products.
     * Adjust bulk prices.
     */
    public function adjustBulkPrices(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'      => 'nullable|exists:categories,id',
            'adjustment_type'  => 'required|in:increase_percent,decrease_percent,increase_fixed,decrease_fixed',
            'adjustment_value' => 'required|numeric|min:0.01',
        ]);

        $categoryId      = $validated['category_id'] ?? null;
        $adjustmentType  = $validated['adjustment_type'];
        $adjustmentValue = (float) $validated['adjustment_value'];

        $productQuery = Product::query();
        if ($categoryId) {
            $productQuery->where('category_id', $categoryId);
            $cat = Category::find($categoryId);
            $scopeName = "category '{$cat->name}'";
        } else {
            $scopeName = 'all catalog products';
        }

        $productIds = $productQuery->pluck('id');

        if ($productIds->isEmpty()) {
            return back()->with('error', 'No products found matching the selected scope.');
        }

        $variants = ProductVariant::whereIn('product_id', $productIds)->get();
        if ($variants->isEmpty()) {
            return back()->with('error', 'No product variants found to update.');
        }

        DB::transaction(function () use ($variants, $adjustmentType, $adjustmentValue) {
            foreach ($variants as $variant) {
                $oldPrice = (float) $variant->price;
                $newPrice = match ($adjustmentType) {
                    'increase_percent' => $oldPrice * (1 + ($adjustmentValue / 100.0)),
                    'decrease_percent' => max(0, $oldPrice * (1 - ($adjustmentValue / 100.0))),
                    'increase_fixed'   => $oldPrice + $adjustmentValue,
                    'decrease_fixed'   => max(0, $oldPrice - $adjustmentValue),
                };

                $variant->price = round($newPrice, 2);
                $variant->save();
            }
        });

        $count = $variants->count();
        \Illuminate\Support\Facades\Log::info("Admin ID " . \Illuminate\Support\Facades\Auth::id() . " performed bulk price adjustment ({$adjustmentType} {$adjustmentValue}) across {$count} variants in {$scopeName} from IP " . $request->ip());
        return back()->with('success', "Bulk price adjustment applied to {$count} variants across {$scopeName}.");
    }

    public function clearCache(): RedirectResponse
    {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return back()->with('success', 'System cache, views, routes, and config cleared successfully.');
    }

    /**
     * Rewrite an absolute link that points at this store into a host-less path.
     */
    private function relativizeInternalUrl(string $value): string
    {
        return Setting::relativizeUrl($value) ?? $value;
    }
}
