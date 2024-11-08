<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'replay',
        'seen',
        'seen_at',
        'replay_at',
        'replay_id',
        'is_admin'
    ];
}
