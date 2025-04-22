<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ConfectioneryController;
use App\Http\Controllers\ProductController;

Route::get('/confectioneries', [ConfectioneryController::class, 'index']);
Route::post('/confectioneries', [ConfectioneryController::class, 'store']);
Route::get('/confectioneries/{confectionery}', [ConfectioneryController::class, 'show']);
Route::put('/confectioneries/{confectionery}', [ConfectioneryController::class, 'update']);
Route::delete('/confectioneries/{confectionery}', [ConfectioneryController::class, 'destroy']);

// Product routes
Route::get('/products', [ProductController::class, 'index']); // Added missing GET route for listing products
Route::get('/products/{product}', [ProductController::class, 'show']); // Added missing GET route for single product
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']); // Changed match to specific PUT route
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::get('/address/cep/{cep}', [AddressController::class, 'fetchAddressByCep']);