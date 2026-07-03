<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'galleries';

    protected $fillable = [
        'banner_heading',
        'banner_title',
        'banner_media',
        'media_type',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
