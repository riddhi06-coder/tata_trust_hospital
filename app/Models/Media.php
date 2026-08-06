<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'media';

    protected $fillable = [
        'title',
        'image',
        'article_link',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
