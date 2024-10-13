<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'sale_price',
        'user_id',
        'invoice_id',
        'audio_guide_id',
    ];
}
