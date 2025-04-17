<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Iniciando a migration para criar a tabela de endereços.
     * A tabela de endereços será utilizada para armazenar os endereços
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('cep', 8);
            $table->string('street');
            $table->string('number', 20);
            $table->string('neighborhood', 100);
            $table->char('state', 2);
            $table->string('city', 100);
            $table->timestamps();
        });
    }

    /**
     * Método para reverter a migration.
     * Esse método será chamado quando a migration for revertida.
     * Ele irá remover a tabela de endereços.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
