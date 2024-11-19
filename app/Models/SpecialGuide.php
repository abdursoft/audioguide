<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialGuide extends Model {
    use HasFactory;

    protected $fillable = [
        'person_id',
        'person_event_id',
        'person_object_id',
        'person_location_id'
    ];
}
