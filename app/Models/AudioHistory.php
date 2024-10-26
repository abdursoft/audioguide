<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioHistory extends Model
{
    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }

    public function User(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'status',
        'user_id',
        'audio_guide_id'
    ];
}
