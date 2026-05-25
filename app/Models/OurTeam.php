<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurTeam extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'our_teams';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'education',
        'designation',
        'social_media_link',
        'show_on_home',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'show_on_home' => 'boolean',
    ];
}
