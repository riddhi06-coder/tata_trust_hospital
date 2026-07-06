<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUs extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'about_us';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'about_heading',
        'about_description',
        'about_image',
        'about_info_items',
        'values_heading',
        'values_image',
        'values_description',
        'commitment_heading',
        'commitment_items',
        'contact_image',
        'contact_description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'about_info_items' => 'array',
        'commitment_items' => 'array',
    ];
}
