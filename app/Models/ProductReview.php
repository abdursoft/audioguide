<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }

    public function User(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'star',
        'description',
        'audio_guide_id',
        'user_id',
    ];
}
