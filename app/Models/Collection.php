<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Collection $collection) {
            if (empty($collection->slug)) {
                $collection->slug = static::generateUniqueSlug($collection->name);
            }
        });

        static::updating(function (Collection $collection) {
            if ($collection->isDirty('name') && empty($collection->slug)) {
                $collection->slug = static::generateUniqueSlug($collection->name, $collection->id);
            }
        });
    }

    /**
     * Generate a unique URL slug for the collection.
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'collection';
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-" . (++$counter);
        }

        return $slug;
    }

    /**
     * Active collections scope.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Associated products relationship.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'collection_product')->withTimestamps();
    }

    /**
     * Helper to resolve an array of collection IDs or newly entered collection names.
     *
     * @param  array|string|null  $input
     * @return int[]
     */
    public static function resolveCollectionIds($input): array
    {
        if (empty($input)) {
            return [];
        }

        if (is_string($input)) {
            $input = preg_split('/[,;\n]+/', $input, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($input)) {
            return [];
        }

        $collectionIds = [];

        foreach ($input as $item) {
            if (is_numeric($item)) {
                $itemInt = (int) $item;
                if ($itemInt > 0 && static::where('id', $itemInt)->exists()) {
                    $collectionIds[] = $itemInt;
                    continue;
                }
            }

            $name = trim(strip_tags((string) $item));
            if ($name === '') {
                continue;
            }

            // Find by exact name or create
            $collection = static::firstOrCreate(
                ['name' => $name],
                [
                    'slug'        => static::generateUniqueSlug($name),
                    'is_active'   => true,
                    'sort_order'  => 0,
                    'description' => null,
                ]
            );

            $collectionIds[] = $collection->id;
        }

        return array_values(array_unique($collectionIds));
    }

    /**
     * Helper for resolving collection image URL with fallback.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }
}
