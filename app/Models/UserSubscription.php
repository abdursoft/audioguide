<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    public function User(){
        return $this->belongsTo(User::class);
    }
    public function Subscription(){
        return $this->belongsTo(Subscription::class);
    }

    protected $fillable = [
        'payment_id',
        'paid_amount',
        'currency',
        'user_id',
        'status',
        'started_at',
        'ended_at',
        'invoice_url',
        'guide_type',
        'guide_id',
        'type',
        'subscription_id',
        'stripe_subscription_id',
    ];
}
