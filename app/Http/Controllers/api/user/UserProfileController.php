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
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'         => 'sometimes',
            // استخدمنا unique بدلاً من exists لكي نتأكد أن الإيميل غير مكرر، مع استثناء المستخدم الحالي
            'email'        => 'sometimes|email|unique:users,email,' . $user->id,
            'phone'        => 'sometimes|unique:users,phone,' . $user->id,
            'image'        => 'sometimes|image', 
            'delete_image' => 'required|boolean'
        ]); 

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // استدعاء صورة المستخدم بشكل صحيح
        $image = $user->image;

        if ($request->hasFile('image')) {
            $image = $this->update_image($request, $image, 'image', 'users');
        } elseif ($request->delete_image) {
            // يفضل إضافة كود هنا لحذف الصورة القديمة من السيرفر إذا لزم الأمر
            $image = null;
        }

        // تحديث بيانات المستخدم
        $user->update([
            "name"  => $request->name ?? $user->name,
            "email" => $request->email ?? $user->email,
            "phone" => $request->phone ?? $user->phone,
            "image" => $image,
        ]);

        return response()->json([
            "success" => "Profile updated successfully"
        ]);
    }
}
