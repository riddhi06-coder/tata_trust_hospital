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
            | Each entry => [ 'Sub-item label', 'slug-prefix', [actions], 'Group heading'? ]
            |   - 'Sub-item label' builds the permission name ("View {label}", ...).
            |   - 'slug-prefix' builds the slug ("{prefix}.{action}") — NEVER rename,
            |     route middleware + sidebar reference these.
            |   - optional 4th element is the matrix card heading (the "module"). When
            |     omitted it defaults to the label (a standalone card).
            |
            | Headings mirror the admin sidebar so a role's access is easy to read:
            | multi-tab sidebar sections (Home, Appointments, Specialities, Join Us,
            | Blog, Form Enquiries) become ONE card each; single tabs are their own card.
            |
            | Read-only modules (enquiries, reports, appointment users) only get
            | 'view'. Appointments get view + edit (status changes). Blog comments
            | get view/edit/delete (no create).
            */
            $modules = [
                // Masters (standalone sidebar tabs)
                ['Our Team',              'our-team',              ['view', 'create', 'edit', 'delete']],
                ['Testimonials',          'testimonials',          ['view', 'create', 'edit', 'delete']],
                ['Our Facilities',        'master-facilities',     ['view', 'create', 'edit', 'delete']],

                // Home page sections -> "Home" card (row labels = sidebar sub-tabs)
                ['Banner Details',        'home-banners',          ['view', 'create', 'edit', 'delete'], 'Home'],
                ['Short Introduction',    'home-short-intro',      ['view', 'create', 'edit', 'delete'], 'Home'],
                ['Services',              'home-specialities',     ['view', 'create', 'edit', 'delete'], 'Home'],
                ['Facilities',            'home-facilities',       ['view', 'create', 'edit', 'delete'], 'Home'],
                ['Our Team',              'home-our-team',         ['view', 'create', 'edit', 'delete'], 'Home'],
                ['Testimonials',          'home-testimonials',     ['view', 'create', 'edit', 'delete'], 'Home'],
                ['Our Board',             'home-board',            ['view', 'create', 'edit', 'delete'], 'Home'],
                ['Follow Us',             'home-follow-us',        ['view', 'create', 'edit', 'delete'], 'Home'],

                // Specialities -> "Specialities" card
                ['Listing',               'specialities',          ['view', 'create', 'edit', 'delete'], 'Specialities'],
                ['Details',               'speciality-details',    ['view', 'create', 'edit', 'delete'], 'Specialities'],

                // Standalone content tabs
                ['About Us',              'about-us',              ['view', 'create', 'edit', 'delete']],
                ['Gallery',               'gallery',               ['view', 'create', 'edit', 'delete']],
                ['Events',                'events',                ['view', 'create', 'edit', 'delete']],
                ['FAQs',                  'faqs',                  ['view', 'create', 'edit', 'delete']],
                ['Contact Details',       'contact-details',       ['view', 'create', 'edit', 'delete']],
                ['Privacy Policy',        'privacy-policy',        ['view', 'create', 'edit', 'delete']],

                // Join Us -> "Join Us" card
                ['Page Details',          'join-page',             ['view', 'create', 'edit', 'delete'], 'Join Us'],
                ['Job Postings',          'job-roles',             ['view', 'create', 'edit', 'delete'], 'Join Us'],

                // Blog -> "Blog" card
                ['Category',              'blog-categories',       ['view', 'create', 'edit', 'delete'], 'Blog'],
                ['Listing',               'blog-listings',         ['view', 'create', 'edit', 'delete'], 'Blog'],
                ['Details',               'blog-details',          ['view', 'create', 'edit', 'delete'], 'Blog'],
                ['Comments',              'blog-comments',         ['view', 'edit', 'delete'],           'Blog'],

                // Appointments -> "Appointments" card
                ['Appointment Users',     'appointment-users',     ['view'],                             'Appointments'],
                ['Appointments',          'appointments',          ['view', 'edit'],                     'Appointments'],
                ['Manage Statuses',       'appointment-statuses',  ['view', 'create', 'edit', 'delete'], 'Appointments'],

                // Form Enquiries -> "Form Enquiries" card
                ['Contact Enquiries',     'contact-enquiries',     ['view'],                             'Form Enquiries'],
                ['Job Applications',      'job-applications',      ['view'],                             'Form Enquiries'],
                ['Appointment Enquiries', 'appointment-enquiries', ['view'],                             'Form Enquiries'],

                // Reports (standalone sidebar tab)
                ['Reports',               'reports',               ['view']],
            ];

            $actionLabels = [
                'view'   => 'View',
                'create' => 'Create',
                'edit'   => 'Edit',
                'delete' => 'Delete',
            ];

            foreach ($modules as $module) {
                [$moduleLabel, $prefix, $actions] = $module;
                // Optional 4th element = matrix group heading (defaults to the label).
                $group = $module[3] ?? $moduleLabel;

                foreach ($actions as $action) {
                    Permission::updateOrCreate(
                        ['slug' => $prefix.'.'.$action],
                        [
                            'name'   => $actionLabels[$action].' '.$moduleLabel,
                            'module' => $group,
                        ]
                    );
                }
            }

            // Align the system permissions (seeded by RolePermissionSeeder as
            // separate "Roles"/"Users"/"Permissions" cards) under one "User" card,
            // matching the sidebar's User section. Only relabels the heading —
            // slugs and role assignments are untouched.
            Permission::whereIn('module', ['Roles', 'Users', 'Permissions'])
                ->update(['module' => 'User']);

            /*
            |--------------------------------------------------------------------------
            | Role assignments
            |--------------------------------------------------------------------------
            | superadmin: all permissions (hasPermission() also short-circuits to true
            |             via isSuperAdmin(); stored here for transparency).
            | admin     : keep existing + every non-delete action on every module
            |             (delete stays with superadmin).
            | user      : unchanged (dashboard.view only).
            */

            // Super Admin -> everything
            $superadmin = Role::where('slug', Role::SUPERADMIN_SLUG)->first();
            if ($superadmin) {
                $superadmin->permissions()->sync(Permission::pluck('id'));
            }

            // Admin -> existing + view/create/edit on every module (no delete)
            $admin = Role::where('slug', 'admin')->first();
            if ($admin) {
                $adminSlugs = [];
                foreach ($modules as [, $prefix, $actions]) {
                    foreach ($actions as $action) {
                        if ($action !== 'delete') {
                            $adminSlugs[] = $prefix.'.'.$action;
                        }
                    }
                }

                $existingAdminIds = $admin->permissions()->pluck('permissions.id')->all();
                $newAdminIds      = Permission::whereIn('slug', $adminSlugs)->pluck('id')->all();
                $mergedAdminIds   = array_unique(array_merge($existingAdminIds, $newAdminIds));
                $admin->permissions()->sync($mergedAdminIds);
            }

            // Standard user -> unchanged (still dashboard.view only)
        });
    }
}
