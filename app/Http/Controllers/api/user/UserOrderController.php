<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use App\trait\image;
use Stripe\Stripe;
use Stripe\PaymentIntent;

use App\Models\CartProduct;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderOption;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Coupon;

class UserOrderController extends Controller
{
    use image;

    public function make_order(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_method_id'   => 'sometimes|exists:payment_methods,id',
            'address_id'          => 'required|exists:addresses,id',
            'receipt'             => 'sometimes|image',
            'cart_product_ids'    => 'required|array',
            'cart_product_ids.*'  => 'required|exists:cart_products,id',
            'coupon_code'         => 'sometimes',
            "payment_type"        => "required|in:offline,stripe"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if($request->payment_type == "offline" && !$request->payment_method_id){
            return response()->json([
                "errors" => "payment method is required"
            ], 400);
        }
        if($request->payment_type == "offline" && !$request->receipt){
            return response()->json([
                "errors" => "receipt is required"
            ], 400);
        }
        // تحميل الـ cart items تبع اليوزر فقط
        $cartProducts = CartProduct::with([
            'product',
            'cart_variations.cart_options.option',
        ])
            ->whereIn('id', $request->cart_product_ids)
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartProducts->isEmpty()) {
            return response()->json(['errors' => 'No valid cart items found'], 400);
        }

        $coupon = Coupon::where("code", $request->coupon_code)
            ->where("from", "<=", now())
            ->where("to", ">=", now())
            ->whereColumn("usage_limit", ">", "users_count")
            ->whereHas("users", function($query) {
                $query->where("users.id", auth()->id());
            }, "<", DB::raw('coupons.user_usage_limit'))
            ->first();

        $products       = [];
        $total_price    = 0;
        $total_discount = 0;
        $total_final    = 0;
        $min_order      = Setting::first()?->min_order ?? 0;

        foreach ($cartProducts as $cp) {
            $product = $cp->product;

            $optionIds = $cp->cart_variations
                ->flatMap(fn($cv) => $cv->cart_options->pluck('option_id'))
                ->toArray();

            $option_prices = $cp->cart_variations
                ->flatMap(fn($cv) => $cv->cart_options)
                ->sum(fn($co) => $co->option?->price ?? 0);

            $discount    = $product->is_discounted ? $product->discount : 0;
            $price       = ($product->price + $option_prices) * $cp->count;
            $final_price = ($product->final_price + $option_prices) * $cp->count;

            $total_price    += $price;
            $total_discount += $discount * $cp->count;
            $total_final    += $final_price;

            $products[] = [
                'product_id'  => $product->id,
                'discount'    => $discount,
                'price'       => $price,
                'final_price' => $final_price,
                'count'       => $cp->count,
                'option_ids'  => $optionIds,
            ];
        }

        if ($total_final < $min_order) {
            return response()->json(['errors' => 'Min Order must be ' . $min_order], 400);
        }

        $coupon_discount = 0;
        $coupon_id       = $coupon?->id ?? null;
        $receipt         = null;

        if ($request->hasFile('receipt')) {
            $receipt = $this->upload_image($request, 'receipt', 'orders');
        }

        if ($coupon) {
            $coupon_discount = $coupon->type == "precentage"
                ? $total_final * $coupon->discount / 100
                : $coupon->discount;
            if ($coupon->max_discount && $coupon->max_discount < $coupon_discount) {
                $coupon_discount = $coupon->max_discount;
            }
            $total_final -= $coupon_discount;
            $coupon->users()->attach($request->user()->id);
            $coupon->increment('users_count');
        }

