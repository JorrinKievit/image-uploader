<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function index() {
        return view("images.index");
    }

    public function show() {
        // return all images
        return Image::latest()->pluck("name")->toArray();
    }

    public function store(Request $request) {
        if (!$request->hasFile("image")) {
            return response()->json([
                "message" => "No image found"
            ], 400);
        }

        $request->validate([
            "image" => "required|file|image|mimes:jpg,jpeg,png|max:2048"
        ]);

        Log::info($request->file("image"));

        $path = $request->file('image')->store('images', 'public');

        if (!$path) {
            return response()->json([
                "message" => "Image could not be saved"
            ], 500);
        }

        $uploaded_file = $request->file('image');

        $image = Image::create([
            "name" => $uploaded_file->hashName(),
            "extension" => $uploaded_file->extension(),
            "size" => $uploaded_file->getSize(),
        ]);

        return $image->name;
    }
}
