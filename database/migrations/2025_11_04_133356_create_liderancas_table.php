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
        Schema::create('liderancas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministerio_id')->constrained()->onDelete('cascade');
            $table->foreignId('lider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vice_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liderancas');
    }
};
