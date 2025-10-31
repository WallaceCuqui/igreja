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
        Schema::create('relacao_ministerio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relacao_id')->constrained('relacoes')->cascadeOnDelete();
            $table->foreignId('ministerio_id')->constrained('ministerios')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacao_ministerio');
    }
};
