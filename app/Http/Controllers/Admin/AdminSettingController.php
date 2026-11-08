<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
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
 * enforces strict image upload whitelisting (Domain 5), and sanitizes SVG files.
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
            'vyra_hero_image',
            'buttercup_hero_image',
            'buttercup_slide_1_image',
            'buttercup_slide_2_image',
            'buttercup_slide_3_image',
            'buttercup_slide_4_image',
            'buttercup_slide_5_image',
            'lumiere_hero_image',
            'saaya_hero_image',
            'saaya_story_image',
            'vyra_story_image',
            'buttercup_story_image',
            'lumiere_story_image',
            'organic_story_image',
            'hero_banner_image',
        ];

        $request->validate([
            'site_logo'               => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:5120',
            'vyra_hero_image'         => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'buttercup_hero_image'    => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'buttercup_slide_1_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'buttercup_slide_2_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'buttercup_slide_3_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'buttercup_slide_4_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'buttercup_slide_5_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'lumiere_hero_image'      => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'saaya_hero_image'        => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'saaya_story_image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'vyra_story_image'        => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'buttercup_story_image'   => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'lumiere_story_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'organic_story_image'     => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
            'hero_banner_image'       => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp|max:10240',
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
                    if ($oldImg && Storage::disk('public')->exists($oldImg)) {
                        Storage::disk('public')->delete($oldImg);
                    }
                    Setting::set($targetKey, '');
                }
            }
        }

        // Process whitelisted file uploads only (prevents arbitrary file uploads)
        foreach ($allowedImageKeys as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $folder = ($fileKey === 'site_logo') ? 'branding' : 'banners';
                $oldImg = Setting::get($fileKey);
                if ($oldImg && Storage::disk('public')->exists($oldImg)) {
                    Storage::disk('public')->delete($oldImg);
                }
                $uploadedFile = $request->file($fileKey);
                if (strtolower($uploadedFile->getClientOriginalExtension()) === 'svg' || str_contains($uploadedFile->getMimeType() ?? '', 'svg')) {
                    $sanitized = \App\Services\HtmlSanitizerService::sanitizeSvg(file_get_contents($uploadedFile->getRealPath()));
                    file_put_contents($uploadedFile->getRealPath(), $sanitized);
                }
                $storedPath = $uploadedFile->store($folder, 'public');
                Setting::set($fileKey, $storedPath);
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

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                if (in_array($key, $richContentKeys, true)) {
                    $value = \App\Services\HtmlSanitizerService::clean($value);
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
        return back()->with('success', "Bulk price adjustment applied to {$count} variants across {$scopeName}.");
    }

    public function clearCache(): RedirectResponse
    {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return back()->with('success', 'System cache, views, routes, and config cleared successfully.');
    }
}
