<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::with('city')->paginate(15);
        return response()->json($zones);
    }

    // Simple list for dropdowns (select zone_id)
    public function list()
    {
        $zones = City::select('id', 'name')->where('status', 1)->get();
        return response()->json($zones);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|array',
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'price'   => 'required|numeric|min:0',
            'city_id' => 'required|exists:cities,id',
            'status'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $zone = Zone::create($request->only(['name', 'price', 'city_id', 'status']));
        return response()->json($zone->load('city'), 201);
    }

    public function show($id)
    {
        $zone = Zone::with('city')->find($id);
        if (!$zone) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($zone);
    }

    public function update(Request $request, $id)
    {
        $zone = Zone::find($id);
        if (!$zone) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'nullable|array',
            'name.en' => 'nullable|string',
            'name.ar' => 'nullable|string',
            'price'   => 'nullable|numeric|min:0',
            'city_id' => 'nullable|exists:cities,id',
            'status'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $zone->update($request->only(['name', 'price', 'city_id', 'status']));
        return response()->json($zone->load('city'));
    }

    public function destroy($id)
    {
        $zone = Zone::find($id);
        if (!$zone) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $zone->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function changeStatus($id)
    {
        $zone = Zone::find($id);
        if (!$zone) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $zone->update(['status' => !$zone->status]);
        return response()->json(['status' => $zone->status]);
    }
}
