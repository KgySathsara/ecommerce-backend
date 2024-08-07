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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::delete('public/' . $product->image_path);
            }
            $imagePath = $request->file('image')->store('images', 'public');
            $product->image_path = $imagePath;
        }

        $product->update($request->only('name', 'description'));

        return response()->json(['product' => $product]);
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::delete($product->image_path);
        }
        $product->delete();
        return response()->json(null, 204);
    }
}

