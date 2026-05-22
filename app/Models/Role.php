<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPERADMIN_SLUG = 'superadmin';

    protected $fillable = ['name', 'slug', 'description', 'is_protected', 'is_active'];

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
