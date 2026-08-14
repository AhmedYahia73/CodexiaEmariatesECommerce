<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
                'status' => 1,
                'zones' => [
                    ['name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر'], 'price' => 15.00, 'status' => 1],
                    ['name' => ['en' => 'Heliopolis', 'ar' => 'مصر الجديدة'], 'price' => 20.00, 'status' => 1],
                    ['name' => ['en' => 'Maadi', 'ar' => 'المعادي'], 'price' => 18.00, 'status' => 1],
                    ['name' => ['en' => 'Zamalek', 'ar' => 'الزمالك'], 'price' => 22.00, 'status' => 1],
                ],
            ],
            [
                'name' => ['en' => 'Giza', 'ar' => 'الجيزة'],
                'status' => 1,
                'zones' => [
                    ['name' => ['en' => '6th of October', 'ar' => 'السادس من أكتوبر'], 'price' => 25.00, 'status' => 1],
                    ['name' => ['en' => 'Dokki', 'ar' => 'الدقي'], 'price' => 18.00, 'status' => 1],
                    ['name' => ['en' => 'Haram', 'ar' => 'الهرم'], 'price' => 20.00, 'status' => 1],
                ],
            ],
            [
                'name' => ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
                'status' => 1,
                'zones' => [
                    ['name' => ['en' => 'Smouha', 'ar' => 'سموحة'], 'price' => 30.00, 'status' => 1],
                    ['name' => ['en' => 'Stanley', 'ar' => 'ستانلي'], 'price' => 35.00, 'status' => 1],
                    ['name' => ['en' => 'Montazah', 'ar' => 'المنتزه'], 'price' => 32.00, 'status' => 1],
                ],
            ],
        ];

        foreach ($cities as $cityData) {
            $zones = $cityData['zones'];
            unset($cityData['zones']);

            $city = City::create($cityData);

            foreach ($zones as $zone) {
                Zone::create(array_merge($zone, ['city_id' => $city->id]));
            }
        }
    }
}
