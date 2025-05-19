<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DepositHistoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('user')->group(function () {
    Route::resource('categories', CategoriesController::class)->only(['index', 'show']);
});


Route::prefix('admin')->group(function () {
    Route::resource('categories', CategoriesController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('deposits', DepositHistoryController::class)->except(['show']);
    Route::get('/users', [DepositHistoryController::class, 'users']);
});
