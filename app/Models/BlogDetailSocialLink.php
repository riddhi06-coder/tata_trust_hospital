<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogDetailSocialLink extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'blog_detail_social_links';

    // Same platform catalog as ContactSocialLink so admin sees consistent options.
    public const PLATFORMS = [
        'facebook'  => ['label' => 'Facebook',    'icon' => 'fab fa-facebook-f'],
        'linkedin'  => ['label' => 'LinkedIn',    'icon' => 'fab fa-linkedin-in'],
        'instagram' => ['label' => 'Instagram',   'icon' => 'fab fa-instagram'],
        'twitter'   => ['label' => 'Twitter / X', 'icon' => 'fab fa-x-twitter'],
        'youtube'   => ['label' => 'YouTube',     'icon' => 'fab fa-youtube'],
        'whatsapp'  => ['label' => 'WhatsApp',    'icon' => 'fab fa-whatsapp'],
        'tiktok'    => ['label' => 'TikTok',      'icon' => 'fab fa-tiktok'],
        'telegram'  => ['label' => 'Telegram',    'icon' => 'fab fa-telegram-plane'],
        'pinterest' => ['label' => 'Pinterest',   'icon' => 'fab fa-pinterest-p'],
    ];

    protected $fillable = [
        'blog_detail_id',
        'platform',
        'url',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function blogDetail()
    {
        return $this->belongsTo(BlogDetails::class, 'blog_detail_id');
    }

    public function getIconClassAttribute(): string
    {
        return self::PLATFORMS[$this->platform]['icon'] ?? 'fas fa-link';
    }

    public function getPlatformLabelAttribute(): string
    {
        return self::PLATFORMS[$this->platform]['label'] ?? ucfirst($this->platform);
    }
}
