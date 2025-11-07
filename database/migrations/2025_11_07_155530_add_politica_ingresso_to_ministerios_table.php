<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {
            $table->enum('politica_ingresso', ['aberto', 'restrito'])
                ->default('restrito')
                ->after('descricao');
        });
    }

    public function down(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {
            $table->dropColumn('politica_ingresso');
        });
    }
};
