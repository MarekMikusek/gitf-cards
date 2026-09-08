<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/me', [AuthController::class,'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
