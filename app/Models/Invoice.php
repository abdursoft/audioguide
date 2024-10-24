<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'vat',
        'total',
        'discount',
        'sub_total',
        'payable',
        'trans_id',
        'coupon_code',
        'gateway',
        'payment_id',
        'delivery_status',
        'payment_status',
        'user_id',
    ];
}
