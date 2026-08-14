<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'category_id', 
        'status'
    ];

    protected $appends = ["image_url"];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
        ];
    }

    public function getImageUrlAttribute()
    {
        if (isset($this->attributes['image'])) {
            return asset('storage/' . $this->attributes['image']);
        }
        return null;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'category_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
