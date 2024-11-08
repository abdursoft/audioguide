<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseMail extends Model
{

    public function User(){
        return $this->belongsTo(User::class);
    }

    
    protected $fillable = [
        'mail',
        'user_id',
    ];
}
