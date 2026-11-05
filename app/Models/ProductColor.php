<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $fillable = ['product_id', 'name', 'hex', 'sort_order'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductColorImage::class)->orderBy('sort_order');
    }

    /**
     * Only the size variants that exist for THIS color. Because combinations
     * are sparse, a color might have 2 sizes while another has 4 — this
     * relationship naturally returns only what was actually created.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getCoverImageAttribute(): string
    {
        $image = $this->images->first();

        if (!$image || empty($image->image_path)) {
            return asset('images/placeholder.jpg');
        }

        if (\Illuminate\Support\Str::startsWith($image->image_path, ['http://', 'https://'])) {
            return $image->image_path;
        }

        return asset('storage/' . ltrim($image->image_path, '/'));
    }

    /**
     * Shape this color (and its available sizes/prices/stock) into the
     * structure the front-end color/size picker JS consumes directly.
     */
    public function toPickerArray(): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'hex'    => $this->hex,
            'images' => $this->images->map(fn ($img) => \Illuminate\Support\Str::startsWith($img->image_path, ['http://', 'https://']) ? $img->image_path : asset('storage/' . ltrim($img->image_path, '/')))->values(),
            'sizes'  => $this->variants->map(fn ($v) => [
                'variantId'      => $v->id,
                'sku'            => $v->sku,
                'size'           => $v->size,
                'price'          => (float) $v->price,
                'compareAtPrice' => $v->compare_at_price ? (float) $v->compare_at_price : null,
                'stock'          => $v->stock,
                'inStock'        => $v->stock > 0,
            ])->values(),
        ];
    }
}
