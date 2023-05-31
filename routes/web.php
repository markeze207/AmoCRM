<?php

use App\RMVC\Route\Route;

Route::get('/amoCRM', [\App\Http\Controllers\MainController::class, 'index']);
Route::post('/store', [\App\Http\Controllers\MainController::class, 'store']);