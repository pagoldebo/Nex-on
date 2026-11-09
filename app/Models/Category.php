<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'image', 'sort_order', 'parent_id'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, '//')) {
            return $this->image;
        }

        $path = ltrim($this->image, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset('images/' . $path);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get array of all descendant category IDs (including this category ID).
     */
    public function getAllDescendantIds(array &$visited = []): array
    {
        if (in_array($this->id, $visited, true)) {
            return [];
        }
        $visited[] = $this->id;

        $ids = [$this->id];
        $children = $this->relationLoaded('children') 
            ? $this->children 
            : $this->children()->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds($visited));
        }

        return array_values(array_unique($ids));
    }

    /**
     * Get ancestors ordered from root parent down to this category.
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $current = $this;
        $visited = [];
        while ($current && !in_array($current->id, $visited, true)) {
            $visited[] = $current->id;
            array_unshift($breadcrumbs, $current);
            $current = $current->parent;
        }
        return $breadcrumbs;
    }

    /**
     * Formatted string breadcrumb path, e.g. "Men → Shirt → Formal Shirt"
     */
    public function getHierarchyNameAttribute(): string
    {
        return collect($this->getBreadcrumbs())->pluck('name')->join(' → ');
    }

    /**
     * Tree of top-level categories with recursive children and products.
     */
    public static function tree()
    {
        return static::whereNull('parent_id')
            ->with(['children.children', 'products:id,category_id,name,slug'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Return all categories flattened in hierarchical order with indent display.
     */
    public static function getFlatTree(?int $excludeId = null): \Illuminate\Support\Collection
    {
        $all = static::with(['parent', 'children'])
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $grouped = $all->groupBy(function ($cat) {
            return $cat->parent_id ? (int) $cat->parent_id : 0;
        });

        $result = collect();
        $visited = [];

        $traverse = function (int $parentId, int $depth, ?int $rootId = null, array $ancestorIds = []) use (&$traverse, $grouped, &$result, $excludeId, &$visited) {
            $children = $grouped->get($parentId);
            if (! $children) {
                return;
            }

            foreach ($children as $cat) {
                if ($excludeId && (int) $cat->id === (int) $excludeId) {
                    continue;
                }

                if (in_array($cat->id, $visited, true)) {
                    continue;
                }
                $visited[] = $cat->id;

                $currentRootId = $depth === 0 ? (int) $cat->id : $rootId;
                $currentAncestors = $ancestorIds;

                $indentPrefix = $depth > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth - 1) . '↳ ' : '';
                $result->push(new \Illuminate\Support\Fluent([
                    'id'             => $cat->id,
                    'name'           => $cat->name,
                    'slug'           => $cat->slug,
                    'parent_id'      => $cat->parent_id ? (int) $cat->parent_id : null,
                    'root_id'        => $currentRootId,
                    'ancestor_ids'   => $currentAncestors,
                    'has_children'   => (bool) ($cat->children && $cat->children->count() > 0),
                    'sort_order'     => $cat->sort_order,
                    'depth'          => $depth,
                    'indent_name'    => $indentPrefix . e($cat->name),
                    'image'          => $cat->image,
                    'image_url'      => $cat->image_url,
                    'hierarchy_name' => $cat->hierarchy_name,
                    'category'       => $cat,
                ]));

                $traverse((int) $cat->id, $depth + 1, $currentRootId, array_merge($currentAncestors, [(int) $cat->id]));
            }
        };

        $traverse(0, 0, null, []);

        return $result;
    }
}
