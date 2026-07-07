<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoinPageCommonRow extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'join_page_common_rows';

    protected $fillable = [
        'join_page_id',
        'job_title',
        'subject',
        'description',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function joinPage()
    {
        return $this->belongsTo(JoinPage::class);
    }
}
