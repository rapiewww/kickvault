<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Demo Admin, Staff, dan Customer
        User::updateOrCreate(
            ['email' => 'admin@runnersvault.com'],
            [
                'name' => 'Admin Owner',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@runnersvault.com'],
            [
                'name' => 'Staff Kasir',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@runnersvault.com'],
            [
                'name' => 'Pelanggan Runner',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // 2. Buat Kategori
        $catShoes = Category::create(['name' => 'Running Shoes']);
        $catWatch = Category::create(['name' => 'Sportswatch']);

        // 3. Buat Supplier
        Supplier::create([
            'name' => 'PT Ortus Indonesia',
            'phone' => '081234567890',
            'email' => 'distributor@ortuseight.com',
        ]);

        Supplier::create([
            'name' => 'Global Sportsindo',
            'phone' => '089876543210',
            'email' => 'sales@globalsports.com',
        ]);

        // 4. Buat Produk Perlengkapan Lari
        Product::create([
            'category_id' => $catShoes->id,
            'sku' => 'SHO-ORT-HYP3',
            'name' => 'Ortuseight Hyperblast 3.0',
            'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=600&q=80',
            'purchase_price' => 450000,
            'selling_price' => 600000,
            'stock' => 20,
            'minimum_stock' => 5,
        ]);

        Product::create([
            'category_id' => $catShoes->id,
            'sku' => 'SHO-ORT-HYG4',
            'name' => 'Ortuseight Hyperglide 4.0',
            'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=600&q=80',
            'purchase_price' => 380000,
            'selling_price' => 499000,
            'stock' => 15,
            'minimum_stock' => 3,
        ]);

        Product::create([
            'category_id' => $catShoes->id,
            'sku' => 'SHO-NIK-ALP3',
            'name' => 'Nike Alphafly 3',
            'image' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=600&q=80',
            'purchase_price' => 3800000,
            'selling_price' => 4599000,
            'stock' => 8,
            'minimum_stock' => 2,
        ]);

        Product::create([
            'category_id' => $catShoes->id,
            'sku' => 'SHO-PUM-DEV',
            'name' => 'Puma Deviate Nitro',
            'image' => 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?auto=format&fit=crop&w=600&q=80',
            'purchase_price' => 1800000,
            'selling_price' => 2299000,
            'stock' => 12,
            'minimum_stock' => 4,
        ]);

        Product::create([
            'category_id' => $catWatch->id,
            'sku' => 'WAT-GAR-FOR',
            'name' => 'Garmin Forerunner',
            'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=600&q=80',
            'purchase_price' => 3500000,
            'selling_price' => 4200000,
            'stock' => 5,
            'minimum_stock' => 2,
        ]);
    }
}
