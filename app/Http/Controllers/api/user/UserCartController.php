<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Models\CartOption;
use App\Models\CartProduct;
use App\Models\CartVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserCartController extends Controller
{
    // GET /user/cart?local=en
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;

        $cartProducts = CartProduct::with([
            'product',
            'cart_variations.variation',
            'cart_variations.cart_options.option',
        ])
            ->where('user_id', auth()->id())
            ->get()
            ->map(fn($cp) => $this->formatCartProduct($cp, $lang));

        return response()->json(['cart' => $cartProducts]);
    }

    // POST /user/cart
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local'                          => 'required|in:en,ar',
            'product_id'                     => 'required|exists:products,id',
            'count'                          => 'required|integer|min:1',
            'variations'                     => 'sometimes|array',
            'variations.*.variation_id'      => 'required|exists:variations,id',
            'variations.*.option_id'         => 'required|exists:options,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;

        $cartProduct = CartProduct::create([
            'user_id'    => auth()->id(),
            'product_id' => $request->product_id,
            'count'      => $request->count,
        ]);

        foreach ($request->variations ?? [] as $var) {
            $cartVariation = CartVariation::create([
                'cart_product_id' => $cartProduct->id,
                'variation_id'    => $var['variation_id'],
            ]);
            CartOption::create([
                'cart_variation_id' => $cartVariation->id,
                'option_id'         => $var['option_id'],
            ]);
        }

        $cartProduct->load([
            'product',
            'cart_variations.variation',
            'cart_variations.cart_options.option',
        ]);

        return response()->json([
            'message' => 'Added to cart',
            'item'    => $this->formatCartProduct($cartProduct, $lang),
        ], 201);
    }

    // POST /user/cart/{id}  (update count)
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
            'count' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $cartProduct = CartProduct::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $cartProduct->update(['count' => $request->count]);

        $cartProduct->load([
            'product',
            'cart_variations.variation',
            'cart_variations.cart_options.option',
        ]);

        return response()->json([
            'message' => 'Cart updated',
            'item'    => $this->formatCartProduct($cartProduct, $request->local),
        ]);
    }

    // DELETE /user/cart/{id}
    public function destroy($id)
    {
        $cartProduct = CartProduct::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $cartProduct->delete();

        return response()->json(['message' => 'Removed from cart']);
    }

    // DELETE /user/cart  (clear all)
    public function clear()
    {
        CartProduct::where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Cart cleared']);
    }

    // ----------------------------------------------------------------
    private function formatCartProduct(CartProduct $cp, string $lang): array
    {
        $product = $cp->product;

        $optionPrices = $cp->cart_variations
            ->flatMap(fn($cv) => $cv->cart_options)
            ->sum(fn($co) => $co->option?->price ?? 0);

        $basePrice      = $product->price + $optionPrices;
        $finalPrice     = $product->final_price + $optionPrices;
        $isDiscounted   = $product->is_discounted;

        return [
            'cart_product_id' => $cp->id,
            'count'           => $cp->count,
            'product'         => [
                'id'           => $product->id,
                'name'         => $product->name[$lang] ?? $product->name,
                'description'  => $product->description[$lang] ?? $product->description,
                'image'        => $product->image_url,
                'price'        => $basePrice,
                'discount'     => $isDiscounted ? $product->discount : 0,
                'final_price'  => $finalPrice,
                'is_discounted'=> $isDiscounted,
            ],
            'variations' => $cp->cart_variations->map(fn($cv) => [
                'variation_id'   => $cv->variation_id,
                'variation_name' => $cv->variation?->name[$lang] ?? null,
                'selected_option' => $cv->cart_options->map(fn($co) => [
                    'option_id'   => $co->option_id,
                    'option_name' => $co->option?->name[$lang] ?? null,
                    'price'       => $co->option?->price ?? 0,
                ])->first(),
            ])->values(),
        ];
    }
}
