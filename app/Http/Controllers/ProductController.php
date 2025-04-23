<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Confectionery;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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

    public function update(Request $request, Product $product)
    {
        try {
            DB::beginTransaction();
            
            // Convert empty strings to null to avoid validation issues
            $input = collect($request->all())->map(function ($value) {
                return $value === '' ? null : $value;
            })->toArray();

            // Update only the fields that were sent
            $updateData = collect($input)->only(['name', 'price', 'description', 'confectionery_id'])
                ->filter()
                ->toArray();

            if (!empty($updateData)) {
                // Validate the fields that are present
                $rules = collect([
                    'name' => ['string', 'max:255'],
                    'price' => ['numeric', 'min:0'],
                    'description' => ['nullable', 'string'],
                    'confectionery_id' => ['exists:confectioneries,id']
                ])->only(array_keys($updateData))->toArray();

                $validator = validator($updateData, $rules);
                $validator->validate();

                $product->update($updateData);
            }

            // Handle image uploads if provided
            if ($request->hasFile('images')) {
                // Remove existing images
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }

                // Add new images
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            DB::commit();
            return response()->json($product->fresh()->load(['images', 'confectionery']));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao atualizar produto: ' . $e->getMessage()], 500);
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