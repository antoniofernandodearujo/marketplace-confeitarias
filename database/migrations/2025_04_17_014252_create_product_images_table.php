<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Iniciando a migration para criar a tabela de imagens de produtos.
     * A tabela de imagens de produtos será utilizada para armazenar as imagens
     * dos produtos e suas informações, como o caminho da imagem.
     * 
     * A tabela de imagens de produtos terá uma relação com a tabela de produtos,
     * onde cada imagem estará associada a um produto específico.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    /**
     * Método para reverter a migration.
     * Esse método será chamado quando a migration for revertida.
     * Ele irá remover a tabela de imagens de produtos.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
