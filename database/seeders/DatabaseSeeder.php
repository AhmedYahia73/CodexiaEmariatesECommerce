<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CitySeeder::class,
            PaymentMethodSeeder::class,
            CouponSeeder::class,
            AboutSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
