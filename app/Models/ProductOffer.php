<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_type',
        'offer_amount',
        'price_amount',
        'status',
        'audio_guide_id',
    ];
}
