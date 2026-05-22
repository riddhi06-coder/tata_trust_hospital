<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    // ---------------------------------------------------------------
    // Per-role assignment (matrix)
    // ---------------------------------------------------------------
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        return view('backend.permissions.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('module')->orderBy('id')->get()->groupBy('module');
        $assigned    = $role->permissions->pluck('id')->all();

        return view('backend.permissions.edit', compact('role', 'permissions', 'assigned'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_protected) {
            return back()->with('message', 'Permissions for Super Admin cannot be changed (always has full access).');
        }

        $validated = $request->validate([
            'permissions'   => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.permissions.index')->with('message', 'Permissions updated for '.$role->name.'.');
    }

    // ---------------------------------------------------------------
    // Permission catalog CRUD (add new permissions when new tabs are added)
    // ---------------------------------------------------------------
    public function manage()
    {
        $grouped = Permission::orderBy('module')->orderBy('id')->get()->groupBy('module');
        return view('backend.permissions.manage', compact('grouped'));
    }

    public function createPermission()
    {
        return view('backend.permissions.manage-form', [
            'permission' => null,
            'modules'    => Permission::query()->select('module')->distinct()->orderBy('module')->pluck('module'),
        ]);
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'module'      => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        Permission::create([
            'name'        => $validated['name'],
            'module'      => $validated['module'],
            'description' => $validated['description'] ?? null,
            'slug'        => $this->generatePermissionSlug($validated['module'], $validated['name']),
        ]);

        return redirect()->route('admin.permissions.manage')->with('message', 'Permission created. It will now appear in the role-permissions matrix.');
    }

    public function editPermission(Permission $permission)
    {
        return view('backend.permissions.manage-form', [
            'permission' => $permission,
            'modules'    => Permission::query()->select('module')->distinct()->orderBy('module')->pluck('module'),
        ]);
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'module'      => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // Keep the slug stable so middleware references don't break
        $permission->update([
            'name'        => $validated['name'],
            'module'      => $validated['module'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.permissions.manage')->with('message', 'Permission updated. (Slug is preserved so route middleware references keep working.)');
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.manage')->with('message', 'Permission deleted.');
    }

    private function generatePermissionSlug(string $module, string $name): string
    {
        $base = Str::slug($module).'.'.Str::slug($name);
        $slug = $base;
        $i    = 1;
        while (Permission::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }
        return $slug;
    }
}
