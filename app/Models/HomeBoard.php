<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeBoard extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'home_boards';

    protected $fillable = [
        'title',
        'description',
        'image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
