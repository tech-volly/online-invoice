<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();

        $categories = [
            ['name' => 'Eloctrinics', 'is_status' => 1],
            ['name' => 'Clothing & Accessories', 'is_status' => 1],
            ['name' => 'Furniture', 'is_status' => 1],
            ['name' => 'Toys & Games', 'is_status' => 1],
            ['name' => 'Health & Personal Care', 'is_status' => 1],
            ['name' => 'Home & Kitchen', 'is_status' => 1]

        ];

        foreach ($categories as $key => $value) {
            Category::create($value);
        }

    }
}
