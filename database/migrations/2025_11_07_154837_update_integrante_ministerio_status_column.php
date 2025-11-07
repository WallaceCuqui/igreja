<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('integrante_ministerio', function (Blueprint $table) {
            // Remove a coluna antiga
            $table->dropColumn('tipo_vinculo');

            // Adiciona a nova coluna de status
            $table->enum('status', ['pendente', 'ativo', 'inativo'])->default('pendente')->after('membro_id');
        });
    }

    public function down(): void
    {
        Schema::table('integrante_ministerio', function (Blueprint $table) {
            // Remove a nova coluna
            $table->dropColumn('status');

            // Restaura a coluna antiga
            $table->enum('tipo_vinculo', ['ativo', 'auxiliar', 'visitante', 'ex-integrante'])->default('ativo')->after('membro_id');
        });
    }
};
