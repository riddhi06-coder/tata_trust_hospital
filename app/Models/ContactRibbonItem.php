<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactRibbonItem extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'contact_ribbon_items';

    protected $fillable = [
        'contact_details_id',
        'icon',
        'title',
        'value',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function contactDetails()
    {
        return $this->belongsTo(ContactDetails::class, 'contact_details_id');
    }
}
