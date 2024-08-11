<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        // Convert image to binary data
        $imageBinary = file_get_contents($request->file('upload'));

        $gallery = Gallery::create([
            'name' => $request->name,
            'image' => $imageBinary,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);

        return response()->json(['gallery' => $gallery], 201);
    }

    public function index()
    {
        $galleries = Gallery::all()->map(function ($gallery) {
            // Determine the MIME type and base64 encode the image
            $mimeType = finfo_buffer(finfo_open(), $gallery->image, FILEINFO_MIME_TYPE);
            $gallery->image = 'data:' . $mimeType . ';base64,' . base64_encode($gallery->image);
            return $gallery;
        });
    
        return response()->json(['galleries' => $galleries]);
    }
    
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        try {
            $gallery = Gallery::findOrFail($id);
            $gallery->name = $validatedData['name'];
            $gallery->price = $validatedData['price'];
            $gallery->quantity = $validatedData['quantity'];
    
            if ($request->hasFile('upload')) {
                $imageBinary = file_get_contents($request->file('upload'));
                $gallery->image = $imageBinary;
            }
    
            $gallery->save();
    
            // Determine the MIME type and base64 encode the image before sending the response
            $mimeType = finfo_buffer(finfo_open(), $gallery->image, FILEINFO_MIME_TYPE);
            $gallery->image = 'data:' . $mimeType . ';base64,' . base64_encode($gallery->image);
    
            return response()->json(['gallery' => $gallery], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the image.', 'error' => $e->getMessage()], 500);
        }
    }    

    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->delete();
        return response()->json(['message' => 'Gallery deleted successfully']);
    }
}
