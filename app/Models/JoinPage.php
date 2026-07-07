<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoinPage extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'join_pages';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'section_heading',
        'section_description',
        'current_job_title',
        'current_job_description',
        'common_heading',
        'common_title',
        'common_description',
        'extra_background_image',
        'extra_description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function infos()
    {
        return $this->hasMany(JoinPageInfo::class)->orderBy('sort_order')->orderBy('id');
    }

    public function commonRows()
    {
        return $this->hasMany(JoinPageCommonRow::class)->orderBy('sort_order')->orderBy('id');
    }
}
