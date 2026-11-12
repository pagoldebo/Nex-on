<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Generate complete Schema.org JSON-LD for a Product page.
     * Includes Product, Offer (or AggregateOffer), AggregateRating, Reviews, and BreadcrumbList.
     *
     * @param Product $product
     * @return array
     */
    public function generateProductJsonLd(Product $product): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Nex-on'));
        $currency  = Setting::get('currency_code', 'INR');
        $siteUrl   = url('/');

        // 1. Collect all images (absolute URLs)
        $images = [];
        if ($product->cover_image) {
            $images[] = $this->ensureAbsoluteUrl($product->cover_image);
        }
        if ($product->relationLoaded('colors') || $product->colors()->exists()) {
            foreach ($product->colors as $color) {
                foreach ($color->images as $img) {
                    if (!empty($img->url)) {
                        $images[] = $this->ensureAbsoluteUrl($img->url);
                    }
                }
            }
        }
        $images = array_values(array_unique(array_filter($images)));
        if (empty($images)) {
            $images[] = $this->ensureAbsoluteUrl('images/branding/logo.png');
        }

        // 2. Price and Availability
        $price = $product->starting_price ?? $product->price ?? 0;
        $inStock = true;
        if ($product->relationLoaded('variants') || $product->variants()->exists()) {
            $totalStock = $product->variants->sum('stock');
            $inStock = $totalStock > 0;
        }

        $cleanDescription = Str::limit(strip_tags($product->short_description ?: $product->description ?: $product->name), 500);

        // 3. Product Schema
        $productSchema = [
            '@context'    => 'https://schema.org/',
            '@type'       => 'Product',
            'name'        => $product->name,
            'image'       => count($images) === 1 ? $images[0] : $images,
            'description' => $cleanDescription,
            'sku'         => $product->sku ?: 'NEX-' . str_pad((string)$product->id, 5, '0', STR_PAD_LEFT),
            'mpn'         => $product->sku ?: 'NEX-' . str_pad((string)$product->id, 5, '0', STR_PAD_LEFT),
            'brand'       => [
                '@type' => 'Brand',
                'name'  => $product->brand?->name ?? $storeName,
            ],
            'offers'      => [
                '@type'           => 'Offer',
                'url'             => route('product.show', $product->slug),
                'priceCurrency'   => $currency,
                'price'           => number_format((float)$price, 2, '.', ''),
                'priceValidUntil' => now()->addYear()->endOfYear()->format('Y-m-d'),
                'itemCondition'   => 'https://schema.org/NewCondition',
                'availability'    => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller'          => [
                    '@type' => 'Organization',
                    'name'  => $storeName,
                ],
            ],
        ];

        // 4. AggregateRating and Reviews (if reviews exist)
        $approvedReviews = $product->relationLoaded('approvedReviews') 
            ? $product->approvedReviews 
            : $product->approvedReviews()->get();

        if ($approvedReviews->count() > 0) {
            $avgRating = round($approvedReviews->avg('rating'), 1);
            $reviewCount = $approvedReviews->count();

            $productSchema['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string)$avgRating,
                'reviewCount' => (string)$reviewCount,
                'bestRating'  => '5',
                'worstRating' => '1',
            ];

            $reviewItems = [];
            foreach ($approvedReviews->take(5) as $review) {
                $reviewItems[] = [
                    '@type'         => 'Review',
                    'author'        => [
                        '@type' => 'Person',
                        'name'  => $review->customer_name ?: ($review->user?->name ?: 'Verified Patron'),
                    ],
                    'datePublished' => $review->created_at ? $review->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'reviewRating'  => [
                        '@type'       => 'Rating',
                        'ratingValue' => (string)$review->rating,
                        'bestRating'  => '5',
                    ],
                    'reviewBody'    => $review->comment ?: ($review->title ?: 'Verified Purchase'),
                ];
            }
            if (!empty($reviewItems)) {
                $productSchema['review'] = $reviewItems;
            }
        }

        // 5. BreadcrumbList Schema
        $breadcrumbElements = [
            [
                '@type'    => 'ListItem',
                'position' => 1,
                'name'     => 'Home',
                'item'     => $siteUrl,
            ],
        ];

        $pos = 2;
        if ($product->category) {
            $ancestors = $product->category->getBreadcrumbs();
            foreach ($ancestors as $cat) {
                $breadcrumbElements[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => $cat->name,
                    'item'     => route('category.show', $cat->slug),
                ];
            }
        }

        $breadcrumbElements[] = [
            '@type'    => 'ListItem',
            'position' => $pos,
            'name'     => $product->name,
            'item'     => route('product.show', $product->slug),
        ];

        $breadcrumbSchema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $breadcrumbElements,
        ];

        return [$productSchema, $breadcrumbSchema];
    }

    /**
     * Generate Schema.org JSON-LD for a Category / Collection page.
     *
     * @param Category $category
     * @return array
     */
    public function generateCategoryJsonLd(Category $category): array
    {
        $siteUrl   = url('/');
        $storeName = Setting::get('store_name', config('app.name', 'Nex-on'));

        // 1. CollectionPage schema
        $collectionSchema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'CollectionPage',
            'name'        => $category->name . ' Collection',
            'url'         => route('category.show', $category->slug),
            'description' => "Explore the {$category->name} curated edit at {$storeName}. Handcrafted designer fashion and luxury bridalwear.",
        ];

        if ($category->image_url) {
            $collectionSchema['image'] = $this->ensureAbsoluteUrl($category->image_url);
        }

        // 2. Breadcrumbs
        $breadcrumbElements = [
            [
                '@type'    => 'ListItem',
                'position' => 1,
                'name'     => 'Home',
                'item'     => $siteUrl,
            ],
        ];

        $pos = 2;
        $ancestors = $category->getBreadcrumbs();
        foreach ($ancestors as $cat) {
            $breadcrumbElements[] = [
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $cat->name,
                'item'     => route('category.show', $cat->slug),
            ];
        }

        $breadcrumbSchema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $breadcrumbElements,
        ];

        return [$collectionSchema, $breadcrumbSchema];
    }

    /**
     * Generate Schema.org Organization schema.
     *
     * @return array
     */
    public function generateOrganizationJsonLd(): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Nex-on'));
        $siteUrl   = url('/');
        $logoUrl   = $this->ensureAbsoluteUrl(Setting::siteLogoUrl() ?: 'images/branding/logo.png');

        return [
            '@context'     => 'https://schema.org',
            '@type'        => 'Organization',
            'name'         => $storeName,
            'url'          => $siteUrl,
            'logo'         => $logoUrl,
            'contactPoint' => [
                '@type'             => 'ContactPoint',
                'telephone'         => Setting::supportPhone(),
                'contactType'       => 'customer service',
                'areaServed'        => 'IN',
                'availableLanguage' => ['English', 'Hindi'],
            ],
        ];
    }

    /**
     * Generate Schema.org WebSite schema with Sitelinks Searchbox.
     *
     * @return array
     */
    public function generateWebSiteJsonLd(): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Nex-on'));
        $siteUrl   = url('/');

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => $storeName,
            'url'             => $siteUrl,
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => $siteUrl . '/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Convert relative path or asset to fully qualified absolute URL.
     *
     * @param string|null $path
     * @return string
     */
    public function ensureAbsoluteUrl(?string $path): string
    {
        if (empty($path)) {
            return url('/');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '//')) {
            return (request()->isSecure() ? 'https:' : 'http:') . $path;
        }

        return url(ltrim($path, '/'));
    }
}

