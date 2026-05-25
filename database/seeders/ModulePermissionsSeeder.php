<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | Module catalog
            |--------------------------------------------------------------------------
            | Each entry => [ 'Module label', 'slug-prefix' ]
            | Four permissions get seeded per module: view, create, edit, delete
            */
            $modules = [
                // Master modules
                ['Our Team Master',     'our-team'],
                ['Testimonials Master', 'testimonials'],

                // Home page sections
                ['Home Banners',          'home-banners'],
                ['Home Short Intro',      'home-short-intro'],
                ['Home Specialities',     'home-specialities'],
                ['Home Facilities',       'home-facilities'],
                ['Home Our Team',         'home-our-team'],
                ['Home Testimonials',     'home-testimonials'],
                ['Home Board',            'home-board'],
                ['Home Follow Us',        'home-follow-us'],
            ];

            $actions = [
                'view'   => 'View',
                'create' => 'Create',
                'edit'   => 'Edit',
                'delete' => 'Delete',
            ];

            $insertedSlugs = [];

            foreach ($modules as [$moduleLabel, $prefix]) {
                foreach ($actions as $actionSlug => $actionLabel) {
                    $slug = $prefix.'.'.$actionSlug;
                    Permission::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name'   => $actionLabel.' '.$moduleLabel,
                            'module' => $moduleLabel,
                        ]
                    );
                    $insertedSlugs[] = $slug;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Role assignments
            |--------------------------------------------------------------------------
            | superadmin: sync all permissions (hasPermission() also short-circuits
            |             to true via isSuperAdmin(), so this is for transparency).
            | admin     : keep existing + add VIEW, CREATE, EDIT on every new module
            |             (no DELETE — that stays with superadmin).
            | user      : no module access (already has dashboard.view only).
            */

            $allPermissionIds = Permission::pluck('id');

            // Super Admin → everything
            $superadmin = Role::where('slug', Role::SUPERADMIN_SLUG)->first();
            if ($superadmin) {
                $superadmin->permissions()->sync($allPermissionIds);
            }

            // Admin → existing + module view/create/edit (no delete on new modules)
            $admin = Role::where('slug', 'admin')->first();
            if ($admin) {
                $newAdminSlugs = [];
                foreach ($modules as [, $prefix]) {
                    $newAdminSlugs[] = $prefix.'.view';
                    $newAdminSlugs[] = $prefix.'.create';
                    $newAdminSlugs[] = $prefix.'.edit';
                }
                $existingAdminIds  = $admin->permissions()->pluck('permissions.id')->all();
                $newAdminIds       = Permission::whereIn('slug', $newAdminSlugs)->pluck('id')->all();
                $mergedAdminIds    = array_unique(array_merge($existingAdminIds, $newAdminIds));
                $admin->permissions()->sync($mergedAdminIds);
            }

            // Standard user → unchanged (still dashboard.view only)
        });
    }
}