        // ── Stripe payment ────────────────────────────────────────────────
        if ($request->payment_type === 'stripe') {

            // 1. Create order with payment_status = 'faild'
            //    (webhook will change it to 'approve' on success)
            $order = Order::create([
                'price'           => $total_price,
                'discount'        => $total_discount,
                'coupon_discount' => $coupon_discount,
                'coupon_id'       => $coupon_id,
                'user_id'         => $request->user()->id,
                'address_id'      => $request->address_id,
                'final_price'     => $total_final,
                'payment_status'  => 'faild',
                'status'          => 'pending',
            ]);

            // 2. Save order products & options
            foreach ($products as $item) {
                $order_product = OrderProduct::create([
                    'product_id'  => $item['product_id'],
                    'discount'    => $item['discount'],
                    'price'       => $item['price'],
                    'final_price' => $item['final_price'],
                    'order_id'    => $order->id,
                    'count'       => $item['count'],
                ]);
                foreach ($item['option_ids'] as $optionId) {
                    OrderOption::create([
                        'order_product_id' => $order_product->id,
                        'option_id'        => $optionId,
                    ]);
                }
            }

            // 3. Clear cart
            CartProduct::whereIn('id', $request->cart_product_ids)
                ->where('user_id', $request->user()->id)
                ->delete();

            // 4. Create Stripe PaymentIntent
            //    AED smallest unit = fils (1 AED = 100 fils)
            try {
                Stripe::setApiKey(config('services.stripe.secret'));

                $paymentIntent = PaymentIntent::create([
                    'amount'   => (int) round($total_final * 100), // convert to fils
                    'currency' => config('services.stripe.currency', 'aed'),
                    'metadata' => [
                        'order_id' => $order->id,
                        'user_id'  => $request->user()->id,
                    ],
                ]);

                // 5. Save transaction_id in the order
                $order->update(['transaction_id' => $paymentIntent->id]);

            } catch (\Exception $e) {
                // If Stripe fails, delete the order and return error
                $order->order_products()->delete();
                $order->delete();

                Log::error('Stripe PaymentIntent creation failed: ' . $e->getMessage());
                return response()->json([
                    'errors' => 'Payment gateway error. Please try again.',
                ], 500);
            }

            return response()->json([
                'message'            => 'Order created. Complete payment to confirm.',
                'order_id'           => $order->id,
                'client_secret'      => $paymentIntent->client_secret,
                'payment_intent_id'  => $paymentIntent->id,
            ], 201);
        }

        // ── Offline payment ───────────────────────────────────────────────
        $order = Order::create([
            'price'             => $total_price,
            'discount'          => $total_discount,
            'coupon_discount'   => $coupon_discount,
            'coupon_id'         => $coupon_id,
            'payment_method_id' => $request->payment_method_id,
            'user_id'           => $request->user()->id,
            'address_id'        => $request->address_id,
            'final_price'       => $total_final,
            'receipt'           => $receipt,
            'payment_status'    => 'pending',
            'status'            => 'pending',
        ]);

        foreach ($products as $item) {
            $order_product = OrderProduct::create([
                'product_id'  => $item['product_id'],
                'discount'    => $item['discount'],
                'price'       => $item['price'],
                'final_price' => $item['final_price'],
                'order_id'    => $order->id,
                'count'       => $item['count'],
            ]);
            foreach ($item['option_ids'] as $optionId) {
                OrderOption::create([
                    'order_product_id' => $order_product->id,
                    'option_id'        => $optionId,
                ]);
            }
        }

