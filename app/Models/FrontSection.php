<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontSection extends Model
{
    protected $fillable = [
        'pagename',
        'section_title',
        'section_title_two',
        'heading',
        'heading_part_two',
        'subheading',
        'subheading_part_two',
        'short_description',
        'short_description_two',
        'description',
        'image',
        'faqs'
    ];
}
