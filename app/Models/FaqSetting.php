<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqSetting extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'faq_settings';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'section_heading',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
