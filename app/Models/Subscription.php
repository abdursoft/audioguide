<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public function UserSubscription(){
        return $this->hasMany(UserSubscription::class);
    }

    protected $fillable = [
        'stripe_id',
        'stripe_price',
        'title',
        'description',
        'price',
        'currency',
        'duration',
        'status',
        'type',
    ];
}
