<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOption extends Model
{
    protected $fillable = [
        'order_product_id',
        'option_id',
    ];

    public function option(){
        return $this->belongsTo(Option::class, "option_id");
    }
}