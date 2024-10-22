<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioGuide extends Model
{
    use HasFactory;

    public function AudioContent(){
        return $this->hasMany(AudioContent::class);
    }

    public function AudioDescription(){
        return $this->hasOne(AudioDescription::class);
    }

    public function Affiliate(){
        return $this->hasMany(AudioAffiliate::class);
    }

    public function ProductCart(){
        return $this->hasMany(ProductCart::class);
    }

    public function ProductWish(){
        return $this->hasMany(ProductWish::class);
    }

    public function ProductOffer(){
        return $this->hasOne(ProductOffer::class);
    }

    public function ProductInvoice(){
        return $this->hasMany(InvoiceProduct::class);
    }

    public function ProductTag(){
        return $this->hasOne(AudioTag::class);
    }

    public function Category(){
        return $this->belongsTo(Category::class);
    }


    protected $fillable = [
        'name',
        'title',
        'price',
        'status',
        'cover', 
        'remark',
        'discount',
        'type',
        'category_id',
        'call_to_action',
        'short_description'
    ];
}
