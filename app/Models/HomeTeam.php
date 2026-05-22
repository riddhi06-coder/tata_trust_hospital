<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeTeam extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'home_teams';

    protected $fillable = [
        'title',
        'description',
        'members',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'members' => 'array',
    ];
}
