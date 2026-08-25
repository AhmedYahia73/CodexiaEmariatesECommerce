<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'brand_name',
        'logo',
        'phone', 
        'wattsapp',
        'email',
        'address',
        'lat',
        'lng',
        'facebook',
        'insta',
        'tiktok',
        'ios_app',
        'android_app',
        'min_order',
        'currency',
        'logo2'
    ];

    protected $appends = ["logo_url", "map", "logo2_url"];


    public function getMapAttribute(){
        if(isset($this->attributes['lat']) && isset($this->attributes['lng'])){
            return "https://www.google.com/maps?q=" . $this->attributes['lat'] . ',' . $this->attributes['lng'];
        }
    }

    protected function casts(): array
    {
        return [
            'brand_name' => 'array', 
        ];
    }

    public function getLogoUrlAttribute()
    {
        if (isset($this->attributes['logo'])) {
            return asset('storage/' . $this->attributes['logo']);
        }
        return null;
    }

    public function getLogo2UrlAttribute()
    {
        if (isset($this->attributes['logo2'])) {
            return asset('storage/' . $this->attributes['logo2']);
        }
        return null;
    }
}
