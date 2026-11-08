<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneOtp extends Model
{
    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'is_verified',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Hidden attributes for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'otp',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
