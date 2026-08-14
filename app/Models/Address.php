<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'address',
        'lat',
        'lng',
        'floor',
        'street',
        'building_number',
        'city_id',
        'zone_id',
        'additional_data',
    ];
    protected $appends = ["map"];
 
    public function getMapAttribute(){
        if(isset($this->attributes['lat']) && isset($this->attributes['lng'])){
            return "https://www.google.com/maps?q=" . $this->attributes['lat'] . ',' . $this->attributes['lng'];
        }
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    } 

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    } 
}
