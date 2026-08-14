<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'name' => ['en' => 'Welcome Discount', 'ar' => 'خصم الترحيب'],
                'code' => 'WELCOME10',
                'discount' => 10.00,
                'type' => 'precentage',
                'users_count' => 0,
                'usage_limit' => 100,
                'user_usage_limit' => 1,
                'from' => '2026-06-01',
                'to' => '2026-12-31',
                'max_discount' => 50.00,
            ],
            [
                'name' => ['en' => 'Summer Sale', 'ar' => 'تخفيضات الصيف'],
                'code' => 'SUMMER25',
                'discount' => 25.00,
                'type' => 'precentage',
                'users_count' => 0,
                'usage_limit' => 50,
                'user_usage_limit' => 1,
                'from' => '2026-06-21',
                'to' => '2026-09-21',
                'max_discount' => 100.00,
            ],
            [
                'name' => ['en' => 'Fixed 30 Off', 'ar' => 'خصم ثابت 30 جنيه'],
                'code' => 'SAVE30',
                'discount' => 30.00,
                'type' => 'value',
                'users_count' => 0,
                'usage_limit' => 200,
                'user_usage_limit' => 2,
                'from' => '2026-06-01',
                'to' => '2026-08-31',
                'max_discount' => null,
            ],
            [
                'name' => ['en' => 'VIP Coupon', 'ar' => 'كوبون VIP'],
                'code' => 'VIP50',
                'discount' => 50.00,
                'type' => 'precentage',
                'users_count' => 0,
                'usage_limit' => 10,
                'user_usage_limit' => 1,
                'from' => '2026-06-01',
                'to' => '2026-07-31',
                'max_discount' => 200.00,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
