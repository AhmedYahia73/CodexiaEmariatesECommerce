<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = [
        'title', 
        'content', 
        'image'
    ];
    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'content' => 'array',
        ];
    }

    public function getImageUrlAttribute()
    {
        if (isset($this->attributes['image'])) {
            return asset('storage/' . $this->attributes['image']);
        }
        return null;
    } 
}
