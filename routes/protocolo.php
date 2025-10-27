<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProtocoloController;

Route::prefix('protocolo')->group(function () {
    Route::get('/abrir', [ProtocoloController::class, 'create'])->name('protocolo.create');
    Route::post('/abrir', [ProtocoloController::class, 'store'])->name('protocolo.store');
    Route::get('/{protocolo}', [ProtocoloController::class, 'show'])->name('protocolo.show');
    Route::post('/protocolo/{protocolo}/responder', [ProtocoloController::class, 'responder'])
        ->name('protocolo.responder');

});
