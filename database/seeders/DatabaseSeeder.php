<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Permissions
        $modules = [
            'Sales' => ['view', 'create', 'edit', 'delete', 'approve', 'return'],
            'Purchases' => ['view', 'create', 'edit', 'delete', 'receive'],
            'Stock' => ['view', 'adjust', 'transfer', 'audit'],
            'Finance' => ['view', 'manage_payments', 'reports'],
            'Employees' => ['view', 'create', 'edit', 'delete'],
            'Settings' => ['view', 'manage_company', 'manage_branches', 'manage_users'],
        ];

        foreach ($modules as $module => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate([
                    'name' => $perm,
                    'module' => $module,
                ]);
            }
        }

        // 2. Create Roles
        /** @var \App\Models\Role $adminRole */
        $adminRole = Role::firstOrCreate(
            ['name' => 'Administrator'],
            [
                'description' => 'Full system access across all branches and stores',
                'branch_access_type' => 'all',
            ]
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'Branch Manager'],
            [
                'description' => 'Manage all operations within assigned branch(es)',
                'branch_access_type' => 'restricted',
            ]
        );

        // Sync all permissions to Admin
        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions */
        $permissions = Permission::all();
        $adminRole->permissions()->sync($permissions->pluck('id'));

        // 3. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@XaliyePro.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'status' => 'Active',
                'view_all_branches' => true,
            ]
        );

        $this->call([
            AccountsSeeder::class,
            CoreERPSystemSeeder::class,
            DemoDataSeeder::class,
            PurchaseBillSeeder::class,
        ]);
    }
}
