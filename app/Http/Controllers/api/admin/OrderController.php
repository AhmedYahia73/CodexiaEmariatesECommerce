<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local'          => 'required|in:en,ar',
            'status'         => 'nullable|in:pending,inprogress,delivered,faild_delivered,return',
            'payment_status' => 'nullable|in:pending,approve,reject',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;

        $orders = Order::with('user', 'payment_method')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->payment_status, fn($q) => $q->where('payment_status', $request->payment_status))
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
                    'user'           => $order->user?->name,
                    'payment_method' => $order->payment_method?->name[$lang],
                    'receipt_url'    => $order->receipt_url,
                ];
            });

        return response()->json($orders);
    }
    
    public function show(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;

        $order = Order::with([
            'user',
            'coupon',
            'payment_method',
            'order_products.product',
            'order_products.options.option.variation',
            'address.city',
            'address.zone',
        ])->find($id);

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
            'coupon'          => $order->coupon ? [
                'id'   => $order->coupon->id,
                'name' => $order->coupon->name[$lang] ?? null,
            ] : null,
            'payment_method'  => $order->payment_method ? [
                'id'   => $order->payment_method->id,
                'name' => $order->payment_method->name[$lang],
                'icon' => $order->payment_method->icon_url,
            ] : null,
            'user'            => $order->user ? [
                'id'    => $order->user->id,
                'name'  => $order->user->name,
                'phone' => $order->user->phone,
                'email' => $order->user->email,
                'image' => $order->user->image_url,
            ] : null,
            'address'         => $order->address ? [
                'address'         => $order->address->address,
                'floor'           => $order->address->floor,
                'street'          => $order->address->street,
                'building_number' => $order->address->building_number,
                'additional_data' => $order->address->additional_data,
                'lat'             => $order->address->lat,
                'lng'             => $order->address->lng,
                'map'             => $order->address->map,
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
    
    public function changePaymentStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,approve,reject',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $order->update(['payment_status' => $request->payment_status]);
        return response()->json(['payment_status' => $order->payment_status]);
    }

    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,inprogress,delivered,faild_delivered,return',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $order->update(['status' => $request->status]);
        return response()->json(['status' => $order->status]);
    } 
}
