<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialities extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'specialities';

    protected $fillable = [
        'speciality',
        'slug',
        'image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
