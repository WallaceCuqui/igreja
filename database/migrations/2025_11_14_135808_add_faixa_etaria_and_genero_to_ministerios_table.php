<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {

            // Faixa etária (flexível)
            $table->unsignedTinyInteger('idade_min')->nullable()->after('descricao');
            $table->unsignedTinyInteger('idade_max')->nullable()->after('idade_min');

            // Gênero do ministério
            $table->enum('genero', ['todos', 'masculino', 'feminino'])
                  ->default('todos')
                  ->after('politica_ingresso');
        });
    }

    public function down(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {
            $table->dropColumn(['idade_min', 'idade_max', 'genero']);
        });
    }
};
