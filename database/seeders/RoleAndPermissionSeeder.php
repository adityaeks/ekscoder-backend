<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // List of permissions grouped by module
        $modulesPermissions = [
            'orders' => [
                'orders.view',
                'orders.create',
                'orders.edit',
                'orders.delete',
                'orders.update-status',
            ],
            'projects' => [
                'projects.view',
                'projects.create',
                'projects.edit',
                'projects.delete',
                'projects.toggle-active',
            ],
            'logs' => [
                'logs.view',
                'logs.clear',
            ],
            'users' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
            ],
            'roles' => [
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
            ],
            'sites' => [
                'sites.view',
                'sites.create',
                'sites.edit',
                'sites.delete',
                'sites.check',
            ],
            'cloudflare' => [
                'cloudflare.view',
                'cloudflare.create',
                'cloudflare.edit',
                'cloudflare.delete',
                'cloudflare.purge',
            ],
        ];

        // Create permissions
        $allPermissionNames = [];
        foreach ($modulesPermissions as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
                $allPermissionNames[] = $permission;
            }
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole      = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $staffRole      = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        // Give permissions to Admin & Staff roles
        $adminRole->syncPermissions($allPermissionNames);
        $staffRole->syncPermissions([
            'orders.view',
            'orders.create',
            'orders.edit',
            'projects.view',
            'logs.view',
        ]);

        // Assign 'Super Admin' role to all current users (or specific admin)
        $adminUser = User::where('email', 'admin@ekscoder.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($superAdminRole);
        }

        // Also assign Super Admin role to any user who doesn't have a role yet
        User::all()->each(function (User $user) use ($superAdminRole) {
            if ($user->roles()->count() === 0) {
                $user->assignRole($superAdminRole);
            }
        });
    }
}
