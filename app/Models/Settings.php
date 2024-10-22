<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'brand_logo',
        'mobile_logo',
        'icon',
        'title',
        'phone',
        'email',
        'address',
        'primary_color',
        'secondary_color',
        'short_description',
        'description'
    ];
}
