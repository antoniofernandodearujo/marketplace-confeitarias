<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Confectionery;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['images', 'confectionery'])->get();
        return response()->json($products);
    }

    public function show(Product $product)
    {
        return response()->json($product->load(['images', 'confectionery']));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $validatedData = $request->validated();
            
            // Create the product
            $product = Product::create($validatedData);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            DB::commit();
            return response()->json($product->load('images'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar produto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar produto'], 500);
        }
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            
            // Update product data
            $product->update($validatedData);

            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            DB::commit();
            return response()->json($product->load('images'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao atualizar produto'], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete images from storage and database
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $product->delete();

            DB::commit();
            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar produto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao deletar produto'], 500);
        }
    }
}