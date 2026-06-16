<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\PenyiramanController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthApiController::class, 'register']);
Route::post('/auth/login', [AuthApiController::class, 'login']);
Route::post('/auth/one-tap', [AuthApiController::class, 'oneTapLogin']);
Route::get('/auth/me', [AuthApiController::class, 'me']);
Route::post('/auth/logout', [AuthApiController::class, 'logout']);


