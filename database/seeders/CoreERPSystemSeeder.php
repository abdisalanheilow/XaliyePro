<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class CoreERPSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Branches & Stores
        /** @var \App\Models\Branch $mainBranch */
        $mainBranch = Branch::firstOrCreate(
            ['code' => 'BR-001'],
            [
                'name' => 'Main Headquarters',
                'address' => '123 ERP Plaza',
                'city' => 'Metropolis',
                'state' => 'NY',
                'zip_code' => '10001',
                'phone' => '+1-555-0100',
                'email' => 'hq@XaliyePro.com',
                'status' => 'active',
            ]
        );

        $mainStore = Store::firstOrCreate(
            ['code' => 'CWH-001'],
            [
                'branch_id' => $mainBranch->id,
                'name' => 'Central Warehouse',
                'address' => 'Warehouse Block 1',
                'phone' => '+1-555-0101',
                'status' => 'active',
            ]
        );

        // 2. Units
        $units = [
            ['name' => 'Each', 'short_name' => 'pcs'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Hour', 'short_name' => 'hr'],
        ];

        foreach ($units as $u) {
            Unit::firstOrCreate(['name' => $u['name']], $u);
        }

        // 3. Categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'type' => 'product'],
            ['name' => 'Office Supplies', 'slug' => 'office-supplies', 'type' => 'product'],
            ['name' => 'Raw Materials', 'slug' => 'raw-materials', 'type' => 'product'],
            ['name' => 'Consulting Services', 'slug' => 'consulting-services', 'type' => 'service'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(['name' => $c['name']], $c);
        }

        // 4. Brands
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Dell', 'slug' => 'dell'],
            ['name' => 'Logitech', 'slug' => 'logitech'],
            ['name' => 'Generic', 'slug' => 'generic'],
        ];
        foreach ($brands as $b) {
            Brand::firstOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
