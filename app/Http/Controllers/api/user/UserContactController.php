<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;

use Illuminate\Support\Facades\Validator;

class UserContactController extends Controller
{
    public function contact(Request $request){
        $validator = Validator::make($request->all(), [
            'f_name' => ["required"],
            'l_name' => ["required"],
            'phone' => ["required"],
            'email' => ["required", "email"],
            'title' => ["required"],
            'content' => ["required"],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $validator->validated();
        $data['status'] = 0;
        ContactUs::create($data);

        return response()->json([
            "success" => "You contact success"
        ]);
    }
}
