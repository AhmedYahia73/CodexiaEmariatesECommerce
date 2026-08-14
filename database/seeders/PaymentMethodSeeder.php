<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => ['en' => 'Cash on Delivery', 'ar' => 'الدفع عند الاستلام'],
                'description' => ['en' => 'Pay with cash when your order arrives', 'ar' => 'ادفع نقداً عند وصول طلبك'],
                'icon' => 'icons/cash.png',
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Credit Card', 'ar' => 'بطاقة ائتمان'],
                'description' => ['en' => 'Pay securely with Visa or Mastercard', 'ar' => 'ادفع بأمان باستخدام فيزا أو ماستركارد'],
                'icon' => 'icons/credit_card.png',
                'status' => 1,
            ],
            [
                'name' => ['en' => 'Vodafone Cash', 'ar' => 'فودافون كاش'],
                'description' => ['en' => 'Pay using Vodafone Cash wallet', 'ar' => 'ادفع باستخدام محفظة فودافون كاش'],
                'icon' => 'icons/vodafone.png',
                'status' => 1,
            ],
            [
                'name' => ['en' => 'InstaPay', 'ar' => 'انستاباي'],
                'description' => ['en' => 'Pay using InstaPay app', 'ar' => 'ادفع باستخدام تطبيق انستاباي'],
                'icon' => 'icons/instapay.png',
                'status' => 1,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
