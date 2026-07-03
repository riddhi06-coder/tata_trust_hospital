<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurTeam extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'our_teams';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'education',
        'designation',
        'bio',
        'social_media_link',
        'show_on_home',
        'show_on_team_page',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'show_on_home'      => 'boolean',
        'show_on_team_page' => 'boolean',
    ];

    public function specialityDetails(): BelongsToMany
    {
        return $this->belongsToMany(
            SpecialitiesDetails::class,
            'speciality_detail_team',
            'our_team_id',
            'speciality_detail_id'
        )->withPivot(['bio_override', 'sort_order'])->withTimestamps();
    }
}
