<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'product_id',
        'discount',
        'price',
        'final_price',
        'order_id',
        'count',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function options()
    {
        return $this->hasMany(OrderOption::class, 'order_product_id');
    }
}
