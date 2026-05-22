<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortIntroduction extends Model
{
    use HasFactory;

    protected $table = 'home_short_intro';

    protected $fillable = [
        'banner_heading',
        'banner_title',
        'banner_media',   
        'media_type',  
        'introduction',  
        'created_by',

        'created_at',
        'updated_at',  
        'updated_by',    
        'deleted_at',
        'deleted_by',

    ];

}
