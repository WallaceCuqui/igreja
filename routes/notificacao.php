<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificacaoController;

Route::middleware('auth')->group(function () {
    Route::get('/notificacoes/lista', [NotificacaoController::class, 'lista'])->name('notificacoes.lista');
    Route::get('/notificacoes/count', [NotificacaoController::class, 'count'])->name('notificacoes.count');
    Route::post('/notificacoes/marcar-todas-lidas', [NotificacaoController::class, 'marcarTodasLidas'])->name('notificacoes.marcarTodasLidas');
    Route::post('/notificacoes/{id}/ocultar', [NotificacaoController::class, 'ocultar'])->name('notificacoes.ocultar');
});


