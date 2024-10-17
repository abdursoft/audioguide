<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCart extends Model
{
    use HasFactory;

    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }


    protected $fillable = [
        'price',
        'user_id',
        'quantity',
        'discount',
        'audio_guide_id'
    ];
}
