<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    public function User(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'current_address',
        'permanent_address',
        'profile_image',
        'user_id',
    ];
}
