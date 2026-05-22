<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeFacilities extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'home_facilities';

    protected $fillable = [
        'title',
        'description',
        'facilities',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'facilities' => 'array',
    ];
}
