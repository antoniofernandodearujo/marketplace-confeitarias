<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ConfectioneryController;
use App\Http\Controllers\ProductController;

Route::prefix('confectioneries')->group(function () {
    Route::get('/', [ConfectioneryController::class, 'index']);
    Route::post('/', [ConfectioneryController::class, 'store']);
    Route::get('/{confectionery}', [ConfectioneryController::class, 'show']);
    Route::patch('/{confectionery}', [ConfectioneryController::class, 'update']);
    Route::delete('/{confectionery}', [ConfectioneryController::class, 'destroy']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::post('/', [ProductController::class, 'store']);
    Route::patch('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'destroy']);
});

Route::get('/address/cep/{cep}', [AddressController::class, 'fetchAddressByCep']);