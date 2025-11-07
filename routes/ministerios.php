<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    MinisterioController,
    LiderancaController,
    ComissaoController,
    IntegranteMinisterioController,
    AgendaController
};

/*
|--------------------------------------------------------------------------
| Rotas do Módulo de Ministérios
|--------------------------------------------------------------------------
|
| Todas as rotas deste módulo são protegidas pelo middleware 'auth'.
|
*/

Route::middleware(['auth'])->prefix('ministerios')->name('ministerios.')->group(function () {

    // Lista e criação de ministérios
    Route::get('/', [MinisterioController::class, 'index'])->name('index');
    Route::post('/', [MinisterioController::class, 'store'])->name('store');

    // Rotas para um ministério específico (show, update, destroy)
    Route::prefix('/{ministerio}')
        ->whereNumber('ministerio')
        ->group(function () {
            Route::get('/', [MinisterioController::class, 'show'])->name('show');
            Route::put('/', [MinisterioController::class, 'update'])->name('update');
            Route::delete('/', [MinisterioController::class, 'destroy'])->name('destroy');

            // Integrantes do ministério
            Route::prefix('integrantes')->name('integrantes.')->group(function () {
                Route::get('/', [IntegranteMinisterioController::class, 'index'])->name('index');
                Route::post('/', [IntegranteMinisterioController::class, 'store'])->name('store');
                Route::put('/{integrante}', [IntegranteMinisterioController::class, 'update'])->whereNumber('integrante')->name('update');

                Route::post('{membro}/ativar', [IntegranteMinisterioController::class, 'ativar'])->name('ativar');
                Route::delete('{membro}/remover', [IntegranteMinisterioController::class, 'remover'])->name('remover');
            });

            // Lideranças do ministério
            Route::prefix('liderancas')->name('liderancas.')->group(function () {
                Route::get('/', [LiderancaController::class, 'index'])->name('index');
                Route::post('/', [LiderancaController::class, 'store'])->name('store');
                Route::get('/{lideranca}', [LiderancaController::class, 'show'])->whereNumber('lideranca')->name('show');
                Route::put('/{lideranca}', [LiderancaController::class, 'update'])->whereNumber('lideranca')->name('update');
                Route::delete('/{lideranca}', [LiderancaController::class, 'destroy'])->whereNumber('lideranca')->name('destroy');
            });

            // Comissões do ministério
            Route::prefix('comissoes')->name('comissoes.')->group(function () {
                Route::get('/', [ComissaoController::class, 'index'])->name('index');
                Route::post('/', [ComissaoController::class, 'store'])->name('store');
                Route::get('/{comissao}', [ComissaoController::class, 'show'])->whereNumber('comissao')->name('show');
                Route::put('/{comissao}', [ComissaoController::class, 'update'])->whereNumber('comissao')->name('update');
                Route::delete('/{comissao}', [ComissaoController::class, 'destroy'])->whereNumber('comissao')->name('destroy');
            });

            // Agendas do ministério
            Route::prefix('agendas')->name('agendas.')->group(function () {
                Route::get('/', [AgendaController::class, 'index'])->name('index');
                Route::post('/', [AgendaController::class, 'store'])->name('store');
                Route::get('/{agenda}', [AgendaController::class, 'show'])->whereNumber('agenda')->name('show');
                Route::put('/{agenda}', [AgendaController::class, 'update'])->whereNumber('agenda')->name('update');
                Route::delete('/{agenda}', [AgendaController::class, 'destroy'])->whereNumber('agenda')->name('destroy');
            });
        });

});
