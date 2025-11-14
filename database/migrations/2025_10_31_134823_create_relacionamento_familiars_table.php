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
        Schema::create('relacionamentos_familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parente_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo'); // ex: pai, mãe, relacao, cônjuge, irmão
            $table->timestamps();

            $table->unique(['user_id', 'parente_id', 'tipo']); // evita duplicidade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacionamentos_familiares');
    }
};
