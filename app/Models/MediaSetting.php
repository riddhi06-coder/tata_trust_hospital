<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaSetting extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'media_settings';

    protected $fillable = [
        'banner_image',
        'heading',
        'section_heading',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
