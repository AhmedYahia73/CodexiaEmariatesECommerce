<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    use image;

    public function index()
    {
        $services = Service::paginate(15);
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name.en'        => 'required|string',
            'name.ar'        => 'required|string',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',
            'icon'           => 'required|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $service = Service::create([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $this->upload_image($request, 'icon', 'services'),
        ]);

        return response()->json($service, 201);
    }

    public function show($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name.en'        => 'nullable|string',
            'name.ar'        => 'nullable|string',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'icon'           => 'nullable|image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = [];

        if ($request->has('name')) {
            $data['name'] = array_merge($service->name ?? [], $request->name);
        }
        if ($request->has('description')) {
            $data['description'] = array_merge($service->description ?? [], $request->description);
        }
        if ($request->hasFile('icon')) {
            $data['icon'] = $this->update_image_v2($request, $service->icon, 'icon', 'services');
        }

        $service->update($data);
        return response()->json($service);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $this->deleteImage($service->icon);
        $service->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
