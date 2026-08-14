<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\City;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang = $request->local;
        $addresses = Address::where('user_id', auth()->id())
            ->with(['city', 'zone'])
            ->paginate(15)
            ->through(function ($item) use ($lang) {
                return [
                    'id'              => $item->id,
                    'address'         => $item->address,
                    'floor'           => $item->floor,
                    'street'          => $item->street,
                    'building_number' => $item->building_number,
                    'additional_data' => $item->additional_data,
                    'lat'             => $item->lat,
                    'lng'             => $item->lng,
                    'map'             => $item->map,
                    'city'            => $item->city?->name[$lang] ?? null,
                    'zone'            => $item->zone?->name[$lang] ?? null,
                ];
            });

        return response()->json($addresses);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address'         => 'nullable|string',
            'lat'             => 'required|string',
            'lng'             => 'required|string',
            'floor'           => 'required|string',
            'street'          => 'required|string',
            'building_number' => 'nullable|string',
            'city_id'         => 'required|exists:cities,id',
            'zone_id'         => 'required|exists:zones,id',
            'additional_data' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only([
            'address', 'lat', 'lng', 'floor', 'street',
            'building_number', 'city_id', 'zone_id', 'additional_data',
        ]);
        $data['user_id'] = auth()->id();

        $address = Address::create($data);
        return response()->json($address->load(['city', 'zone']), 201);
    }

    public function show(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang    = $request->local;
        $address = Address::where('user_id', auth()->id())->find($id);
        if (!$address) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'id'              => $address->id,
            'address'         => $address->address,
            'floor'           => $address->floor,
            'street'          => $address->street,
            'building_number' => $address->building_number,
            'additional_data' => $address->additional_data,
            'lat'             => $address->lat,
            'lng'             => $address->lng,
            'map'             => $address->map,
            'city'            => $address->city?->name[$lang] ?? null,
            'zone'            => $address->zone?->name[$lang] ?? null,
        ]);
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', auth()->id())->find($id);
        if (!$address) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'address'         => 'nullable|string',
            'lat'             => 'nullable|string',
            'lng'             => 'nullable|string',
            'floor'           => 'nullable|string',
            'street'          => 'nullable|string',
            'building_number' => 'nullable|string',
            'city_id'         => 'nullable|exists:cities,id',
            'zone_id'         => 'nullable|exists:zones,id',
            'additional_data' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $address->update($request->only([
            'address', 'lat', 'lng', 'floor', 'street',
            'building_number', 'city_id', 'zone_id', 'additional_data',
        ]));

        return response()->json($address->load(['city', 'zone']));
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', auth()->id())->find($id);
        if (!$address) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $address->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    // List of cities for dropdown
    public function cities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang   = $request->local;
        $cities = City::where('status', 1)->get()->map(function ($item) use ($lang) {
            return [
                'id'   => $item->id,
                'name' => $item->name[$lang] ?? null,
            ];
        });

        return response()->json($cities);
    }

    // List of zones by city for dropdown
    public function zones(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local'   => 'required|in:en,ar',
            'city_id' => 'required|exists:cities,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang  = $request->local;
        $zones = Zone::where('city_id', $request->city_id)
            ->where('status', 1)
            ->get()
            ->map(function ($item) use ($lang) {
                return [
                    'id'    => $item->id,
                    'name'  => $item->name[$lang] ?? null,
                    'price' => $item->price,
                ];
            });

        return response()->json($zones);
    }
}
