<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'price',
        'discount',
        'coupon_discount',
        'coupon_id',
        'payment_method_id',
        'user_id',
        'address_id',
        'final_price',
        'receipt',
        'payment_status',
        'status',
    ];
    protected $appends = ["receipt_url"];
 
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
 
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
 
    public function order_products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function getReceiptUrlAttribute()
    {
        if (isset($this->attributes['receipt'])) {
            return asset('storage/' . $this->attributes['receipt']);
        }
        return null;
    }
}
