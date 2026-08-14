<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'count',
    ]; 
    
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, "product_id");
    }
    
    public function cart_variations()
    {
        return $this->hasMany(CartVariation::class, "cart_product_id");
    }
}
