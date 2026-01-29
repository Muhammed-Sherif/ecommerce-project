<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
        ]);

        // 1. Users
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // $customerId = DB::table('users')->insertGetId([
        //     'name' => 'John Doe',
        //     'email' => 'john@example.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'customer',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // // 2. Products
        // $products = [
        //     [
        //         'name' => 'Soma Slim XS Wood Top',
        //         'description' => 'This amazing accent table is hand-crafted with a solid stone base and upper wood frame. Perfect for modern living spaces.',
        //         'price' => 699.00,
        //         'category' => 'Living Room',
        //         'image' => 'https://images.unsplash.com/photo-1463620910506-d0458143143e?auto=format&fit=crop&w=800&q=80',
        //         'images' => json_encode([
        //             'https://images.unsplash.com/photo-1463620910506-d0458143143e?auto=format&fit=crop&w=800&q=80',
        //             'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=800&q=80',
        //         ]),
        //         'features' => json_encode(['Solid stone base', 'Wood frame', 'Modern design']),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Sculpture Coffee Table',
        //         'description' => 'A triumph of minimalist design that combines natural and man-made materials for a stunning centerpiece.',
        //         'price' => 503.10,
        //         'category' => 'Living Room',
        //         'image' => 'https://images.unsplash.com/photo-1615971677499-5467cbab01c0?auto=format&fit=crop&w=800&q=80',
        //         'images' => json_encode(['https://images.unsplash.com/photo-1615971677499-5467cbab01c0?auto=format&fit=crop&w=800&q=80']),
        //         'features' => json_encode(['Minimalist', 'Mixed materials']),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Tuber Large',
        //         'description' => 'A simple, post-modern design that works well with a variety of styles.',
        //         'price' => 113.89,
        //         'category' => 'Decor',
        //         'image' => 'https://images.unsplash.com/photo-1612372606404-0ab33e7187ee?auto=format&fit=crop&w=800&q=80',
        //         'images' => json_encode(['https://images.unsplash.com/photo-1612372606404-0ab33e7187ee?auto=format&fit=crop&w=800&q=80']),
        //         'features' => json_encode(['Post-modern', 'Versatile']),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Soma L All Stone',
        //         'description' => 'Experience modern art with this beautiful mid-century table.',
        //         'price' => 237.00,
        //         'category' => 'Living Room',
        //         'image' => 'https://images.unsplash.com/photo-1628744876497-eb30460be9f6?auto=format&fit=crop&w=800&q=80',
        //         'images' => json_encode(['https://images.unsplash.com/photo-1628744876497-eb30460be9f6?auto=format&fit=crop&w=800&q=80']),
        //         'features' => json_encode(['Mid-century', 'All stone']),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Ergo Office Chair',
        //         'description' => 'Ergonomic design for maximum comfort during long work hours.',
        //         'price' => 350.00,
        //         'category' => 'Office',
        //         'image' => 'https://images.unsplash.com/photo-1505843490538-5133c6c7d0e1?auto=format&fit=crop&w=800&q=80',
        //         'images' => json_encode(['https://images.unsplash.com/photo-1505843490538-5133c6c7d0e1?auto=format&fit=crop&w=800&q=80']),
        //         'features' => json_encode(['Ergonomic', 'Comfortable']),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'name' => 'Modern Kitchen Island',
        //         'description' => 'Sleek and functional kitchen island with storage.',
        //         'price' => 1200.00,
        //         'category' => 'Kitchen',
        //         'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=800&q=80',
        //         'images' => json_encode(['https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=800&q=80']),
        //         'features' => json_encode(['Storage', 'Sleek']),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]
        // ];

        // foreach ($products as $p) {
        //     $prodId = DB::table('products')->insertGetId($p);
            
        //     // 3. Inventory for each product
        //     DB::table('inventory')->insert([
        //         'product_id' => $prodId,
        //         'quantity' => rand(10, 50),
        //         'reserved_quantity' => rand(0, 5),
        //         'warehouse_location' => 'WH-' . rand(1, 5),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);

        //     // 4. Reviews (Randomly)
        //     if (rand(0, 1)) {
        //         DB::table('reviews')->insert([
        //             'product_id' => $prodId,
                        // user_name' => 'John Doe',
        //             'user_id' => $customerId,
        //             'rating' => rand(4, 5),
        //             'title' => 'Great product!',
        //             'comment' => 'Really loved the quality and finish.',
        //             'status' => 'approved',
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);
        //     }
        // }
    }
}
