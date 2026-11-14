<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Whether privileged attributes (role, is_staff) are permitted during mass assignment.
     * Always false in production/normal operations. Only enabled in test harnesses when explicitly requested.
     */
    public static bool $allowPrivilegedMassAssignment = false;

    /**
     * Get the fillable attributes for the model.
     * In production, role and is_staff are strictly excluded.
     */
    public function getFillable(): array
    {
        if (static::$allowPrivilegedMassAssignment) {
            return array_merge($this->fillable, ['role', 'is_staff']);
        }

        return $this->fillable;
    }

    /**
     * Determine if the given attribute may be mass assigned.
     */
    public function isFillable($key): bool
    {
        if (static::$allowPrivilegedMassAssignment && in_array($key, ['role', 'is_staff'], true)) {
            return true;
        }

        return parent::isFillable($key);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'designation',
        'department_name',
        'employment_status',
        'staff_sales',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'deactivated_at'           => 'datetime',
            'otp_expires_at'           => 'datetime',
            'two_factor_confirmed_at'  => 'datetime',
            'password'                 => 'hashed',
        ];
    }

    public function generateOtp(int $minutes = 10, int $length = 6): string
    {
        $min = 10 ** ($length - 1);
        $max = (10 ** $length) - 1;
        $otp = (string) random_int($min, $max);
        $this->forceFill([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes($minutes),
        ])->save();
        return $otp;
    }

    public function verifyOtp(string $otp): bool
    {
        if (empty($this->otp_code) || $this->otp_code !== $otp) {
            return false;
        }

        if ($this->otp_expires_at && now()->isAfter($this->otp_expires_at)) {
            return false;
        }

        return true;
    }

    public function clearOtp(): void
    {
        $this->forceFill([
            'otp_code'       => null,
            'otp_expires_at' => null,
        ])->save();
    }

    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && !empty($this->two_factor_confirmed_at);
    }

    public function getRecoveryCodes(): array
    {
        if (empty($this->two_factor_recovery_codes)) {
            return [];
        }
        try {
            return json_decode(decrypt($this->two_factor_recovery_codes), true) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function replaceRecoveryCode(string $code): bool
    {
        $codes = $this->getRecoveryCodes();
        $code = trim($code);

        foreach ($codes as $idx => $storedCode) {
            if (hash_equals($storedCode, $code)) {
                unset($codes[$idx]);
                $this->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($codes))),
                ])->save();

                return true;
            }
        }

        return false;
    }

    public function isDeactivated(): bool
    {
        return $this->deactivated_at !== null;
    }

    public function isActive(): bool
    {
        return $this->deactivated_at === null;
    }

    public function isAdmin(): bool
    {
        return ($this->role === 'admin' && ! $this->is_staff) || $this->isSuperAdmin();
    }

    public function isStaff(): bool
    {
        return (bool) $this->is_staff || $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return ! $this->isAdmin() && ! $this->isStaff() && ($this->role === 'customer' || empty($this->role));
    }

    public function isSuperAdmin(): bool
    {
        // 1. Explicitly assigned 'super-admin' role
        if ($this->relationLoaded('roles')) {
            if ($this->roles->contains('slug', 'super-admin')) {
                return true;
            }
        } elseif ($this->roles()->where('slug', 'super-admin')->exists()) {
            return true;
        }

        // 2. Or is a root admin (role === 'admin') that is NOT constrained staff
        return $this->role === 'admin' && ! $this->is_staff;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deactivated_at');
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string|array $roles): bool
    {
        $roleList = is_array($roles) ? $roles : [$roles];

        if ($this->isSuperAdmin() && in_array('super-admin', $roleList, true)) {
            return true;
        }

        $userRoles = $this->relationLoaded('roles') ? $this->roles : $this->roles()->get();
        return $userRoles->pluck('slug')->intersect($roleList)->isNotEmpty();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        // Root super-admin has full unrestricted system access
        if ($this->isSuperAdmin()) {
            return true;
        }

        $userRoles = $this->relationLoaded('roles')
            ? $this->roles
            : $this->roles()->with('permissions')->get();

        foreach ($userRoles as $role) {
            if ($role->hasPermission($permissionSlug)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission(array $permissionSlugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($permissionSlugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(Role|string $role): void
    {
        $roleId = is_string($role) ? Role::where('slug', $role)->value('id') : $role->id;
        if ($roleId) {
            $this->roles()->syncWithoutDetaching([$roleId]);
        }
    }

    /**
     * Get orders placed by this user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function customerGroups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(CustomerGroup::class, 'customer_group_user')
            ->withPivot('assigned_at');
    }

    public function loyaltyAccount(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LoyaltyAccount::class);
    }

    public function departments(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user')
            ->withPivot('role_title', 'is_primary')
            ->withTimestamps();
    }

    public function scopeStaff($query)
    {
        return $query->where(function ($q) {
            $q->where('is_staff', true)
              ->orWhereIn('role', ['admin', 'vendor'])
              ->orWhereHas('roles')
              ->orWhereHas('departments');
        });
    }

    public function getEffectiveRoleNameAttribute(): string
    {
        if (!empty($this->designation)) {
            return $this->designation;
        }
        $userRole = $this->roles()->first();
        if ($userRole) {
            return $userRole->name;
        }
        $dept = $this->departments()->first();
        if ($dept && !empty($dept->pivot->role_title)) {
            return $dept->pivot->role_title;
        }
        return match ($this->role) {
            'admin'  => 'Store Manager',
            'vendor' => 'Operations Vendor',
            default  => 'Staff Associate',
        };
    }

    public function getEffectiveDepartmentNameAttribute(): string
    {
        if (!empty($this->department_name)) {
            return $this->department_name;
        }
        $dept = $this->departments()->first();
        if ($dept) {
            return $dept->name;
        }
        return 'Operations';
    }

    public function getTotalSalesAmountAttribute(): float
    {
        $calculated = (float) Order::where('pos_cashier_id', $this->id)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        if ($calculated > 0) {
            return $calculated;
        }

        return (float) ($this->staff_sales ?? 0);
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name));
        if (count($parts) >= 2) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
        }
        return mb_strtoupper(mb_substr((string) $this->name, 0, 2));
    }

    public function getJoinedDateFormattedAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M Y') : 'Jan 2024';
    }
}
