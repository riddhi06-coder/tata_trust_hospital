<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogListing extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'blog_listings';

    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'thumbnail',
        'short_description',
        'blog_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'blog_date' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags()
    {
        return $this->hasMany(BlogListingTag::class, 'blog_listing_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
