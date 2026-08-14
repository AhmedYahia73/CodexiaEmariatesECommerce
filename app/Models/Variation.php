<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    protected $fillable = [
        'product_id', 
        'name', 
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array', 
        ];
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
