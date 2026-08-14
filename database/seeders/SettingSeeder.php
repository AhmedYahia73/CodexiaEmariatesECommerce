<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'brand_name' => ['en' => 'My Store', 'ar' => 'متجري'],
            'logo' => 'settings/logo.png',
            'phone' => '+201000000000',
            'wattsapp' => '+201000000000',
            'email' => 'info@mystore.com',
            'address' => 'Cairo, Egypt',
            'lat' => '30.0444',
            'lng' => '31.2357',
            'facebook' => 'https://facebook.com/mystore',
            'insta' => 'https://instagram.com/mystore',
            'tiktok' => 'https://tiktok.com/@mystore',
            'ios_app' => 'https://apps.apple.com/app/mystore',
            'android_app' => 'https://play.google.com/store/apps/mystore',
            'min_order' => 50.00,
        ]);
    }
}
