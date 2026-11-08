<?php

namespace App\Models\Concerns;

use App\Models\Scopes\RowLevelSecurityScope;
use Illuminate\Support\Facades\Auth;

trait HasRowLevelSecurity
{
    /**
     * Boot the Row Level Security trait on the model.
     */
    public static function bootHasRowLevelSecurity(): void
    {
        static::addGlobalScope(new RowLevelSecurityScope());

        // Automatically bind owner column to authenticated user on creation if not explicitly set
        static::creating(function ($model) {
            $ownerKey = $model->getOwnerKeyName();
            if (empty($model->{$ownerKey}) && Auth::check()) {
                $user = Auth::user();
                if (! (method_exists($user, 'isAdmin') && $user->isAdmin())) {
                    $model->{$ownerKey} = $user->id;
                }
            }
        });
    }

    /**
     * Get the column name representing the owner user ID.
     */
    public function getOwnerKeyName(): string
    {
        return property_exists($this, 'ownerKeyName') ? $this->ownerKeyName : 'user_id';
    }

    /**
     * Determine if unauthenticated guests are allowed to query this model.
     */
    public function allowGuestAccess(): bool
    {
        return property_exists($this, 'allowGuestAccess') ? (bool) $this->allowGuestAccess : false;
    }

    /**
     * Scope a query to bypass Row Level Security (RLS) when explicitly needed by trusted services.
     */
    public function scopeWithoutRowLevelSecurity($query)
    {
        return $query->withoutGlobalScope(RowLevelSecurityScope::class);
    }
}

