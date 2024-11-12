<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Browser extends Model
{
    protected $fillable = [
        'ip',
        'os',
        'uri',
        'month',
        'year',
        'method',
        'browser',
        'version'
    ];
}
