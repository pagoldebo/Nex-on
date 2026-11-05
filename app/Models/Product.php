<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'brand_id', 'sku', 'name', 'slug', 'description', 'long_description',
        'badge', 'is_active', 'is_featured', 'is_bestseller', 'sort_order',
    ];

    public function getShortDescriptionAttribute(): ?string
    {
        return $this->attributes['description'] ?? null;
    }

    public function setShortDescriptionAttribute($value): void
    {
        $this->attributes['description'] = $value;
    }

    protected $casts = [
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
        'is_bestseller'  => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug ??= Str::slug($product->name) . '-' . Str::random(5);
            if (empty($product->sku)) {
                $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->name), 0, 3));
                $product->sku = 'LS-' . ($code ?: 'PRD') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class);
    }

    /**
     * Return all category slugs for this product including all ancestor categories up to the root.
     * e.g. ['bralettes', 'women-bra', 'lingerie', 'women']
     */
    public function getAllCategorySlugs(): array
    {
        if (! $this->category) {
            return [];
        }

        $slugs = [];
        $current = $this->category;
        $visited = [];
        while ($current && !in_array($current->id, $visited, true)) {
            $visited[] = $current->id;
            $slugs[] = $current->slug;
            $current = $current->parent;
        }

        return array_values(array_unique($slugs));
    }

    /**
     * Space-separated string of all category and ancestor slugs for data-category attribute.
     * e.g. "bralettes women-bra lingerie women"
     */
    public function getCategorySlugsStringAttribute(): string
    {
        return implode(' ', $this->getAllCategorySlugs());
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values')
            ->withPivot(['value_text', 'value_number', 'value_json'])
            ->withTimestamps();
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * Return localized product name based on active or specified locale.
     */
    public function getLocalizedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        if ($locale === 'en') {
            return $this->name;
        }

        if (is_array($this->translations ?? null) && !empty($this->translations[$locale]['name'])) {
            return (string) $this->translations[$locale]['name'];
        }

        $translated = __($this->name);
        return ($translated !== $this->name) ? $translated : $this->name;
    }

    /**
     * Return localized product description based on active or specified locale.
     */
    public function getLocalizedDescription(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        if ($locale === 'en' || empty($this->description)) {
            return $this->description;
        }

        if (is_array($this->translations ?? null) && !empty($this->translations[$locale]['description'])) {
            return (string) $this->translations[$locale]['description'];
        }

        $translated = __($this->description);
        return ($translated !== $this->description) ? $translated : $this->description;
    }

    /**
     * The lowest price across all variants — used for "from ₹X" display
     * on product cards before a color/size has been picked.
     */
    public function getStartingPriceAttribute(): ?float
    {
        return $this->variants->min('price');
    }

    /**
     * Highest compare_at_price among variants, for showing a strike-through
     * on the card before a specific variant is selected.
     */
    public function getStartingCompareAtPriceAttribute(): ?float
    {
        return $this->variants->whereNotNull('compare_at_price')->max('compare_at_price');
    }

    /**
     * The first color (by sort order) — used as the default selected
     * color when a product card or quick-view first renders.
     */
    public function getDefaultColorAttribute()
    {
        return $this->colors->first();
    }

    /**
     * Cover image for the card grid — the first image of the default color.
     * Falls back to a placeholder if no images have been uploaded yet.
     */
    public function getCoverImageAttribute(): string
    {
        $color = $this->default_color;
        $image = $color?->images->first();

        if (!$image || empty($image->image_path)) {
            return asset('images/placeholder.jpg');
        }

        if (Str::startsWith($image->image_path, ['http://', 'https://'])) {
            return $image->image_path;
        }

        return asset('storage/' . ltrim($image->image_path, '/'));
    }

    /**
     * Sum of stock across every size/color variant — used by Lumière's
     * "Only X Left" urgency badge on the Lustre Edit / Bestsellers rails.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock');
    }

    /**
     * Shape this product into the flat array structure the Blade product-card
     * partials and Lumière's JS expect. One method, reused by every theme.
     */
    public function toCardArray(): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'brand'               => $this->brand?->name,
            'slug'                => $this->slug,
            'category'            => $this->category?->name ?? 'Collection',
            'badge'               => $this->badge,
            'startingPrice'       => $this->starting_price,
            'startingCompareAt'   => $this->starting_compare_at_price,
            'coverImage'          => $this->cover_image,
            'description'         => $this->description,
            'shortDescription'    => $this->description,
            'longDescription'     => $this->long_description,
            'totalStock'          => $this->total_stock,
            'colors'              => $this->colors->map(fn ($c) => $c->toPickerArray())->values(),
        ];
    }
}
