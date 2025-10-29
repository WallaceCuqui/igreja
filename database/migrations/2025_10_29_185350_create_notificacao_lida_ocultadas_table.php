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
        Schema::create('notificacao_lida_ocultadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notificacao_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('lida')->default(false);
            $table->boolean('ocultada')->default(false);
            $table->timestamp('lida_at')->nullable();
            $table->timestamp('ocultada_at')->nullable();
            $table->timestamps();

            $table->unique(['notificacao_id','user_id']);
            $table->foreign('notificacao_id')->references('id')->on('notificacoes')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_lida_ocultadas');
    }
};
