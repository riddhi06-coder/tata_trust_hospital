<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderByDesc('id')->get();
        return view('backend.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        return view('backend.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
            'role_id'  => 'required|exists:roles,id',
        ]);

        $role = Role::find($validated['role_id']);
        if (! $role || ! $role->is_active) {
            return back()->withInput()->withErrors(['role_id' => 'Selected role is not active.']);
        }

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users.index')->with('message', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // Show active roles + the user's current role (even if currently inactive) so the form doesn't break
        $roles = Role::where('is_active', true)
            ->orWhere('id', $user->role_id)
            ->orderBy('name')->get();

        return view('backend.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at')],
            'password' => ['nullable', 'string', 'confirmed', PasswordRule::min(8)],
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user->name    = $validated['name'];
        $user->email   = $validated['email'];
        $user->role_id = $validated['role_id'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Don't let an admin lock themselves out by deactivating their own account
        if ($user->id !== $request->user()->id) {
            $user->is_active = $request->boolean('is_active');
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('message', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('message', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin() && User::whereHas('role', fn ($q) => $q->where('slug', Role::SUPERADMIN_SLUG))->count() <= 1) {
            return back()->with('message', 'Cannot delete the only Super Admin user.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('message', 'User deleted successfully.');
    }
}
