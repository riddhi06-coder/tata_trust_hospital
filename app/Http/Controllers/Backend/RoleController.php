<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id')->get();
        return view('backend.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('backend.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        Role::create([
            'name'         => $validated['name'],
            'slug'         => $this->generateUniqueSlug($validated['name']),
            'description'  => $validated['description'] ?? null,
            'is_protected' => false,
            'is_active'    => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.roles.index')->with('message', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('backend.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $role->name        = $validated['name'];
        $role->description = $validated['description'] ?? null;

        // Slug stays stable; superadmin can never be deactivated
        if (! $role->is_protected) {
            $role->is_active = $request->boolean('is_active');
        }

        $role->save();

        return redirect()->route('admin.roles.index')->with('message', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_protected) {
            return back()->with('message', 'This role is protected and cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return back()->with('message', 'Cannot delete a role that still has users assigned.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('message', 'Role deleted successfully.');
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;
        while (Role::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }
        return $slug;
    }
}
