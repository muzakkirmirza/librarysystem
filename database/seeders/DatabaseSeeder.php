<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users Banayein (Bina Duplicate Error Ke)
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]
        );

        // 2. Categories Banayein
        $cat1 = Category::firstOrCreate(['name' => 'Laptops']);
        $cat2 = Category::firstOrCreate(['name' => 'Hardware Kits']);

        // 3. Inventory Items Banayein
        InventoryItem::firstOrCreate(
            ['name' => 'Dell Latitude Laptop'],
            [
                'category_id' => $cat1->id,
                'condition' => 'Good',
                'is_available' => true,
            ]
        );

        InventoryItem::firstOrCreate(
            ['name' => 'Arduino Starter Kit'],
            [
                'category_id' => $cat2->id,
                'condition' => 'Good',
                'is_available' => true,
            ]
        );
    }
}