<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'icon', 
        'status', 
    ];
    protected $appends = ["icon_url"];

    public function getIconUrlAttribute()
    {
        if (isset($this->attributes['icon'])) {
            return asset('storage/' . $this->attributes['icon']);
        }
        return null;
    }
    
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }
}
