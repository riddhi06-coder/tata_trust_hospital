<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'blog_comments';

    protected $fillable = [
        'blog_listing_id',
        'name',
        'email',
        'website',
        'comment',
        'is_active',
        'ip_address',
        'user_agent',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function blogListing()
    {
        return $this->belongsTo(BlogListing::class, 'blog_listing_id');
    }
}
