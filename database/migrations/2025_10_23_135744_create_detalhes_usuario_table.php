<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalhes_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Identificação pessoal ou empresarial
            $table->string('nome_fantasia')->nullable();       // Nome fantasia (para empresas)
            $table->string('documento')->nullable()->unique(); // CPF ou CNPJ
            $table->enum('genero', ['Masculino', 'Feminino', 'Outro'])->nullable();
            $table->date('data_nascimento')->nullable();

            // Endereço
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('cep')->nullable();

            // Contato
            $table->string('telefone')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalhes_usuario');
    }
};
