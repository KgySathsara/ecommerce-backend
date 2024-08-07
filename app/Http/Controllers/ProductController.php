<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json(['products' => $products]);
    }

    public function show(Product $product)
    {
        return response()->json(['product' => $product]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20000' 
        ]);

        $imagePath = $request->file('image')->store('images', 'public');

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image_path' => $imagePath
        ]);

        return response()->json(['product' => $product], 201);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // 'nullable' means image is optional during update
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($product->image_path) {
                Storage::delete('public/' . $product->image_path);
            }
            // Store the new image
            $imagePath = $request->file('image')->store('images', 'public');
            $product->image_path = $imagePath;
        }

        // Update product with new values
        $product->update($request->only('name', 'description'));

        return response()->json(['product' => $product]);
    }

    public function destroy(Product $product)
    {
        // Delete the image from storage if it exists
        if ($product->image_path) {
            Storage::delete('public/' . $product->image_path);
        }
        // Delete the product record
        $product->delete();
        
        return response()->json(null, 204);
    }
}