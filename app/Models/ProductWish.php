<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWish extends Model
{
    use HasFactory;

    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }


    protected $fillable = [
        'audio_guide_id',
        'user_id'
    ];
}
