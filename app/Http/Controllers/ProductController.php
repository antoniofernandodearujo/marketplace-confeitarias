<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Confectionery;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * Controlador para gerenciamento de produtos
 * 
 * Esta classe gerencia operações CRUD para os produtos das confeitarias,
 * incluindo o processamento de imagens associadas.
 */
class ProductController extends Controller
{
    /**
     * Lista todos os produtos cadastrados com suas imagens e confeitarias relacionadas
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Eager loading para evitar problema de N+1 queries
        $products = Product::with(['images', 'confectionery'])->get();
        return response()->json($products);
    }

    /**
     * Exibe os detalhes de um produto específico
     * 
     * @param Product $product O produto a ser exibido (injetado pelo Laravel)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        return response()->json($product->load(['images', 'confectionery']));
    }

    /**
     * Cria um novo produto com suas imagens associadas
     * 
     * @param StoreProductRequest $request Requisição validada
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $validatedData = $request->validated();
            
            // Verifica se a confeitaria existe
            $confectioneryExists = Confectionery::where('id', $validatedData['confectionery_id'])->exists();
            if (!$confectioneryExists) {
                return response()->json(['error' => 'Confeitaria não encontrada'], 404);
            }
            
            // Cria o produto
            $product = Product::create($validatedData);

            // Processa o upload de imagens
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Valida cada imagem individualmente
                    if ($image->isValid() && in_array($image->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                        $path = $image->store('products', 'public');
                        $product->images()->create(['image_path' => $path]);
                    }
                }
            }

            DB::commit();
            return response()->json($product->load('images'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar produto: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Erro ao criar produto'], 500);
        }
    }

    /**
     * Atualiza os dados de um produto existente
     * 
     * @param Request $request Dados da requisição
     * @param Product $product O produto a ser atualizado
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product)
    {
        try {
            DB::beginTransaction();
            
            // Converte strings vazias para null para evitar problemas de validação
            $input = collect($request->all())->map(function ($value) {
                return $value === '' ? null : $value;
            })->toArray();

            // Atualiza apenas os campos que foram enviados
            $updateData = collect($input)->only(['name', 'price', 'description', 'confectionery_id'])
                ->filter()
                ->toArray();

            if (!empty($updateData)) {
                // Valida os campos presentes
                $rules = collect([
                    'name' => ['string', 'max:255'],
                    'price' => ['numeric', 'min:0'],
                    'description' => ['nullable', 'string'],
                    'confectionery_id' => ['exists:confectioneries,id']
                ])->only(array_keys($updateData))->toArray();

                $validator = validator($updateData, $rules);
                $validator->validate();

                // Se estiver alterando a confeitaria, verifica se ela existe
                if (isset($updateData['confectionery_id'])) {
                    $confectioneryExists = Confectionery::where('id', $updateData['confectionery_id'])->exists();
                    if (!$confectioneryExists) {
                        return response()->json(['error' => 'Confeitaria não encontrada'], 404);
                    }
                }

                $product->update($updateData);
            }

            // Processa upload de novas imagens (se houver)
            if ($request->hasFile('images')) {
                // Remove imagens existentes através do ProductObserver
                foreach ($product->images as $image) {
                    $image->delete(); // O observer se encarrega de remover o arquivo
                }

                // Adiciona novas imagens
                foreach ($request->file('images') as $image) {
                    if ($image->isValid() && in_array($image->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                        $path = $image->store('products', 'public');
                        $product->images()->create(['image_path' => $path]);
                    }
                }
            }

            DB::commit();
            return response()->json($product->fresh()->load(['images', 'confectionery']));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar produto: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Erro ao atualizar produto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove um produto e suas imagens associadas
     * 
     * As imagens são removidas automaticamente pelo ProductObserver
     * 
     * @param Product $product O produto a ser excluído
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // As imagens são removidas automaticamente pelo ProductObserver
            // através do evento "deleting"
            $product->delete();

            DB::commit();
            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar produto: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Erro ao deletar produto'], 500);
        }
    }
}