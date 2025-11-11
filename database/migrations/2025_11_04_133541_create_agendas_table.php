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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministerio_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('local')->nullable();
            $table->datetime('data_inicio');
            $table->datetime('data_fim')->nullable();
            $table->foreignId('criado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['planejado', 'realizado', 'cancelado'])->default('planejado');
            $table->string('tipo_evento')->nullable(); // reunião, ensaio, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
