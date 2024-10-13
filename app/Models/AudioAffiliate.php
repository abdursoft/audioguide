<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioAffiliate extends Model
{
    use HasFactory;

    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }

    protected $fillable = [
        'button_label',
        'button_action',
        'audio_guide_id'
    ];
}
