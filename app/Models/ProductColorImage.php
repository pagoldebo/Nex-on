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
        $cleanRel = str_starts_with($path, 'images/') ? substr($path, 7) : $path;

        if (app()->environment('testing')) {
            return str_starts_with($path, 'images/') ? asset($path) : asset('images/' . $path);
        }

        if (file_exists(public_path('images/' . $cleanRel))) {
            return asset('images/' . $cleanRel);
        }
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('images/placeholder.jpg');
    }
}
