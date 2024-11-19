<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReview extends Model
{
    protected $fillable = [
        'title',
        'image',
        'description'
    ];
}
