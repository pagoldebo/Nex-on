<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RowLevelSecurityScope implements Scope
{
    /**
     * Apply Row Level Security (RLS) to Eloquent query builders.
     *
     * Ensures customers and non-privileged users are strictly bounded
     * to their own rows at the database query abstraction layer.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 1. Skip RLS enforcement in non-testing CLI / Artisan commands
        if (App::runningInConsole() && ! App::runningUnitTests()) {
            return;
        }

        $user = Auth::user();

        // 2. Authenticated user logic
        if ($user) {
            // Super-admins and system administrative staff bypass customer RLS boundaries
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return;
            }
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return;
            }
            // Staff members holding explicit operational permissions bypass customer RLS across custom & system roles (L-02)
            if ($user->is_staff && method_exists($user, 'hasAnyPermission') && $user->hasAnyPermission([
                'orders.view', 'orders.manage', 'reports.view', 'pos.access', 'roles.manage', 'settings.manage'
            ])) {
                return;
            }

            // Standard authenticated customer: strictly restrict rows to the authenticated user ID
            $ownerColumn = method_exists($model, 'getOwnerKeyName')
                ? $model->getOwnerKeyName()
                : 'user_id';

            $builder->where($model->qualifyColumn($ownerColumn), $user->id);
            return;
        }

        // 3. Unauthenticated guest logic
        if (method_exists($model, 'allowGuestAccess') && $model->allowGuestAccess()) {
            return;
        }

        // Strictly user-owned models (e.g., CustomerAddress, Wishlist, SupportTicket)
        // must never expose rows to unauthenticated anonymous callers
        $ownerColumn = method_exists($model, 'getOwnerKeyName')
            ? $model->getOwnerKeyName()
            : 'user_id';

        $builder->whereNull($model->qualifyColumn($ownerColumn));
    }
}

