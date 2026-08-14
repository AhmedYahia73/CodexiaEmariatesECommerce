<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartOption extends Model
{
    protected $fillable = [
        'option_id',
        'cart_variation_id',
    ];
    
    public function option()
    {
        return $this->belongsTo(Option::class, "option_id");
    }
}
