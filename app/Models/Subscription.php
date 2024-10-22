<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_id',
        'stripe_price',
        'title',
        'description',
        'price',
        'currency',
        'duration',
        'status'
    ];
}
