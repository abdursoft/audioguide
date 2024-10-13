<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function AudioGuide(){
        return $this->hasMany(AudioGuide::class);
    }


    protected $fillable = [
        'category',
        'name',
        'image'
    ];
}
