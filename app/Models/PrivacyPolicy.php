<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivacyPolicy extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'privacy_policies';

    protected $fillable = [
        'file',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
