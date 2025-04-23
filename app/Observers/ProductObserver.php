<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Observer para manipulação do ciclo de vida dos produtos
 * 
 * Esta classe gerencia eventos do ciclo de vida dos produtos,
 * garantindo que recursos associados sejam manipulados corretamente.
 */
class ProductObserver
{
    /**
     * Manipula o evento de exclusão de um produto
     * 
     * Remove todas as imagens associadas ao produto do armazenamento físico
     * e do banco de dados antes que o produto seja excluído.
     * 
     * @param Product $product O produto que está sendo excluído
     */
    public function deleting(Product $product)
    {
        try {
            // Remove todas as imagens do produto quando ele for excluído
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }
        } catch (\Exception $e) {
            Log::error('Erro ao deletar imagens do produto: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'exception' => $e
            ]);
            // Não relançamos a exceção para permitir que a exclusão do produto continue
        }
    }

    /**
     * Manipula o evento de restauração de um produto (caso use soft deletes)
     * 
     * @param Product $product O produto que está sendo restaurado
     */
    public function restored(Product $product)
    {
        Log::info('Produto restaurado: ' . $product->id);
    }
}