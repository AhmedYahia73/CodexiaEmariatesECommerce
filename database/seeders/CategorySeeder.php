<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'description' => ['en' => 'All electronic devices and accessories', 'ar' => 'جميع الأجهزة الإلكترونية والملحقات'],
                'image' => 'categories/electronics.png',
                'category_id' => null,
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Clothing', 'ar' => 'ملابس'],
                'description' => ['en' => 'Men and women clothing', 'ar' => 'ملابس رجالية ونسائية'],
                'image' => 'categories/clothing.png',
                'category_id' => null,
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Food & Beverages', 'ar' => 'طعام ومشروبات'],
                'description' => ['en' => 'Fresh food and drinks', 'ar' => 'طعام ومشروبات طازجة'],
                'image' => 'categories/food.png',
                'category_id' => null,
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Phones', 'ar' => 'هواتف'],
                'description' => ['en' => 'Smartphones and accessories', 'ar' => 'الهواتف الذكية والملحقات'],
                'image' => 'categories/phones.png',
                'category_id' => 1, // sub of Electronics
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Laptops', 'ar' => 'لاب توب'],
                'description' => ['en' => 'Laptops and computers', 'ar' => 'أجهزة اللاب توب والكمبيوتر'],
                'image' => 'categories/laptops.png',
                'category_id' => 1, // sub of Electronics
                'status' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
