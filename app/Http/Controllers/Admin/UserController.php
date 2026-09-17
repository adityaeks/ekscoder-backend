<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('roles', 'permissions')->latest()->get();

        $stats = [
            'total' => $users->count(),
            'super_admins' => $users->filter(fn($u) => $u->hasRole('Super Admin'))->count(),
            'admins' => $users->filter(fn($u) => $u->hasRole('Admin'))->count(),
            'staff' => $users->filter(fn($u) => $u->hasRole('Staff'))->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        $permissions = Permission::all();
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = count($parts) > 1 ? ucfirst($parts[0]) : 'General';
            $groupedPermissions[$module][] = $permission;
        }

        $userRole = $user->getRoleNames()->first();
        $userDirectPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'groupedPermissions', 'userRole', 'userDirectPermissions'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::defaults()],
            'role' => 'required|string|exists:roles,name',
            'direct_permissions' => 'nullable|array',
            'direct_permissions.*' => 'string|exists:permissions,name',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync Role
        $user->syncRoles([$validated['role']]);

        // Sync Direct Permissions
        $user->syncPermissions($request->direct_permissions ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Impersonate into the selected user account.
     */
    public function impersonate(User $user)
    {
        $currentUser = Auth::user();

        // Check permission
        if (!$currentUser || (!$currentUser->hasRole('Super Admin') && !$currentUser->can('users.edit'))) {
            abort(403, 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        // Prevent self-impersonation
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Anda tidak dapat masuk sebagai akun Anda sendiri.');
        }

        // Store original admin ID in session
        session()->put('impersonator_id', $currentUser->id);

        // Record UserLog audit
        UserLog::log(
            action: 'login',
            module: 'User Management',
            description: "Admin {$currentUser->name} masuk sebagai pengguna: {$user->name} ({$user->email})",
            model: $user
        );

        // Login as target user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Berhasil masuk sebagai {$user->name}. Menu & dashboard disesuaikan dengan hak akses akun ini.");
    }

    /**
     * Leave impersonation mode and return to original admin account.
     */
    public function leaveImpersonation()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $impersonatorId = session()->pull('impersonator_id');
        $impersonator = User::find($impersonatorId);

        if (!$impersonator) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Sesi admin tidak ditemukan. Silakan login kembali.');
        }

        $impersonatedUser = Auth::user();

        // Record UserLog audit
        UserLog::log(
            action: 'logout',
            module: 'User Management',
            description: "Admin {$impersonator->name} kembali dari mode masuk pengguna: " . ($impersonatedUser ? "{$impersonatedUser->name} ({$impersonatedUser->email})" : "Unknown"),
            model: $impersonatedUser
        );

        // Login back as admin
        Auth::login($impersonator);

        return redirect()->route('admin.users.index')->with('success', "Kembali ke akun Admin ({$impersonator->name}).");
    }
}
