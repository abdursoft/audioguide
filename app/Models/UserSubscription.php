<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'paid_amount',
        'currency',
        'user_id',
        'subscription_id',
    ];
}
