<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'job_applications';

    protected $fillable = [
        'job_role_id',
        'applying_for',
        'full_name',
        'email',
        'phone',
        'location',
        'joining_time',
        'message',
        'resume_file',
        'ip_address',
        'user_agent',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function jobRole()
    {
        return $this->belongsTo(JobRole::class, 'job_role_id');
    }
}
