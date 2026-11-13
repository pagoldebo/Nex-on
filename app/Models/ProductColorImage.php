<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColorImage extends Model
{
    protected $fillable = ['product_color_id', 'image_path', 'sort_order'];

    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/placeholder.jpg');
        }

        if (\Illuminate\Support\Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        $path = ltrim($this->image_path, '/');
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset('images/' . $path);
    }
}
