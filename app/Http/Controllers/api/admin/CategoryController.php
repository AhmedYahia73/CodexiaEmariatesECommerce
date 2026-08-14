<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use image;
    public function index()
    {
        $categories = Category::with('parentCategory')->paginate(15);
        return response()->json($categories);
    }

    // Simple list for dropdowns (select category_id)
    public function list()
    {
        $categories = Category::select('id', 'name')->where('status', 1)->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|array',
            'name.en'     => 'required|string',
            'name.ar'     => 'required|string',
            'description'     => 'required|array',
            'description.en'  => 'required|string',
            'description.ar'  => 'required|string',
            'image'       => 'nullable|image',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'description', 'category_id', 'status']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->upload_image($request, 'image', 'categories');
        }

        $category = Category::create($data);
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::with('parentCategory', 'subcategories')->find($id);
        if (!$category) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'nullable|array',
            'name.en'     => 'nullable|string',
            'name.ar'     => 'nullable|string',
            'description'     => 'nullable|array',
            'description.en'  => 'nullable|string',
            'description.ar'  => 'nullable|string',
            'image'       => 'nullable|image',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'description', 'category_id', 'status']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->update_image_v2($request, $category->image, 'image', 'categories');
        }

        $category->update($data);
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function changeStatus($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $category->update(['status' => !$category->status]);
        return response()->json(['status' => $category->status]);
    }
}
