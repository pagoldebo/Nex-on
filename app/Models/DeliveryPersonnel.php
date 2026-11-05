<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryPersonnel extends Model
{
    use HasFactory;

    protected $table = 'delivery_personnel';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'vehicle_number',
        'vehicle_type',
        'status',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_personnel_id');
    }

    public function activeOrders(): HasMany
    {
        return $this->orders()->whereIn('delivery_status', ['assigned', 'out_for_delivery']);
    }

    public function completedDeliveriesCount(): int
    {
        return $this->orders()->where('delivery_status', 'delivered')->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
