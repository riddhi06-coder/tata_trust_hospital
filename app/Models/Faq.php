<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'answer',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
