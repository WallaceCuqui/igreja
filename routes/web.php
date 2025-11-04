<?php


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// Rotas linkadas ao módulo
require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
require __DIR__.'/protocolo.php';
require __DIR__.'/notificacao.php';
require __DIR__.'/relacoes.php';
require __DIR__.'/ministerios.php';

