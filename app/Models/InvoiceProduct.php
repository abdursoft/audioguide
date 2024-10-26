<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    use HasFactory;

    public function AudioGuide(){
        return $this->belongsTo(AudioGuide::class);
    }

    public function User(){
        return $this->belongsTo(User::class);
    }

    public function Invoice(){
        return $this->belongsTo(Invoice::class);
    }

    protected $fillable = [
        'quantity',
        'sale_price',
        'user_id',
        'invoice_id',
        'audio_guide_id',
    ];
}
