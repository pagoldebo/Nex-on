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

    public function setOtpAttribute($value): void
    {
        $value = (string) $value;
        $this->attributes['otp'] = (strlen($value) === 64 && ctype_xdigit($value)) ? $value : hash('sha256', $value);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
