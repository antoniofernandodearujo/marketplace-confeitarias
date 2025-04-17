<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Iniciando a migration para criar a tabela de confeitarias.
     * A tabela de confeitarias será utilizada para armazenar as confeitarias
     * e suas informações, como nome, telefone e endereço.
     */

    public function up(): void
    {
        Schema::create('confectioneries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone', 20);
            $table->foreignId('address_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Método para reverter a migration.
     * Esse método será chamado quando a migration for revertida.
     * Ele irá remover a tabela de confeitarias.
     */

    public function down(): void
    {
        Schema::dropIfExists('confectioneries');
    }
};