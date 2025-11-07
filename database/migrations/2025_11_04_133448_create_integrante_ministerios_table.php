<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('integrante_ministerio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministerio_id')->constrained()->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pendente', 'ativo', 'inativo'])->default('pendente');
            $table->date('data_entrada')->nullable();
            $table->date('data_saida')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrante_ministerios');
    }
};
