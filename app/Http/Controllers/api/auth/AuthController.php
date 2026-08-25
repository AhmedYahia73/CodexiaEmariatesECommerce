<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordEmail;
use App\Mail\SignUpEmail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user  = Auth::user();
        if(!$user->status){
            return response()->json([
                "errors" => "You are blocked"
            ], 400);
        }
        if(!$user->active){
            return response()->json([
                "errors" => "email is wrong"
            ], 400);
        }
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function sign_up(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|email',
            'phone'    => 'required|string',
            'password' => 'required|string|min:6', // يفضل وضع حد أدنى للباسورد
        ]); 

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // 2. Setup Settings & Code
        $settings = Setting::first();
        $isVerificationRequired = $settings ? $settings->sign_up_code : false;
        $code = rand(1000000, 9999999);

        // 3. Check existing user by email
        $user = User::where("email", $request->email)->first();

        if ($user) {
            // إذا كان المستخدم موجود ومفعل
            if ($user->active) {
                return response()->json(['errors' => 'email is exist'], 400);
            }

            // إذا كان موجود وغير مفعل، وقام بتغيير رقم هاتفه، نتأكد أن الرقم الجديد غير مستخدم
            if ($request->phone !== $user->phone) {
                $phoneExists = User::where("phone", $request->phone)->exists();
                if ($phoneExists) {
                    return response()->json(['errors' => 'phone is exist'], 400);
                }
            }

            // تحديث بيانات المستخدم الغير مفعل
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password); 
        } else {
            // مستخدم جديد تماماً: نتأكد أولاً أن رقم الهاتف غير مستخدم
            $phoneExists = User::where("phone", $request->phone)->exists();
            if ($phoneExists) {
                return response()->json(['errors' => 'phone is exist'], 400);
            }

            // تهيئة المستخدم الجديد
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password); // تم إصلاح ثغرة عدم التشفير
            $user->role = "user";
            $user->status = true;
            $user->order_count = 0;
            $user->order_sum = 0;
        }

        // 4. Handle Activation & Return Response
        if ($isVerificationRequired) {
            $user->active = false;
            $user->code = $code;
            $user->save();

            // إرسال الإيميل سواء كان مستخدم جديد أو قديم غير مفعل
            Mail::to($user->email)->send(new SignUpEmail($code));

            return response()->json([
                "success" => true,
                "sign_up_code" => $isVerificationRequired,
                "user" => $user,
                "token" => null, // بدون توكن لأنه يحتاج تفعيل
            ]);
        } else {
            $user->active = true;
            $user->code = null;
            $user->save();

            return response()->json([
                "success" => true,
                "sign_up_code" => $isVerificationRequired,
                "user" => $user,
                "token" => $user->createToken('auth_token')->plainTextToken, // إصدار التوكن فوراً
            ]);
        }
    }

    public function check_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'code'     => 'required',
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $key = 'check_code_' . $request->email . '_' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'errors' => "لقد تجاوزت الحد المسموح من المحاولات. يرجى الانتظار {$seconds} ثانية ثم المحاولة مجدداً.",
            ], 429);
        }

        $user = User::
        where("code", $request->code)
        ->where("email", $request->email)
        ->first();
        if($user){
            \Illuminate\Support\Facades\RateLimiter::clear($key);

            $user->update([
                "code" => null,
                "active" => true,
            ]);
            return response()->json([
                "success" => true,
                "sign_up_code" => true,
                "user" => $user,
                "token" => $user->createToken('auth_token')->plainTextToken,  // بدون توكن لأنه يحتاج تفعيل
            ]);
        } 

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        return response()->json([
            "success" => false,
            "errors" => "code is wrong",
        ]); 
    }

    public function main_settings(Request $request){
        $settings = Setting::first();

        return response()->json([
            "settings" => $settings,
        ]);
    }

    public function forget_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email', 
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $key = 'forget_password_' . $request->email . '_' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'errors'  => "لقد تجاوزت الحد المسموح من المحاولات. يرجى الانتظار {$seconds} ثانية ثم المحاولة مجدداً.",
            ], 429);
        }

        $code = rand(1000000, 9999999);
        $user = User::where("email", $request->email)->first();
        if(!$user){
            \Illuminate\Support\Facades\RateLimiter::hit($key, 180);
            return response()->json([
                "errors" => "Email is wrong"
            ], 400);
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 180);
        Mail::to($request->email)->send(new ForgetPasswordEmail($code));
        $user->code = $code;
        $user->save();

        return response()->json([
            "success" => "You must check your email"
        ]);
    }

    public function check_code_forget_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email', 
            'code'    => 'required', 
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $key = 'check_code_forget_password_' . $request->email . '_' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'errors'  => "لقد تجاوزت الحد المسموح من المحاولات. يرجى الانتظار {$seconds} ثانية ثم المحاولة مجدداً.",
            ], 429);
        }

        $user = User::
        where("email", $request->email)
        ->where("code", $request->code)
        ->first();
        if(!$user){
            \Illuminate\Support\Facades\RateLimiter::hit($key, 180);
            return response()->json([
                "errors" => "code is wrong"
            ], 400);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($key);
        return response()->json([
            "success" => "You must change your password success"
        ]);
    }

    public function new_password_forget_password(Request $request){
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email', 
            'code'    => 'required', 
            'new_password'    => 'required', 
        ]); 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $key = 'new_password_forget_password_' . $request->email . '_' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'errors'  => "لقد تجاوزت الحد المسموح من المحاولات. يرجى الانتظار {$seconds} ثانية ثم المحاولة مجدداً.",
            ], 429);
        }

        $user = User::
        where("email", $request->email)
        ->where("code", $request->code)
        ->first();

        if (!$user) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 180);
            return response()->json([
                "errors" => "code is wrong"
            ], 400);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($key);
        $user->password = bcrypt($request->new_password);
        $user->code = null;
        $user->save();

        return response()->json([
            "success" => "You change your password success"
        ]);
    }
}
