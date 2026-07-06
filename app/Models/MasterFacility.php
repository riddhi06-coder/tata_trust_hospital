<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFacility extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'master_facilities';

    protected $fillable = [
        'name',
        'image',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
