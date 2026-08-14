<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'product_id', 
        'variation_id', 
        'name', 
        'price', 
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array', 
            'price' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
