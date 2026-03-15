<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CoreERPSystemSeeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class DebugSeeder
{
    use WithoutModelEvents;

    public function run()
    {
        // ... same logic
    }
}

try {
    Event::fake(); // Similar effect to WithoutModelEvents
    echo "Running Permission seeding...\n";
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

    echo "Running Role seeding...\n";
    /** @var \App\Models\Role $adminRole */
    $adminRole = Role::firstOrCreate(
        ['name' => 'Administrator'],
        [
            'description' => 'Full system access across all branches and stores',
            'branch_access_type' => 'all',
        ]
    );

    echo "Running User seeding...\n";
    User::firstOrCreate(
        ['email' => 'admin@xaliyepro.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => 'Active',
            'view_all_branches' => true,
        ]
    );

    echo "Running CoreERPSystemSeeder...\n";
    $seeder1 = new CoreERPSystemSeeder;
    $seeder1->run();
    echo "Running DemoDataSeeder...\n";
    $seeder2 = new DemoDataSeeder;
    $seeder2->run();
    echo "SUCCESS\n";
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo 'FILE: '.$e->getFile()."\n";
    echo 'LINE: '.$e->getLine()."\n";
    if (isset($e->errorInfo)) {
        print_r($e->errorInfo);
    }
}
