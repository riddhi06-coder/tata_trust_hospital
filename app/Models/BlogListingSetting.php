<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogListingSetting extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'blog_listing_settings';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
