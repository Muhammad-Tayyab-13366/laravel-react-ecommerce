<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mobile Phones',
                'department_id' => 1,
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptops',
                'department_id' => 1,
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Men\'s Clothing',
                'department_id' => 2,
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Women\'s Clothing',
                'department_id' => 2,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Furniture',
                'department_id' => 3,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kitchen Appliances',
                'department_id' => 3,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fitness Equipment',
                'department_id' => 4,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outdoor Sports',
                'department_id' => 4,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Action Figures',
                'department_id' => 5,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Board Games',         
                'department_id' => 5,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Skincare Products',

                'department_id' => 6,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),  
                'updated_at' => now(),
            ],
            [
                'name' => 'Makeup', 
                'department_id' => 6,
                'is_active' => true,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('categories')->insert($categories);
    }
}
