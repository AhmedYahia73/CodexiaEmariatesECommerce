<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'name', 
        'users_count', 
        'usage_limit', 
        'user_usage_limit', 
        'code',
        'discount',
        'type', // precentage, value
        'from', 
        'to', 
        'max_discount', 
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array', 
        ];
    }

    public function users(){
        return $this->belongsToMany(User::class, "coupon_user", "coupon_id", "user_id");
    }
}
