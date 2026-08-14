<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAboutController extends Controller
{
    public function about(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang  = $request->local;
        $about = About::first();

        if (!$about) {
            return response()->json(['data' => null]);
        }

        return response()->json([
            'data' => [
                'title'   => $about->title[$lang] ?? null,
                'content' => $about->content[$lang] ?? null,
                'image'   => $about->image_url,
            ],
        ]);
    }

    public function services(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'local' => 'required|in:en,ar',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $lang     = $request->local;
        $services = Service::all()->map(fn($item) => [
            'id'          => $item->id,
            'name'        => $item->name[$lang] ?? null,
            'description' => $item->description[$lang] ?? null,
            'icon'        => $item->icon_url,
        ]);

        return response()->json(['data' => $services]);
    }
}
