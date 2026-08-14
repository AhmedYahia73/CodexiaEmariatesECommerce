<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartVariation extends Model
{
    protected $fillable = [
        'variation_id',
        'cart_product_id',
    ];
    
    public function variation()
    {
        return $this->belongsTo(Variation::class, "variation_id");
    }
    
    public function cart_options()
    {
        return $this->hasMany(CartOption::class, "cart_variation_id");
    }
}
