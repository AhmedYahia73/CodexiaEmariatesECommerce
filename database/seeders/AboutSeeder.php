<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    public function run(): void
    {
        About::create([
            'title' => [
                'en' => 'About Us',
                'ar' => 'من نحن',
            ],
            'content' => [
                'en' => 'We are a leading e-commerce platform dedicated to providing the best shopping experience. Founded with a passion for quality and customer satisfaction, we offer a wide range of products at competitive prices. Our team works around the clock to ensure fast delivery and excellent support for every customer.',
                'ar' => 'نحن منصة تجارة إلكترونية رائدة مكرسة لتقديم أفضل تجربة تسوق. تأسسنا بشغف نحو الجودة ورضا العملاء، ونقدم مجموعة واسعة من المنتجات بأسعار تنافسية. يعمل فريقنا على مدار الساعة لضمان التوصيل السريع والدعم الممتاز لكل عميل.',
            ],
            'image' => 'about/about.png',
        ]);
    }
}
