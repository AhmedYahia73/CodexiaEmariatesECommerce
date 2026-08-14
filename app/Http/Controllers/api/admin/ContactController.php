<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ContactUs;

class ContactController extends Controller
{

    public function index(Request $request){
        $searchTerm = $request->input('search');

        $data = ContactUs::where('status', 0)
        ->when($searchTerm, function ($query, $searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('f_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('l_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('content', 'LIKE', "%{$searchTerm}%");
            });
        })
        ->latest()
        ->paginate(10);

        return response()->json([
            "data" => $data
        ]);
    }

    public function history(Request $request){
        $searchTerm = $request->input('search');

        $data = ContactUs::where('status', 1)
        ->when($searchTerm, function ($query, $searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('f_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('l_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('content', 'LIKE', "%{$searchTerm}%");
            });
        })
        ->latest()
        ->paginate(10);

        return response()->json([
            "data" => $data
        ]);
    }

    public function read($id){
        $data = ContactUs::where('id', $id)
        ->update([
            "status" => 1
        ]);

        return response()->json([
            "success" => "You read success"
        ]);
    }
}
