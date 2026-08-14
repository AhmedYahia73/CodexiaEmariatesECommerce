<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variation;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => ['en' => 'iPhone 15 Pro', 'ar' => 'آيفون 15 برو'],
                'description' => ['en' => 'Latest Apple smartphone with titanium design', 'ar' => 'أحدث هاتف ذكي من أبل بتصميم تيتانيوم'],
                'category_id' => 4,
                'image' => 'products/iphone15.png',
                'price' => 999.00,
                'discount' => 50.00,
                'discount_from' => '2026-06-01',
                'discount_to' => '2026-07-01',
                'status' => 1,
                'images' => ['products/iphone15_1.png', 'products/iphone15_2.png'],
                'variations' => [
                    [
                        'name' => ['en' => 'Storage', 'ar' => 'التخزين'],
                        'options' => [
                            ['name' => ['en' => '128 GB', 'ar' => '128 جيجا'], 'price' => 0],
                            ['name' => ['en' => '256 GB', 'ar' => '256 جيجا'], 'price' => 100],
                            ['name' => ['en' => '512 GB', 'ar' => '512 جيجا'], 'price' => 200],
                        ],
                    ],
                    [
                        'name' => ['en' => 'Color', 'ar' => 'اللون'],
                        'options' => [
                            ['name' => ['en' => 'Black', 'ar' => 'أسود'], 'price' => 0],
                            ['name' => ['en' => 'White', 'ar' => 'أبيض'], 'price' => 0],
                            ['name' => ['en' => 'Blue', 'ar' => 'أزرق'], 'price' => 0],
                        ],
                    ],
                ],
            ],
            [
                'name' => ['en' => 'Samsung Galaxy S24', 'ar' => 'سامسونج جالاكسي S24'],
                'description' => ['en' => 'Flagship Samsung phone with AI features', 'ar' => 'هاتف سامسونج الرائد بمميزات الذكاء الاصطناعي'],
                'category_id' => 4,
                'image' => 'products/s24.png',
                'price' => 850.00,
                'discount' => 0,
                'discount_from' => null,
                'discount_to' => null,
                'status' => 1,
                'images' => ['products/s24_1.png'],
                'variations' => [
                    [
                        'name' => ['en' => 'Storage', 'ar' => 'التخزين'],
                        'options' => [
                            ['name' => ['en' => '128 GB', 'ar' => '128 جيجا'], 'price' => 0],
                            ['name' => ['en' => '256 GB', 'ar' => '256 جيجا'], 'price' => 80],
                        ],
                    ],
                ],
            ],
            [
                'name' => ['en' => 'MacBook Pro 14"', 'ar' => 'ماك بوك برو 14 إنش'],
                'description' => ['en' => 'Apple M3 Pro chip with stunning display', 'ar' => 'شريحة Apple M3 Pro مع شاشة مذهلة'],
                'category_id' => 5,
                'image' => 'products/macbook.png',
                'price' => 1999.00,
                'discount' => 100.00,
                'discount_from' => '2026-06-01',
                'discount_to' => '2026-08-01',
                'status' => 1,
                'images' => ['products/macbook_1.png', 'products/macbook_2.png'],
                'variations' => [
                    [
                        'name' => ['en' => 'RAM', 'ar' => 'الرام'],
                        'options' => [
                            ['name' => ['en' => '16 GB', 'ar' => '16 جيجا'], 'price' => 0],
                            ['name' => ['en' => '32 GB', 'ar' => '32 جيجا'], 'price' => 200],
                        ],
                    ],
                ],
            ],
            [
                'name' => ['en' => 'Men\'s T-Shirt', 'ar' => 'تي شيرت رجالي'],
                'description' => ['en' => 'Comfortable cotton t-shirt', 'ar' => 'تي شيرت قطني مريح'],
                'category_id' => 2,
                'image' => 'products/tshirt.png',
                'price' => 25.00,
                'discount' => 5.00,
                'discount_from' => null,
                'discount_to' => null,
                'status' => 1,
                'images' => [],
                'variations' => [
                    [
                        'name' => ['en' => 'Size', 'ar' => 'المقاس'],
                        'options' => [
                            ['name' => ['en' => 'Small', 'ar' => 'صغير'], 'price' => 0],
                            ['name' => ['en' => 'Medium', 'ar' => 'وسط'], 'price' => 0],
                            ['name' => ['en' => 'Large', 'ar' => 'كبير'], 'price' => 0],
                            ['name' => ['en' => 'X-Large', 'ar' => 'كبير جداً'], 'price' => 2],
                        ],
                    ],
                ],
            ],
            [
                'name' => ['en' => 'Fresh Orange Juice', 'ar' => 'عصير برتقال طازج'],
                'description' => ['en' => '100% natural fresh orange juice', 'ar' => 'عصير برتقال طازج 100% طبيعي'],
                'category_id' => 3,
                'image' => 'products/orange_juice.png',
                'price' => 5.00,
                'discount' => 0,
                'discount_from' => null,
                'discount_to' => null,
                'status' => 1,
                'images' => [],
                'variations' => [
                    [
                        'name' => ['en' => 'Size', 'ar' => 'الحجم'],
                        'options' => [
                            ['name' => ['en' => 'Small 250ml', 'ar' => 'صغير 250 مل'], 'price' => 0],
                            ['name' => ['en' => 'Medium 500ml', 'ar' => 'وسط 500 مل'], 'price' => 2],
                            ['name' => ['en' => 'Large 1L', 'ar' => 'كبير 1 لتر'], 'price' => 5],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $images = $productData['images'];
            $variationsData = $productData['variations'];
            unset($productData['images'], $productData['variations']);

            $product = Product::create($productData);

            foreach ($images as $img) {
                ProductImage::create(['product_id' => $product->id, 'image' => $img]);
            }

            foreach ($variationsData as $varData) {
                $options = $varData['options'];
                unset($varData['options']);

                $variation = Variation::create([
                    'product_id' => $product->id,
                    'name' => $varData['name'],
                ]);

                foreach ($options as $opt) {
                    Option::create([
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'name' => $opt['name'],
                        'price' => $opt['price'],
                    ]);
                }
            }
        }
    }
}
