<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
    ];

    protected static function booted()
    {
        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $baseSlug = Str::slug($tag->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $tag->slug = $slug;
            }
            if (empty($tag->color)) {
                $tag->color = '#4F46E5';
            }
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tag')->withTimestamps();
    }

    /**
     * Parse mixed tag input (comma-separated string, JSON, or array of IDs/names)
     * and resolve/create Tag records, returning an array of Tag IDs for syncing.
     */
    public static function resolveTagIds(mixed $input): array
    {
        if (empty($input)) {
            return [];
        }

        $items = [];
        if (is_string($input)) {
            // Check if JSON array
            $decoded = json_decode($input, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = explode(',', $input);
            }
        } elseif (is_array($input)) {
            $items = $input;
        }

        $tagIds = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $item = $item['name'] ?? $item['value'] ?? $item['id'] ?? null;
            }
            if (empty($item)) {
                continue;
            }

            // If it's already an existing ID
            if (is_numeric($item)) {
                $existing = static::find((int) $item);
                if ($existing) {
                    $tagIds[] = $existing->id;
                    continue;
                }
            }

            // Lookup by name or slug, or create new tag
            $cleanName = trim((string) $item);
            if ($cleanName === '') {
                continue;
            }

            $tag = static::whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
                ->orWhere('slug', Str::slug($cleanName))
                ->first();

            if (! $tag) {
                $palette = ['#E11D48', '#7C3AED', '#D97706', '#DC2626', '#059669', '#2563EB', '#4338CA', '#0891B2'];
                $randomColor = $palette[abs(crc32($cleanName)) % count($palette)];

                $tag = static::create([
                    'name'  => $cleanName,
                    'slug'  => Str::slug($cleanName) ?: ('tag-' . Str::random(5)),
                    'color' => $randomColor,
                ]);
            }

            $tagIds[] = $tag->id;
        }

        return array_values(array_unique($tagIds));
    }
}
