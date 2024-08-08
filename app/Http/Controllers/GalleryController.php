<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = [];
        if($request->hasFile('images')) {
            foreach($request->file('images') as $file) {
                $path = $file->store('galleries', 'public');
                $images[] = $path;
            }
        }

        Gallery::create([
            'name' => $request->name,
            'images' => $images,
        ]);

        return response()->json(['message' => 'Gallery created successfully'], 201);
    }
}

