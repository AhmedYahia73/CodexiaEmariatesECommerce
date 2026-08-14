<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name'        => ['en' => 'Fast Delivery', 'ar' => 'توصيل سريع'],
                'description' => ['en' => 'We deliver your orders within 24 hours to your doorstep.', 'ar' => 'نوصل طلباتك خلال 24 ساعة إلى باب منزلك.'],
                'icon'        => 'services/fast_delivery.png',
            ],
            [
                'name'        => ['en' => 'Secure Payment', 'ar' => 'دفع آمن'],
                'description' => ['en' => 'Your payment is fully protected with the latest encryption technology.', 'ar' => 'مدفوعاتك محمية بالكامل بأحدث تقنيات التشفير.'],
                'icon'        => 'services/secure_payment.png',
            ],
            [
                'name'        => ['en' => '24/7 Support', 'ar' => 'دعم على مدار الساعة'],
                'description' => ['en' => 'Our support team is available around the clock to help you.', 'ar' => 'فريق الدعم لدينا متاح على مدار الساعة لمساعدتك.'],
                'icon'        => 'services/support.png',
            ],
            [
                'name'        => ['en' => 'Easy Returns', 'ar' => 'إرجاع سهل'],
                'description' => ['en' => 'Not satisfied? Return your order within 14 days hassle-free.', 'ar' => 'غير راضٍ؟ أرجع طلبك خلال 14 يومًا بدون أي متاعب.'],
                'icon'        => 'services/returns.png',
            ],
            [
                'name'        => ['en' => 'Best Prices', 'ar' => 'أفضل الأسعار'],
                'description' => ['en' => 'We guarantee the best prices with regular offers and discounts.', 'ar' => 'نضمن أفضل الأسعار مع عروض وخصومات منتظمة.'],
                'icon'        => 'services/best_price.png',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
