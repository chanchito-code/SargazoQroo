<?php

use App\Http\Controllers\Api\PrediccionController;
use App\Http\Controllers\Api\SimuladorController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/api/prediccion', PrediccionController::class)->name('api.prediccion');
Route::post('/api/simular', SimuladorController::class)->name('api.simular');
