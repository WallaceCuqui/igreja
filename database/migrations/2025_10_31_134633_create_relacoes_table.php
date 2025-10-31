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
        Schema::create('relacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membro_id')->constrained('users')->onDelete('cascade'); // pai/mãe responsável
            $table->foreignId('relacionado_id')->nullable()->constrained('users')->onDelete('cascade'); // se tiver login
            $table->string('nome'); // nome da pessoa (dependente ou outro tipo de relação)
            $table->date('data_nascimento')->nullable();
            $table->enum('sexo', ['M','F'])->nullable();
            $table->string('tipo')->nullable(); // filho, cônjuge, outro
            $table->string('foto')->nullable(); // foto do perfil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacoes');
    }
};
