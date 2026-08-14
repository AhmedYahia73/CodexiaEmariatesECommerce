<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'price',
        'city_id',
        'status'
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    
    protected function casts(): array
    {
        return [
            'name' => 'array',
        ];
    }
}
