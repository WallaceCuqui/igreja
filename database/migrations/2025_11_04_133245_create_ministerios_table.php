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
        Schema::create('ministerios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->unsignedTinyInteger('idade_min')->nullable();
            $table->unsignedTinyInteger('idade_max')->nullable();
            $table->enum('genero', ['todos', 'masculino', 'feminino'])
                  ->default('todos')
                  ->after('politica_ingresso');
            $table->enum('politica_ingresso', ['aberto', 'restrito'])
                ->default('restrito');
            $table->date('data_fundacao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->foreignId('igreja_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministerios');
    }
};
