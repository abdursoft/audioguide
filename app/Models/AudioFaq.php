<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioFaq extends Model
{
    use HasFactory;

    public function AudioDescription(){
        return $this->belongsTo(AudioDescription::class);
    }

    protected $fillable = [
        'answer',
        'question',
        'audio_description_id'
    ];
}
