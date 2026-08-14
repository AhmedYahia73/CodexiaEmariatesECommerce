<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id', 
        'image', 
        'price', 
        'discount', 
        'discount_from', 
        'discount_to', 
        'status'
    ];
    protected $appends = ['final_price', 'is_discounted', 'image_url'];

     public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function variations()
    {
        return $this->hasMany(Variation::class);
    }
    
    public function gallery()
    {
        return $this->hasMany(ProductImage::class);
    }
    
    public function getIsDiscountedAttribute()
    {
        if (empty($this->attributes['discount']) || $this->attributes['discount'] <= 0 || 
            empty($this->attributes['discount_from']) || empty($this->attributes['discount_to'])) {
            return false;
        }

        // تحويل النصوص إلى كائنات Carbon وتصفير الوقت (بداية اليوم)
        $today = now()->startOfDay();
        $discountFrom = Carbon::parse($this->attributes['discount_from'])->startOfDay();
        $discountTo = Carbon::parse($this->attributes['discount_to'])->startOfDay();

        // المقارنة الآن أصبحت بين تواريخ فقط بدون تأثير الساعات والدقائق
        return $today->gte($discountFrom) && $today->lte($discountTo);
    }

    public function getFinalPriceAttribute()
    {
        // بنادي على الـ attribute اللي فوق منعاً لتكرار الكود
        if ($this->is_discounted) {
            return $this->price - $this->discount;
        }
        
        return $this->price;
    }

    public function getImageUrlAttribute()
    {
        if (isset($this->attributes['image'])) {
            return asset('storage/' . $this->attributes['image']);
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
