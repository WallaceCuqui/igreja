<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('protocolos', function (Blueprint $table) {
            $table->id();
            $table->string('protocolo')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nome');
            $table->string('email');
            $table->string('assunto');
            $table->text('mensagem');
            $table->enum('status', ['aberto', 'em_atendimento', 'concluido', 'cancelado'])->default('aberto');
            $table->foreignId('atendido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocolos');
    }
};
