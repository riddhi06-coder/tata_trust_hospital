<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoinPageInfo extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'join_page_infos';

    protected $fillable = [
        'join_page_id',
        'image',
        'title',
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
