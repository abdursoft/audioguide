<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sub_title',
        'banner',
        'coupon',
        'started_at',
        'expired_at',
        'amount',
        'status',
        'limitation',
        'coupon_type',
        'min_purchase',
    ];
}
