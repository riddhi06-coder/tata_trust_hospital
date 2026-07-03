<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialitiesDetails extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'speciality_details';

    protected $fillable = [
        'speciality_id',
        'banner_image',
        'section_image',
        'section_heading',
        'section_description',
        'service_heading',
        'services',
        'short_info',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Specialities::class, 'speciality_id');
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(
            OurTeam::class,
            'speciality_detail_team',
            'speciality_detail_id',
            'our_team_id'
        )->withPivot(['bio_override', 'sort_order'])
         ->orderBy('speciality_detail_team.sort_order')
         ->withTimestamps();
    }
}
