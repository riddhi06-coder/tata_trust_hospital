<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeFollowUs extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'home_follow_us';

    protected $fillable = [
        'title',
        'description',
        'image',
        'social_media_link',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
