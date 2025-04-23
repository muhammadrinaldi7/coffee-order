<?php

use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\TableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/tables', TableController::class);
Route::get('/tables/{id}/print-qr', [TableController::class, 'printQrCode']);
Route::apiResource('/menus', MenuController::class);
Route::apiResource('/order', OrderController::class);
Route::post('/pay', [PaymentController::class, 'createCharge']);
Route::post('/create-order', [OrderController::class, 'buatPesanan']);
Route::apiResource('/categories', CategoriesController::class);
Route::get('/orders/table/{table_number}', [OrderController::class, 'showByTable']);
