<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PincodeZone extends Model
{
    protected $fillable = [
        'pincode',
        'city',
        'state',
        'cod_available',
        'delivery_days',
        'is_serviceable',
    ];

    protected $casts = [
        'cod_available'  => 'boolean',
        'delivery_days'  => 'integer',
        'is_serviceable' => 'boolean',
    ];

    /**
     * Look up delivery details for an Indian pincode.
     */
    public static function checkPincode(string $pincode): array
    {
        $zone = static::where('pincode', $pincode)->first();

        if ($zone) {
            $eta = now()->addDays($zone->delivery_days)->format('D, M j');
            return [
                'serviceable'    => $zone->is_serviceable,
                'deliverable'    => $zone->is_serviceable,
                'pincode'        => $zone->pincode,
                'city'           => $zone->city,
                'state'          => $zone->state,
                'cod_available'  => $zone->cod_available,
                'delivery_days'  => $zone->delivery_days,
                'estimated_days' => $zone->delivery_days,
                'eta'            => $eta,
                'estimated_date' => $eta,
                'message'        => $zone->is_serviceable ? "Delivery in {$zone->delivery_days} days to {$zone->city}" : "Service currently unavailable in this area",
            ];
        }

        // Standard Indian delivery fallback for unmapped pincodes
        $etaFallback = now()->addDays(4)->format('D, M j');
        return [
            'serviceable'    => true,
            'deliverable'    => true,
            'pincode'        => $pincode,
            'city'           => 'Your Location',
            'state'          => 'India',
            'cod_available'  => true,
            'delivery_days'  => 4,
            'estimated_days' => 4,
            'eta'            => $etaFallback,
            'estimated_date' => $etaFallback,
            'message'        => 'Standard delivery in 3–5 business days',
        ];
    }
}
