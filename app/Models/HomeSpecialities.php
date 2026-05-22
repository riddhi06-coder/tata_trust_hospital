<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeSpecialities extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'home_specialities';

    protected $fillable = [
        'our_motto',
        'title',
        'description',
        'specialities',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'specialities' => 'array',
    ];
}
