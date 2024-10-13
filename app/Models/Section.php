<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'sub_title',
        'description',
        'button_title',
        'button_action',
        'image',
        'mobile_image',
        'status',
        'short_description'
    ];
}

