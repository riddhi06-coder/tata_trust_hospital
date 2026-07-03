<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Events extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'thumbnail',
        'image',
        'month',
        'show_on_home',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'show_on_home' => 'boolean',
        'month'        => 'integer',
    ];

    public const MONTHS = [
        1  => 'January',
        2  => 'February',
        3  => 'March',
        4  => 'April',
        5  => 'May',
        6  => 'June',
        7  => 'July',
        8  => 'August',
        9  => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];

    public function getMonthNameAttribute(): ?string
    {
        return self::MONTHS[$this->month] ?? null;
    }
}
