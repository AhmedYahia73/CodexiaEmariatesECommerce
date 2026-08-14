<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $fillable = [
        'f_name',
        'l_name',
        'phone',
        'email',
        'title',
        'content',
        'status',
    ];
}
