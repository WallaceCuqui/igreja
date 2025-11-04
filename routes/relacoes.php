<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profile\RelacoesController;

Route::middleware(['auth'])->group(function () {
    // Página única de relações
    Route::get('/profile/relacoes', [RelacoesController::class, 'index'])
        ->name('profile.relacoes');

    // Cadastrar relação/dependente
    Route::post('/profile/relacoes', [RelacoesController::class, 'storeRelacao'])
        ->name('profile.relacoes.store');

    // Vincular relação já cadastrada
    Route::post('/profile/relacoes/vincular', [RelacoesController::class, 'vincularRelacao'])
        ->name('profile.relacoes.vincular');

    // Editar e remover relação
    Route::get('/profile/relacoes/{relacao}/edit', [RelacoesController::class, 'editRelacao'])
        ->name('profile.relacoes.edit');
    Route::patch('/profile/relacoes/{relacao}', [RelacoesController::class, 'updateRelacao'])
        ->name('profile.relacoes.update');
    Route::delete('/profile/relacoes/{relacao}', [RelacoesController::class, 'destroyRelacao'])
        ->name('profile.relacoes.destroy');
});
