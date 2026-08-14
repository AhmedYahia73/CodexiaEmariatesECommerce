<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    use image;
    public function index()
    {
        $methods = PaymentMethod::paginate(15);
        return response()->json($methods);
    } 

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|array',
            'description' => 'required|array',
            'name.en'        => 'required',
            'name.ar'        => 'required',
            'description.en' => 'required',
            'description.ar' => 'required',
            'icon'        => 'required|image',
            'status'      => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'description', 'status']);

        if ($request->hasFile('icon')) {
            $data['icon'] = $this->upload_image($request, 'icon', 'payment_methods');
        }

        $method = PaymentMethod::create($data);
        return response()->json($method, 201);
    }

    public function show($id)
    {
        $method = PaymentMethod::find($id);
        if (!$method) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($method);
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::find($id);
        if (!$method) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|array',
            'description' => 'required|array',
            'name.en'        => 'required',
            'name.ar'        => 'required',
            'description.en' => 'required',
            'description.ar' => 'required',
            'icon'        => 'nullable|image',
            'status'      => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'description', 'status']);

        if ($request->hasFile('icon')) {
            $data['icon'] = $this->update_image_v2($request, $method->icon, 'icon', 'payment_methods');
        }

        $method->update($data);
        return response()->json($method);
    }

    public function destroy($id)
    {
        $method = PaymentMethod::find($id);
        if (!$method) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $method->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function changeStatus($id)
    {
        $method = PaymentMethod::find($id);
        if (!$method) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $method->update(['status' => !$method->status]);
        return response()->json(['status' => $method->status]);
    }
}
