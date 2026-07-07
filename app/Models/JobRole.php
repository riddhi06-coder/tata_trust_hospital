<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobRole extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'job_roles';

    public const JOB_TYPES = [
        'Full-time'  => 'Full-time',
        'Part-time'  => 'Part-time',
        'Contract'   => 'Contract',
        'Internship' => 'Internship',
        'Freelance'  => 'Freelance',
    ];

    public const WORK_MODES = [
        'On-site' => 'On-site',
        'Remote'  => 'Remote',
        'Hybrid'  => 'Hybrid',
    ];

    protected $fillable = [
        'job_position',
        'slug',
        'job_location',
        'job_type',
        'work_mode',
        'jd_file',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
