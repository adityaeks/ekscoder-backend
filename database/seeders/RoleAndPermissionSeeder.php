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
            'finance' => [
                'finance.view',
                'finance.manage',
            ],
            'notes' => [
                'notes.view',
                'notes.create',
                'notes.edit',
                'notes.delete',
            ],
            'calendar' => [
                'calendar.view',
                'calendar.create',
                'calendar.edit',
                'calendar.delete',
            ],
            'posts' => [
                'posts.view',
                'posts.create',
                'posts.edit',
                'posts.delete',
                'posts.publish',
            ],
            'ai_chat' => [
                'ai_chat.view',
                'ai_chat.create',
                'ai_chat.delete',
            ],
        ];


        // Seed Default Financial Categories
        \App\Models\FinancialCategory::firstOrCreate(['name' => 'Project Payment', 'type' => 'income'], ['color' => '#10b981']);
        \App\Models\FinancialCategory::firstOrCreate(['name' => 'Lain-lain (Pemasukan)', 'type' => 'income'], ['color' => '#3b82f6']);
        \App\Models\FinancialCategory::firstOrCreate(['name' => 'Server & Hosting', 'type' => 'expense'], ['color' => '#ef4444']);
        \App\Models\FinancialCategory::firstOrCreate(['name' => 'Operasional Kantor', 'type' => 'expense'], ['color' => '#f59e0b']);
        \App\Models\FinancialCategory::firstOrCreate(['name' => 'Tools & Software', 'type' => 'expense'], ['color' => '#8b5cf6']);
        \App\Models\FinancialCategory::firstOrCreate(['name' => 'Gaji & Bonus', 'type' => 'expense'], ['color' => '#ec4899']);

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
            'notes.view',
            'notes.create',
            'notes.edit',
            'notes.delete',
            'calendar.view',
            'calendar.create',
            'calendar.edit',
            'calendar.delete',
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
