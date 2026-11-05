<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'contact_name',
        'email',
        'phone',
        'lead_time_days',
        'gst_number',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'lead_time_days' => 'integer',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
