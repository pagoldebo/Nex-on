<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'phone',
        'email',
        'is_central',
        'is_active',
        'channel',
    ];

    protected $casts = [
        'is_central' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(OutletStock::class);
    }

    public function transfersSent(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'from_outlet_id');
    }

    public function transfersReceived(): HasMany
    {
        return $this->hasMany(StockTransfer::class, 'to_outlet_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCentral($query)
    {
        return $query->where('is_central', true);
    }

    public function totalStockOnHand(): int
    {
        return (int) $this->stocks()->sum('stock');
    }

    public function uniqueSkusCount(): int
    {
        return (int) $this->stocks()->where('stock', '>', 0)->count();
    }
}
