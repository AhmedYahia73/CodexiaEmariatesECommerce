<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use image;

    public function index()
    {
        $users = User::
        where('role', 'user')
        ->where("active", true)
        ->paginate(15);
        return response()->json($users);
    }

    // Simple list for dropdowns (select user_id)
    public function list()
    {
        $users = User::select('id', 'name', 'email', 'phone')->where('role', 'user')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone'    => 'required|string',
            "status"   => "required|boolean",
            'image'    => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'email', 'password', 'phone', 'status']);
        $data['role'] = 'user';
        $data['active'] = true;

        if ($request->hasFile('image')) {
            $data['image'] = $this->upload_image($request, 'image', 'users');
        }

        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::where('role', 'user')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'user')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string',
            'email'    => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'phone'    => 'nullable|string',
            'image'    => 'nullable|image',
            'status'   => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['name', 'email', 'password', 'phone']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->update_image_v2($request, $user->image, 'image', 'users');
        }

        $user->update($data);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::where('role', 'user')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function changeStatus($id)
    {
        $user = User::where('role', 'user')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now(),
        ]);
        return response()->json(['active' => !is_null($user->email_verified_at)]);
    }
}
