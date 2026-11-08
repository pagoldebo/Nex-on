<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    /**
     * Check if role has a given permission slug.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains('slug', $permissionSlug);
        }

        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
}
