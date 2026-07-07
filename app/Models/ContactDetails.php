<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactDetails extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'contact_details';

    protected $fillable = [
        'banner_heading',
        'banner_image',
        'address',
        'email',
        'footer_email',
        'emergency_no',
        'join_team_email',
        'donate_info',
        'map_url',
        'iframe_url',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function ribbonItems()
    {
        return $this->hasMany(ContactRibbonItem::class, 'contact_details_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
