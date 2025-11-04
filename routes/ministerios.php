<?php

//dd('Carregou o arquivo de ministérios!');


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
| Todas as rotas deste módulo são protegidas pelo middleware 'auth',
| garantindo que apenas usuários autenticados possam acessá-las.
|
*/

Route::middleware(['auth'])->prefix('ministerios')->name('ministerios.')->group(function () {

    // 🧱 Ministério
    Route::get('/', [MinisterioController::class, 'index'])->name('index');
    Route::post('/', [MinisterioController::class, 'store'])->name('store');
    Route::put('/{ministerio}', [MinisterioController::class, 'update'])->name('update');
    Route::delete('/{ministerio}', [MinisterioController::class, 'destroy'])->name('destroy');

    // 👤 Liderança
    Route::prefix('liderancas')->name('liderancas.')->group(function () {
        Route::get('/', [LiderancaController::class, 'index'])->name('index');
        Route::post('/', [LiderancaController::class, 'store'])->name('store');
        Route::get('/{lideranca}', [LiderancaController::class, 'show'])->name('show');
        Route::put('/{lideranca}', [LiderancaController::class, 'update'])->name('update');
        Route::delete('/{lideranca}', [LiderancaController::class, 'destroy'])->name('destroy');
    });

    // 🧑‍🤝‍🧑 Comissão
    Route::prefix('comissoes')->name('comissoes.')->group(function () {
        Route::get('/', [ComissaoController::class, 'index'])->name('index');
        Route::post('/', [ComissaoController::class, 'store'])->name('store');
        Route::get('/{comissao}', [ComissaoController::class, 'show'])->name('show');
        Route::put('/{comissao}', [ComissaoController::class, 'update'])->name('update');
        Route::delete('/{comissao}', [ComissaoController::class, 'destroy'])->name('destroy');
    });

    // 👥 Integrantes
    Route::prefix('integrantes')->name('integrantes.')->group(function () {
        Route::get('/', [IntegranteMinisterioController::class, 'index'])->name('index');
        Route::post('/', [IntegranteMinisterioController::class, 'store'])->name('store');
        Route::get('/{integrante}', [IntegranteMinisterioController::class, 'show'])->name('show');
        Route::put('/{integrante}', [IntegranteMinisterioController::class, 'update'])->name('update');
        Route::delete('/{integrante}', [IntegranteMinisterioController::class, 'destroy'])->name('destroy');
    });

    // 📅 Agenda
    Route::prefix('agendas')->name('agendas.')->group(function () {
        Route::get('/', [AgendaController::class, 'index'])->name('index');
        Route::post('/', [AgendaController::class, 'store'])->name('store');
        Route::get('/{agenda}', [AgendaController::class, 'show'])->name('show');
        Route::put('/{agenda}', [AgendaController::class, 'update'])->name('update');
        Route::delete('/{agenda}', [AgendaController::class, 'destroy'])->name('destroy');
    });

    // ⚠️ Essa precisa vir POR ÚLTIMO
    Route::get('/{ministerio}', [MinisterioController::class, 'show'])->name('show');


});


