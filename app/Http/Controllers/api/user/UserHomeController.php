<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;

class UserHomeController extends Controller
{
    public function parent_categories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $categories = Category::
        whereNull("category_id")
        ->where("status", 1)
        ->paginate(15)
        ->through(function ($item) use($lang){
            return [
                "id" => $item->id,
                "name" => $item->name[$lang] ?? null,
                "description" => $item->description[$lang] ?? null,
                "image" => $item->image_url,
            ];
        });
        return response()->json($categories);
    }

    public function sub_categories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            "category_id" => "required|exists:categories,id"
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $categories = Category::
        where("category_id", $request->category_id)
        ->where("status", 1)
        ->paginate(15)
        ->through(function ($item) use($lang){
            return [
                "id" => $item->id,
                "name" => $item->name[$lang] ?? null,
                "description" => $item->description[$lang] ?? null,
                "image" => $item->image_url,
            ];
        });
        return response()->json($categories);
    }

    public function products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            "category_id" => "required|exists:categories,id"
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $products = Product::
        where("category_id", $request->category_id)
        ->where("status", 1)
        ->paginate(15)
        ->through(function ($item) use($lang){
            return [
                "id" => $item->id,
                "name" => $item->name[$lang] ?? null,
                "description" => $item->description[$lang] ?? null,
                "image" => $item->image_url,
                "price" => $item->price,
                "discount" => $item->is_discounted ? $item->discount : 0,
                "final_price" => $item->final_price,
            ];
        });
        return response()->json($products);
    }

    public function all_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar', 
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $products = Product:: 
        where("status", 1)
        ->paginate(15)
        ->through(function ($item) use($lang){
            return [
                "id" => $item->id,
                "name" => $item->name[$lang] ?? null,
                "description" => $item->description[$lang] ?? null,
                "image" => $item->image_url,
                "price" => $item->price,
                "discount" => $item->is_discounted ? $item->discount : 0,
                "final_price" => $item->final_price,
                "category_id" => $item->category_id,
                "category_name" => $item?->category?->name[$lang] ?? null,
            ];
        });
        return response()->json($products);
    }

    public function product_details(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar', 
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $product = Product::with("category", "variations.options")->findOrFail($id);

        $gallery = ProductImage::where("product_id", $id)
            ->get()
            ->map(function ($item) {
                return [
                    "id"    => $item->id,
                    "image" => $item->image_url,
                ];
            });

        return response()->json([
            "product" => [
                "id"          => $product->id,
                "name"        => $product->name[$lang] ?? null,
                "description" => $product->description[$lang] ?? null,
                "image"       => $product->image_url,
                "price"       => $product->price,
                "discount"    => $product->is_discounted ? $product->discount : 0,
                "final_price" => $product->final_price,
                "category"    => $product->category?->name[$lang] ?? null,
                "variations"  => $product->variations->map(function ($element) use ($lang) {
                    return [
                        "id"      => $element->id,
                        "name"    => $element->name[$lang] ?? null,
                        "options" => $element->options->map(function ($value) use ($lang) {
                            return [
                                "id"    => $value->id,
                                "name"  => $value->name[$lang] ?? null,
                                "price" => $value->price,
                            ];
                        }),
                    ];
                }),
            ],
            "gallery" => $gallery,
        ]);
    }


    public function footer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar', 
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $data = Setting::first();
        if ($data) {
            $data->brand_name = $data->brand_name ? $data->brand_name[$lang] : null;
        }

        return response()->json([
            "data" => $data
        ]);
    }
}
