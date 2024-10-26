<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBilling extends Model
{
    public function User(){
        return $this->belongsTo(User::class);
    }

    
    use HasFactory;
}
