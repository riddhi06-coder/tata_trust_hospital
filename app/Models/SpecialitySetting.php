<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialitySetting extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'speciality_settings';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'service_section_heading',
        'service_description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
