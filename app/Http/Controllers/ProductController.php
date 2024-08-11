<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function getProductCount(): JsonResponse
    {
        $productCount = Product::count();
        return response()->json(['count' => $productCount]);
    }

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
            // Delete the old image
            if ($product->image) {
                Storage::delete(public_path('images/' . $product->image));
            }

            // Save the new image
            $imageName = time().'.'.$request->file('upload')->extension();
            $request->file('upload')->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->update($request->only('name', 'description', 'price', 'quantity'));

        return response()->json(['product' => $product]);
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete associated image
            if ($product->image) {
                $imagePath = public_path('images/' . $product->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                } else {
                    Log::warning('Image file not found: ' . $imagePath);
                }
            }

            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            Log::error('Product not found: ' . $id);
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['error' => 'There was an error deleting the product'], 500);
        }
    }
}
