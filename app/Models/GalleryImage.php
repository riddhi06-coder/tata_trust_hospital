<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryImage extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'gallery_images';

    protected $fillable = [
        'image',
        'title',
        'show_on_home',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'show_on_home' => 'boolean',
        'sort_order'   => 'integer',
    ];
}
