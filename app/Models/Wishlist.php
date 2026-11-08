<?php

namespace App\Models;

use App\Models\Concerns\HasRowLevelSecurity;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasRowLevelSecurity;
    protected $fillable = ['user_id', 'product_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
