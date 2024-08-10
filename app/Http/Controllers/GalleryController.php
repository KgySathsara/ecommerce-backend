<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $imageName = time().'.'.$request->file('images')->extension();
        $request->file('images')->move(public_path('images'), $imageName);

        $gallery = Gallery::create([
            'name' => $request->name,
            'image' => $imageData, 
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);

        return response()->json(['message' => 'Gallery created successfully'], 201);
    }

    public function index()
    {
        $galleries = Gallery::all()->map(function ($gallery) {
            $gallery->image = base64_encode($gallery->image);

            return $gallery;
        });

        return response()->json(['galleries' => $galleries]);
    }
}
