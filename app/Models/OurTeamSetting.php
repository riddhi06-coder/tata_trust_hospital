<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurTeamSetting extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'our_team_settings';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'section_heading',
        'section_description',
        'motto',
        'motto_description',
        'motto_image',
        'board_heading',
        'board_small_desc',
        'board_image',
        'board_name',
        'board_designation',
        'board_members',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'board_members' => 'array',
    ];
}
