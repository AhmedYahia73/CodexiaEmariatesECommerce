<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::paginate(15);
        return response()->json($coupons);
    }

    // Simple list for dropdowns (select coupon_id)
    public function list()
    {
        $coupons = Coupon::select('id', 'name', 'code')->get();
        return response()->json($coupons);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|array',
            'name.en'          => 'required|string',
            'name.ar'          => 'required|string',
            'code'             => 'required|string|unique:coupons,code',
            'discount'         => 'required|numeric|min:0',
            'type'             => 'required|in:precentage,value',
            'usage_limit'      => 'nullable|integer|min:1',
            'user_usage_limit' => 'nullable|integer|min:1',
            'from'             => 'nullable|date_format:Y-m-d',
            'to'               => 'nullable|date_format:Y-m-d|after_or_equal:from',
            'max_discount'     => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $coupon = Coupon::create($request->only([
            'name', 'code', 'discount', 'type',
            'usage_limit', 'user_usage_limit', 'from', 'to', 'max_discount',
        ]));

        return response()->json($coupon, 201);
    }

    public function show($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($coupon);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'             => 'nullable|array',
            'name.en'          => 'nullable|string',
            'name.ar'          => 'nullable|string',
            'code'             => 'nullable|string|unique:coupons,code,' . $id,
            'discount'         => 'nullable|numeric|min:0',
            'type'             => 'nullable|in:precentage,value',
            'usage_limit'      => 'nullable|integer|min:1',
            'user_usage_limit' => 'nullable|integer|min:1',
            'from'             => 'nullable|date_format:Y-m-d',
            'to'               => 'nullable|date_format:Y-m-d|after_or_equal:from',
            'max_discount'     => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $coupon->update($request->only([
            'name', 'code', 'discount', 'type',
            'usage_limit', 'user_usage_limit', 'from', 'to', 'max_discount',
        ]));

        return response()->json($coupon);
    }

    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $coupon->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
