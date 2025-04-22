<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfectioneryController;
use App\Http\Controllers\ProductController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::prefix('confectioneries')->group(function () {
    Route::get('/', [ConfectioneryController::class, 'index']);
    Route::post('/', [ConfectioneryController::class, 'store']);
    Route::get('/{confectionery}', [ConfectioneryController::class, 'show']);
    Route::put('/{confectionery}', [ConfectioneryController::class, 'update']);
    Route::delete('/{confectionery}', [ConfectioneryController::class, 'destroy']);
});

Route::prefix('products')->group(function () {
    Route::post('/', [ProductController::class, 'store']);
    Route::put('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'destroy']);
});
