<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushCampaign extends Model
{
    protected $fillable = [
        'title',
        'body',
        'action_url',
        'image_url',
        'sent_count',
        'status',
    ];

    protected $casts = [
        'sent_count' => 'integer',
    ];
}
