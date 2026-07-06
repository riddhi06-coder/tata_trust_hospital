<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilitySetting extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'facility_settings';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'section_heading',
        'section_description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
