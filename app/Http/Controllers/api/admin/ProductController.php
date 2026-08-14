<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variation;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use image;

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $products = Product::with('category')
            ->paginate(15)
            ->through(function ($product) use ($lang) {
                return [
                    'id'            => $product->id,
                    'name'          => $product->name[$lang] ?? null,
                    'description'   => $product->description[$lang] ?? null,
                    'image'         => $product->image_url,
                    'price'         => $product->price,
                    'discount'      => $product->is_discounted ? $product->discount : 0,
                    'final_price'   => $product->final_price,
                    'status'        => $product->status,
                    'category'      => $product->category ? [
                        'id'   => $product->category->id,
                        'name' => $product->category->name[$lang] ?? null,
                    ] : null, 
                ];
            });

        return response()->json($products);
    }

    // List for dropdowns: id + name by local
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $products = Product::select('id', 'name')->where('status', 1)->get()
            ->map(fn($p) => ['id' => $p->id, 'name' => $p->name[$lang] ?? null]);

        return response()->json($products);
    }

    // List of categories for dropdown
    public function categories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $categories = Category::select('id', 'name')->where('status', 1)->get()
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name[$lang] ?? null]);

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|array',
            'name.en'           => 'required|string',
            'name.ar'           => 'required|string',
            'description'       => 'required|array',
            'description.en'    => 'required|string',
            'description.ar'    => 'required|string',
            'category_id'       => 'required|exists:categories,id',
            'image'             => 'required|image',
            'price'             => 'required|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'discount_from'     => 'nullable|date',
            'discount_to'       => 'nullable|date|after_or_equal:discount_from',
            'status'            => 'required|boolean',
            // variations
            'variations'                        => 'nullable|array',
            'variations.*.name'                 => 'required|array',
            'variations.*.name.en'              => 'required|string',
            'variations.*.name.ar'              => 'required|string',
            'variations.*.options'              => 'required|array',
            'variations.*.options.*.name'       => 'required|array',
            'variations.*.options.*.name.en'    => 'required|string',
            'variations.*.options.*.name.ar'    => 'required|string',
            'variations.*.options.*.price'      => 'required|numeric|min:0',
            // gallery
            'gallery'           => 'nullable|array',
            'gallery.*'         => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'description', 'category_id', 'price', 'discount', 'discount_from', 'discount_to', 'status']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->upload_image($request, 'image', 'products');
        }

        $product = Product::create($data);

        // variations + options
        if ($request->has('variations')) {
            foreach ($request->variations as $varData) {
                $variation = Variation::create([
                    'product_id' => $product->id,
                    'name'       => $varData['name'],
                ]);
                if (!empty($varData['options'])) {
                    foreach ($varData['options'] as $optData) {
                        Option::create([
                            'product_id'   => $product->id,
                            'variation_id' => $variation->id,
                            'name'         => $optData['name'],
                            'price'        => $optData['price'],
                        ]);
                    }
                }
            }
        }

        // gallery
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $fakeRequest = new Request();
                $fakeRequest->files->set('file', $file);
                $path = $this->uploadFile_v2($file, 'products/gallery');
                ProductImage::create(['product_id' => $product->id, 'image' => $path]);
            }
        }

        return response()->json($product->load('variations.options', 'gallery'), 201);
    }

    public function show($id)
    {
        $product = Product::with('category', 'variations.options', 'gallery')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            // product fields
            'name'           => 'nullable|array',
            'name.en'        => 'nullable|string',
            'name.ar'        => 'nullable|string',
            'description'    => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'category_id'    => 'nullable|exists:categories,id',
            'image'          => 'nullable|image',
            'price'          => 'nullable|numeric|min:0',
            'discount'       => 'nullable|numeric|min:0',
            'discount_from'  => 'nullable|date',
            'discount_to'    => 'nullable|date|after_or_equal:discount_from',
            'status'         => 'nullable|boolean',
            // variations: each item must have an id to update, or no id to create
            'variations'                        => 'nullable|array',
            'variations.*.id'                   => 'nullable|exists:variations,id',
            'variations.*.name'                 => 'nullable|array',
            'variations.*.name.en'              => 'nullable|string',
            'variations.*.name.ar'              => 'nullable|string',
            'variations.*.delete'               => 'nullable|boolean',
            'variations.*.options'              => 'nullable|array',
            'variations.*.options.*.id'         => 'nullable|exists:options,id',
            'variations.*.options.*.name'       => 'nullable|array',
            'variations.*.options.*.name.en'    => 'nullable|string',
            'variations.*.options.*.name.ar'    => 'nullable|string',
            'variations.*.options.*.price'      => 'nullable|numeric|min:0',
            'variations.*.options.*.delete'     => 'nullable|boolean',
            // gallery
            'gallery_delete'    => 'nullable|array',
            'gallery_delete.*'  => 'exists:product_images,id',
            'gallery_add'       => 'nullable|array',
            'gallery_add.*'     => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // --- update product ---
        $data = $request->only(['name', 'description', 'category_id', 'price', 'discount', 'discount_from', 'discount_to', 'status']);
        if ($request->hasFile('image')) {
            $data['image'] = $this->update_image_v2($request, $product->image, 'image', 'products');
        }
        $product->update($data);

        // --- variations ---
        if ($request->has('variations')) {
            foreach ($request->variations as $varData) {
                // delete variation
                if (!empty($varData['delete']) && !empty($varData['id'])) {
                    Variation::find($varData['id'])?->delete();
                    continue;
                }
                // update existing variation
                if (!empty($varData['id'])) {
                    $variation = Variation::find($varData['id']);
                    if ($variation && !empty($varData['name'])) {
                        $variation->update(['name' => $varData['name']]);
                    }
                } else {
                    // create new variation
                    $variation = Variation::create([
                        'product_id' => $product->id,
                        'name'       => $varData['name'],
                    ]);
                }

                // --- options ---
                if (!empty($varData['options']) && isset($variation)) {
                    foreach ($varData['options'] as $optData) {
                        // delete option
                        if (!empty($optData['delete']) && !empty($optData['id'])) {
                            Option::find($optData['id'])?->delete();
                            continue;
                        }
                        // update existing option
                        if (!empty($optData['id'])) {
                            $option = Option::find($optData['id']);
                            $option?->update(array_filter([
                                'name'  => $optData['name'] ?? null,
                                'price' => $optData['price'] ?? null,
                            ]));
                        } else {
                            // create new option
                            Option::create([
                                'product_id'   => $product->id,
                                'variation_id' => $variation->id,
                                'name'         => $optData['name'],
                                'price'        => $optData['price'],
                            ]);
                        }
                    }
                }
            }
        }

        // --- gallery delete ---
        if ($request->has('gallery_delete')) {
            foreach ($request->gallery_delete as $imgId) {
                $img = ProductImage::find($imgId);
                if ($img) {
                    $this->deleteImage($img->image);
                    $img->delete();
                }
            }
        }

        // --- gallery add ---
        if ($request->hasFile('gallery_add')) {
            foreach ($request->file('gallery_add') as $file) {
                $path = $this->uploadFile_v2($file, 'products/gallery');
                ProductImage::create(['product_id' => $product->id, 'image' => $path]);
            }
        }

        return response()->json($product->load('variations.options', 'gallery'));
    }

    public function destroy($id)
    {
        $product = Product::with('gallery')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // delete main image
        $this->deleteImage($product->image);

        // delete gallery images
        foreach ($product->gallery as $img) {
            $this->deleteImage($img->image);
        }

        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function changeStatus($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $product->update(['status' => !$product->status]);
        return response()->json(['status' => $product->status]);
    }

    // =================== Gallery ===================

    public function addGallery(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'images'   => 'required|array',
            'images.*' => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $added = [];
        foreach ($request->file('images') as $file) {
            $path = $this->uploadFile_v2($file, 'products/gallery');
            $img  = ProductImage::create(['product_id' => $product->id, 'image' => $path]);
            $added[] = $img;
        }

        return response()->json($added, 201);
    }

    public function deleteGalleryImage($id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $this->deleteImage($image->image);
        $image->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    // =================== Variations ===================

    public function addVariation(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'required|array',
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $variation = Variation::create([
            'product_id' => $product->id,
            'name'       => $request->name,
        ]);

        return response()->json($variation, 201);
    }

    public function deleteVariation($id)
    {
        $variation = Variation::find($id);
        if (!$variation) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $variation->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    // =================== Options ===================

    public function addOption(Request $request, $variationId)
    {
        $variation = Variation::find($variationId);
        if (!$variation) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'required|array',
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'price'   => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $option = Option::create([
            'product_id'   => $variation->product_id,
            'variation_id' => $variation->id,
            'name'         => $request->name,
            'price'        => $request->price,
        ]);

        return response()->json($option, 201);
    }

    public function deleteOption($id)
    {
        $option = Option::find($id);
        if (!$option) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $option->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
