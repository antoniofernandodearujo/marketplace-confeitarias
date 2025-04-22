<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Confectionery;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();

            // Check if confectionery exists
            $confectionery = Confectionery::findOrFail($request->confectionery_id);

            // Create the product associated with the confectionery
            $product = $confectionery->products()->create($request->validated());

            // Handle multiple image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            DB::commit();
            
            // Return product with its images
            return response()->json($product->load('images'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Delete any uploaded files if they exist
            if (isset($paths)) {
                foreach ($paths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
            return response()->json(['error' => 'Erro ao criar produto'], 500);
        }
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $product->update($request->validated());

            // Handle additional images
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
            return response()->json(['error' => 'Erro ao atualizar produto'], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete all associated images from storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            $product->delete();

            DB::commit();
            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao deletar produto'], 500);
        }
    }
}