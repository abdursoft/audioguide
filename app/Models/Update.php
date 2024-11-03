<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    protected $fillable = [
        'title',
        'image',
        'type',
        'sub_title',
        'reference_id'
    ];
}
