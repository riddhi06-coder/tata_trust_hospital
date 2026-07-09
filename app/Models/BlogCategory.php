<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'slug',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
