<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioDescription extends Model
{
    use HasFactory;

    public function AudioFaq(){
        return $this->hasMany(AudioFaq::class);
    }

    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }

    protected $fillable = [
        'files',
        'description',
        'audio_guide_id'
    ];
}
