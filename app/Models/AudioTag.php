<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioTag extends Model
{
    use HasFactory;

    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }

    protected $fillable = [
        'tags',
        'audio_guide_id'
    ];
}
