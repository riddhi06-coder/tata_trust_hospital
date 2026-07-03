<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventSetting extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'event_settings';

    protected $fillable = [
        'section_heading',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
