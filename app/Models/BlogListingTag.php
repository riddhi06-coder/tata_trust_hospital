<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogListingTag extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'blog_listing_tags';

    protected $fillable = [
        'blog_listing_id',
        'tag',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function blogListing()
    {
        return $this->belongsTo(BlogListing::class, 'blog_listing_id');
    }
}
