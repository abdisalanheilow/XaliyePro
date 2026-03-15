<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Store;
use App\Models\StoreItemStock;
use App\Models\Unit;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Customers
        $customers = [
            ['customer_id' => 'CUS-001', 'name' => 'Acme Corp', 'email' => 'sales@acme.com', 'type' => 'company', 'payment_terms' => 'net_30'],
            ['customer_id' => 'CUS-002', 'name' => 'John Doe', 'email' => 'john@example.com', 'type' => 'individual', 'payment_terms' => 'due_on_receipt'],
            ['customer_id' => 'CUS-003', 'name' => 'Global Tech Solutions', 'email' => 'procurement@globaltech.com', 'type' => 'company', 'payment_terms' => 'net_15'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(['customer_id' => $c['customer_id']], $c);
        }

        // 2. Vendors
        $vendors = [
            ['vendor_id' => 'VEN-001', 'name' => 'Tech Supply Ltd', 'email' => 'orders@techsupply.com', 'type' => 'company'],
            ['vendor_id' => 'VEN-002', 'name' => 'Furniture World', 'email' => 'billing@furnitureworld.com', 'type' => 'company'],
        ];

        foreach ($vendors as $v) {
            Vendor::firstOrCreate(['vendor_id' => $v['vendor_id']], $v);
        }

        // 3. Items
        /** @var \App\Models\Category $catElectronics */
        $catElectronics = Category::where('name', 'Electronics')->first();
        /** @var \App\Models\Unit $unitEach */
        $unitEach = Unit::where('short_name', 'pcs')->first();
        /** @var \App\Models\Brand $brandApple */
        $brandApple = Brand::where('name', 'Apple')->first();
        /** @var \App\Models\Brand $brandDell */
        $brandDell = Brand::where('name', 'Dell')->first();

        /** @var \App\Models\Category $catConsulting */
        $catConsulting = Category::where('name', 'Consulting Services')->first();
        /** @var \App\Models\Unit $unitHr */
        $unitHr = Unit::where('short_name', 'hr')->first();

        $items = [
            [
                'name' => 'MacBook Pro 14"',
                'slug' => 'macbook-pro-14',
                'sku' => 'APP-MBP-14',
                'category_id' => $catElectronics->id,
                'unit_id' => $unitEach->id,
                'brand_id' => $brandApple->id,
                'type' => 'product',
                'cost_price' => 1500.00,
                'selling_price' => 1999.00,
                'track_inventory' => true,
                'current_stock' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Dell UltraSharp 27"',
                'slug' => 'dell-ultrasharp-27',
                'sku' => 'DEL-U27-MON',
                'category_id' => $catElectronics->id,
                'unit_id' => $unitEach->id,
                'brand_id' => $brandDell->id,
                'type' => 'product',
                'cost_price' => 450.00,
                'selling_price' => 699.00,
                'track_inventory' => true,
                'current_stock' => 25,
                'status' => 'active',
            ],
            [
                'name' => 'Laravel Development Service',
                'slug' => 'laravel-dev-service',
                'sku' => 'SRV-DEV-PHP',
                'category_id' => $catConsulting->id,
                'unit_id' => $unitHr->id,
                'type' => 'service',
                'cost_price' => 0.00,
                'selling_price' => 100.00,
                'track_inventory' => false,
                'status' => 'active',
            ],
        ];

        foreach ($items as $i) {
            Item::firstOrCreate(['sku' => $i['sku']], $i);
        }

        // 4. Initial Stock Seeding for Central Warehouse
        /** @var \App\Models\Store $mainStore */
        $mainStore = Store::where('name', 'Central Warehouse')->first();
        if ($mainStore) {
            $seededItems = Item::where('track_inventory', true)->get();
            foreach ($seededItems as $item) {
                StoreItemStock::updateOrCreate(
                    ['store_id' => $mainStore->id, 'item_id' => $item->id],
                    ['current_stock' => $item->current_stock, 'reorder_level' => 5]
                );
            }
        }
    }
}
