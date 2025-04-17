<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Iniciando a migration para criar a tabela de produtos.
     * A tabela de produtos será utilizada para armazenar os produtos
     * e suas informações, como nome, preço e descrição.
     * 
     * A tabela de produtos terá uma relação com a tabela de confeitarias,
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->foreignId('confectionery_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Método para reverter a migration.
     * Esse método será chamado quando a migration for revertida.
     * Ele irá remover a tabela de produtos.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
