<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::paginate(15);
        return response()->json($cities);
    } 

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|array',
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $city = City::create($request->only(['name', 'status']));
        return response()->json($city, 201);
    }

    public function show($id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($city);
    }

    public function update(Request $request, $id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'nullable|array',
            'name.en' => 'nullable|string',
            'name.ar' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $city->update($request->only(['name', 'status']));
        return response()->json($city);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $city->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function changeStatus($id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $city->update(['status' => !$city->status]);
        return response()->json(['status' => $city->status]);
    }
}
