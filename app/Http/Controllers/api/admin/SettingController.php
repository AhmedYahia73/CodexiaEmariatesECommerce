<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    use image;

    public function show()
    {
        $setting = Setting::first();
        return response()->json($setting);
    }

    public function update(Request $request)
    {
        $setting = Setting::first();
        $isNew   = !$setting;

        $validator = Validator::make($request->all(), [
            'brand_name'    => ($isNew ? 'required' : 'nullable') . '|array',
            'brand_name.en' => ($isNew ? 'required' : 'nullable') . '|string',
            'brand_name.ar' => ($isNew ? 'required' : 'nullable') . '|string',
            'logo'          => 'nullable|image',
            'logo2'         => 'nullable|image',
            'phone'         => ($isNew ? 'required' : 'nullable') . '|string',
            'wattsapp'      => ($isNew ? 'required' : 'nullable') . '|string',
            'email'         => ($isNew ? 'required' : 'nullable') . '|email',
            'address'       => 'nullable|string',
            'lat'           => 'nullable|string',
            'lng'           => 'nullable|string',
            'facebook'      => 'nullable|string',
            'insta'         => 'nullable|string',
            'tiktok'        => 'nullable|string',
            'ios_app'       => 'nullable|string',
            'android_app'   => 'nullable|string',
            'min_order'     => ($isNew ? 'required' : 'nullable') . '|numeric|min:0',
            "sign_up_code"  => "sometimes|boolean",
            "currency"      => "sometimes"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only([
            'brand_name', 'phone', 'wattsapp', 'email',
            'address', 'lat', 'lng', 'facebook', 'insta',
            'tiktok', 'ios_app', 'android_app', 'min_order',
            'sign_up_code', "currency"
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $isNew
                ? $this->upload_image($request, 'logo', 'settings')
                : $this->update_image_v2($request, $setting->logo, 'logo', 'settings');
        }

        if ($request->hasFile('logo2')) {
            $data['logo2'] = $isNew
                ? $this->upload_image($request, 'logo2', 'settings')
                : $this->update_image_v2($request, $setting->logo2, 'logo2', 'settings');
        }

        if ($isNew) {
            $setting = Setting::create($data);
        } else {
            $setting->update($data);
        }

        return response()->json($setting);
    }
}
