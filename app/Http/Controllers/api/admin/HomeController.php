<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(){
        $products = Product::count();
        $categories = Category::count();
        $users = User::where("role", "user")->count();

        $year = now()->year;

        // عدد الأوردرات + total final_price لكل شهر خلال السنة الحالية
        $monthlyOrders = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(final_price) as orders_total')
            )
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // نبني array كامل لـ 12 شهر حتى لو مفيش أوردرات
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = [
                'month'        => $m,
                'orders_count' => $monthlyOrders->has($m) ? (int) $monthlyOrders[$m]->orders_count : 0,
                'orders_total' => $monthlyOrders->has($m) ? (float) $monthlyOrders[$m]->orders_total : 0.0,
            ];
        }
        $best_products = OrderProduct::
        selectRaw("product_id, sum(count) as products_count")
        ->groupBy("product_id")
        ->whereNotNull("product_id")
        ->with("product")
        ->get()
        ->map(function($item){
            return [
                "id" => $item->id,
                "product" => $item?->product?->name,
                "count" => $item->products_count,
            ];
        });
        return response()->json([
            'products'       => $products,
            'categories'     => $categories,
            'users'          => $users,
            'year'           => $year,
            'monthly_orders' => $months,
            'best_products'  => $best_products,
        ]);
    }
}
