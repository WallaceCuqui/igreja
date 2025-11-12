<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notificacoes', function (Blueprint $table) {
            // Adiciona o campo ministerio_id
            $table->foreignId('ministerio_id')
                ->nullable()
                ->after('target_user_id')
                ->constrained('ministerios')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notificacoes', function (Blueprint $table) {
            // Remove a constraint e a coluna caso o migration seja revertido
            $table->dropForeign(['ministerio_id']);
            $table->dropColumn('ministerio_id');
        });
    }
};
