<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    use image;

    public function index()
    {
        $about = About::first();
        return response()->json($about);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title.en'   => 'required|string',
            'title.ar'   => 'required|string',
            'content.en' => 'required|string',
            'content.ar' => 'required|string',
            'image'      => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $about = About::first();
        $data  = [];

        if ($request->has('title')) {
            $data['title'] = $request->title;
        }
        if ($request->has('content')) {
            $data['content'] =  $request->content;
        }
        if ($request->hasFile('image')) {
            $data['image'] = $about
                ? $this->update_image_v2($request, $about->image, 'image', 'about')
                : $this->upload_image($request, 'image', 'about');
        }

        if ($about) {
            $about->update($data);
        } else {
            $about = About::create($data);
        }

        return response()->json($about);
    }
}
