<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::all(['name', 'description', 'price', 'quantity', 'image']);
            foreach ($products as $product) {
                $product->image_url = url('images/' . $product->image);
            }
            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            \Log::error('Error fetching products: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching products'], 500);
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $product->image_url = url('images/' . $product->image);

        return response()->json(['product' => $product]);
    }   
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time().'.'.$request->file('upload')->extension();
        $request->file('upload')->move(public_path('images'), $imageName);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'image' => $imageName,
        ]);

        return response()->json(['product' => $product], 201);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('upload')) {
            $imageData = file_get_contents($request->file('upload')->getRealPath());
            $product->image = $imageData;
        }

        $product->update($request->only('name', 'description', 'price', 'quantity'));

        return response()->json(['product' => $product]);
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                Storage::delete($product->image);
            }
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was an error deleting the product'], 500);
        }
    }

}
