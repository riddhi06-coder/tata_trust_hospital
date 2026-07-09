<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogDetails extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'blog_details';

    protected $fillable = [
        'blog_listing_id',
        'image',
        'information',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function listing()
    {
        return $this->belongsTo(BlogListing::class, 'blog_listing_id');
    }

    public function socialLinks()
    {
        return $this->hasMany(BlogDetailSocialLink::class, 'blog_detail_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
