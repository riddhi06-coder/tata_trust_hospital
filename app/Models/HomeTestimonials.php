<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeTestimonials extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'home_testimonials';

    protected $fillable = [
        'title',
        'image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
