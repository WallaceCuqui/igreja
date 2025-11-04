<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IgrejaController;
use App\Http\Controllers\MembroController;

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/membros/search', [ProfileController::class, 'buscaMembro'])->name('membro.busca');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/igrejas/buscar', [IgrejaController::class, 'buscar'])->name('igrejas.buscar');

    


});
