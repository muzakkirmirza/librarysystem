<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ItemCategory;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        User::create([
            'name' => 'Staff Admin',
            'email' => 'staff@campus.edu',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Ali Student',
            'email' => 'student@campus.edu',
            'password' => Hash::make('password123'),
        ]);

        
        $cat1 = ItemCategory::create([
            'name' => 'Laptops & Devices', 
            'description' => 'Lab laptops and tablets for checkout'
        ]);
        
        $cat2 = ItemCategory::create([
            'name' => 'Lab Instruments', 
            'description' => 'Oscilloscopes, Arduino kits and tools'
        ]);

        
        InventoryItem::create([
            'category_id' => $cat1->id,
            'name' => 'Dell Latitude Laptop #1',
            'condition' => 'Good',
            'is_available' => true,
        ]);

        InventoryItem::create([
            'category_id' => $cat2->id,
            'name' => 'Arduino Starter Kit #1',
            'condition' => 'Good',
            'is_available' => true,
        ]);

        InventoryItem::create([
            'category_id' => $cat2->id,
            'name' => 'Digital Oscilloscope #1',
            'condition' => 'Needs Repair',
            'is_available' => false,
        ]);
    }
}