<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactEnquiry extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'contact_enquiries';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