        // حذف الـ cart items اللي اتحولت لأوردر
        CartProduct::whereIn('id', $request->cart_product_ids)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'Order placed successfully', 'order_id' => $order->id], 201);
    }

    public function lists(Request $request){
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $lang = $request->local;
        $payment_methods = PaymentMethod::
        where("status", 1)
        ->get()
        ->map(function($item) use($lang){
            return [
                'name' => $item->name[$lang], 
                'description' => $item->description[$lang], 
                'icon' => $item->icon_url, 
            ]; 
        });

        return response()->json([
            "payment_methods" => $payment_methods
        ]);
    }

    public function order_history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;

        $orders = Order::with('payment_method')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(15)
            ->through(function ($order) use ($lang) {
                return [
                    'id'             => $order->id,
                    'price'          => $order->price,
                    'discount'       => $order->discount,
                    'coupon_discount'=> $order->coupon_discount,
                    'final_price'    => $order->final_price,
                    'payment_status' => $order->payment_status,
                    'status'         => $order->status,
                    'payment_method' => $order->payment_method?->name[$lang] ?? null,
                    'receipt_url'    => $order->receipt_url,
                    'created_at'     => $order->created_at,
                ];
            });

        return response()->json($orders);
    }

    public function order_details(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;

        $order = Order::with([
            'coupon',
            'payment_method',
            'order_products.product',
            'order_products.options.option.variation',
            'address.city',
            'address.zone',
        ])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'id'              => $order->id,
            'price'           => $order->price,
            'discount'        => $order->discount,
            'coupon_discount' => $order->coupon_discount,
            'final_price'     => $order->final_price,
            'receipt_url'     => $order->receipt_url,
            'payment_status'  => $order->payment_status,
            'status'          => $order->status,
            'created_at'      => $order->created_at,
            'coupon'          => $order->coupon ? [
                'id'   => $order->coupon->id,
                'name' => $order->coupon->name[$lang] ?? null,
            ] : null,
            'payment_method'  => $order->payment_method ? [
                'id'   => $order->payment_method->id,
                'name' => $order->payment_method->name[$lang],
                'icon' => $order->payment_method->icon_url,
            ] : null,
            'address'         => $order->address ? [
                'address'         => $order->address->address,
                'floor'           => $order->address->floor,
                'street'          => $order->address->street,
                'building_number' => $order->address->building_number,
                'additional_data' => $order->address->additional_data,
                'lat'             => $order->address->lat,
                'lng'             => $order->address->lng,
                'city'            => $order->address->city?->name[$lang] ?? null,
                'zone'            => $order->address->zone?->name[$lang] ?? null,
            ] : null,
            'order_products'  => $order->order_products->map(function ($op) use ($lang) {
                return [
                    'id'          => $op->id,
                    'price'       => $op->price,
                    'discount'    => $op->discount,
                    'final_price' => $op->final_price,
                    'count'       => $op->count,
                    'product'     => $op->product ? [
                        'id'          => $op->product->id,
                        'name'        => $op->product->name[$lang] ?? null,
                        'description' => $op->product->description[$lang] ?? null,
                        'image'       => $op->product->image_url,
                    ] : null,
                    'options'     => $op->options->map(function ($oo) use ($lang) {
                        return [
                            'id'        => $oo->option?->id,
                            'name'      => $oo->option?->name[$lang] ?? null,
                            'price'     => $oo->option?->price,
                            'variation' => $oo->option?->variation?->name[$lang] ?? null,
                        ];
                    }),
                ];
            }),
        ]);
    }
    

    public function check_coupon(Request $request){
        $validator = Validator::make($request->all(), [
            "coupon_code" => "required",
            "amount" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Rate limit: 3 attempts per 3 minutes per user
        $key = 'check_coupon:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'errors' => 'Too many attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
            ], 429);
        }
        RateLimiter::hit($key, 180); // 180 seconds = 3 minutes

        $coupon = Coupon::where("code", $request->coupon_code)
        ->where("from", "<=", now())
        ->where("to", ">=", now())
        ->whereColumn("usage_limit", ">", "users_count")
        ->whereHas("users", function($query) {
            $query->where("users.id", auth()->id());
        }, "<", DB::raw('coupons.user_usage_limit'))
        ->first();

        if(!$coupon){
            return response()->json([
                "errors" => "code is wrong"
            ], 400);
        }

        // Clear attempts on success
        RateLimiter::clear($key);

        $amount = $request->amount;
        $coupon_discount = $coupon->type == "precentage" ?
        $amount * $coupon->discount / 100 : $coupon->discount;
        $coupon_discount = $coupon->max_discount < $coupon_discount ? $coupon->max_discount
        : $coupon_discount;
        $amount -= $coupon_discount;
        return response()->json([
            "coupon_discount" => $coupon_discount,
            "amount" => $amount,
        ]);
    }
}
