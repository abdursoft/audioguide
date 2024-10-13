<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon',
        'start_date',
        'end_date',
        'amount',
        'min_purchase',
    ];
}
