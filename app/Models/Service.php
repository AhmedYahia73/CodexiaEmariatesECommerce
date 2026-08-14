<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'icon'
    ];
    protected $appends = ['icon_url'];
    
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }

    public function getIconUrlAttribute()
    {
        if (isset($this->attributes['icon'])) {
            return asset('storage/' . $this->attributes['icon']);
        }
        return null;
    } 
}
