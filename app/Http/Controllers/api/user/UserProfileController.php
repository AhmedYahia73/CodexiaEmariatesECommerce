<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\image;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserProfileController extends Controller
{
    use image;

    public function profile(){
        return response()->json([
            "id" => auth()->user()->id,
            "name" => auth()->user()->name,
            "email" => auth()->user()->email,
            "phone" => auth()->user()->phone,
            "image" => auth()->user()->image_url,
        ]);
    }

    public function update_profile(Request $request){

        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes',
            'email'    => 'sometimes|email|exists:users,email,' . auth()->id,
            'phone'    => 'sometimes|exists:users,phone,' . auth()->id,
            'image'    => 'sometimes',
            'delete_image' => "required|boolean"
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $image = auth()->image;

        if($request->image){
            $image = $this->update_image($request, $image, 'image', 'users');
        }
        elseif($request->delete_image){
            $image = null;
        }

        User::where("id", auth()->user()->id)
        ->update([
            "name" => $request->name ?? auth()->user()->name,
            "email" => $request->email ?? auth()->user()->email,
            "phone" => $request->phone ?? auth()->user()->phone,
            "image" => $image,
        ]);

        return response()->json([
            "success" => "You update profile success"
        ]);
    }
}
