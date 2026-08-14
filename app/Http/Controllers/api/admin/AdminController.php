<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use image;

    public function index()
    {
        $admins = User::where('role', 'admin')->paginate(15);
        return response()->json($admins);
    } 

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone'    => 'required|string',
            'image'    => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'email', 'password', 'phone']);
        $data['role'] = 'admin';

        if ($request->hasFile('image')) {
            $data['image'] = $this->upload_image($request, 'image', 'users');
        }

        $admin = User::create($data);
        return response()->json($admin, 201);
    }

    public function show($id)
    {
        $admin = User::where('role', 'admin')->find($id);
        if (!$admin) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->find($id);
        if (!$admin) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string',
            'email'    => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'phone'    => 'nullable|string',
            'image'    => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'email', 'password', 'phone']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->update_image_v2($request, $admin->image, 'image', 'users');
        }

        $admin->update($data);
        return response()->json($admin);
    }

    public function destroy($id)
    {
        $admin = User::where('role', 'admin')->find($id);
        if (!$admin) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $admin->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function changeStatus($id)
    {
        // toggle active/inactive via email_verified_at as status flag
        $admin = User::where('role', 'admin')->find($id);
        if (!$admin) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $admin->update([
            'email_verified_at' => $admin->email_verified_at ? null : now(),
        ]);
        return response()->json(['active' => !is_null($admin->email_verified_at)]);
    }
}
