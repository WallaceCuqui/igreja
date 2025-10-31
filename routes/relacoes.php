<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profile\RelacoesController;

Route::middleware(['auth'])->group(function () {
    // Página única de relações
    Route::get('/profile/relacoes', [RelacoesController::class, 'index'])->name('profile.relacoes');

    // Cadastrar relacao/dependente
    Route::post('/profile/relacoes', [RelacoesController::class, 'storeRelacao'])->name('profile.relacoes.store');

    // Vincular adolescente já cadastrado
    Route::post('/profile/relacoes/vincular', [RelacoesController::class, 'vincularAdolescente'])->name('profile.relacoes.vincular');

    // Editar/Remover relacao (opcional)
    Route::delete('/profile/relacoes/{relacao}', [RelacoesController::class, 'destroyRelacao'])->name('profile.relacoes.destroy');
    Route::get('/profile/relacoes/{relacao}/edit', [RelacoesController::class, 'editRelacao'])->name('profile.relacoes.edit');
    Route::patch('/profile/relacoes/{relacao}', [RelacoesController::class, 'updateRelacao'])->name('profile.relacoes.update');
});