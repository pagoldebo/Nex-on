<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $fillable = ['product_id', 'name', 'hex', 'cover_image', 'sort_order'];

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
        $coverPath = $this->attributes['cover_image'] ?? null;

        if (!empty($coverPath)) {
            if (\Illuminate\Support\Str::startsWith($coverPath, ['http://', 'https://'])) {
                return $coverPath;
            }

            $path = ltrim($coverPath, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            if (str_starts_with($path, 'images/')) {
                return asset($path);
            }

            return asset('images/' . $path);
        }

        $image = $this->images->first();

        if (!$image || empty($image->image_path)) {
            return asset('images/placeholder.jpg');
        }

        return $image->url;
    }

    /**
     * Shape this color (and its available sizes/prices/stock) into the
     * structure the front-end color/size picker JS consumes directly.
     */
    public function toPickerArray(): array
    {
        $gallery = collect();
        $coverUrl = $this->cover_image;

        if ($coverUrl && $coverUrl !== asset('images/placeholder.jpg')) {
            $gallery->push($coverUrl);
        }

        foreach ($this->images as $img) {
            $url = $img->url;
            if (!$gallery->contains($url)) {
                $gallery->push($url);
            }
        }

        if ($gallery->isEmpty()) {
            $gallery->push(asset('images/placeholder.jpg'));
        }

        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'hex'    => $this->hex,
            'images' => $gallery->values(),
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
