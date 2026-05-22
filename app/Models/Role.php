<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes, TracksDeletedBy;

    public const SUPERADMIN_SLUG = 'superadmin';

    protected $fillable = ['name', 'slug', 'description', 'is_protected', 'is_active', 'deleted_by'];

    protected $casts = [
        'is_protected' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissions()->where('slug', $slug)->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === self::SUPERADMIN_SLUG;
    }
}
