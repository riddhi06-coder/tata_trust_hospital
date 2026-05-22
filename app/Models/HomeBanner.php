<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;

    protected $table = 'home_banners';

    protected $fillable = [
        'banner_heading',
        'banner_title',
        'banner_media',   
        'media_type',    
        'created_by',

        'created_at',
        'updated_at',  
        'updated_by',    
        'deleted_at',
        'deleted_by',

    ];

}
