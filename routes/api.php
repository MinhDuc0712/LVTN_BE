<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UtilitiesController;
use App\Http\Controllers\DepositHistoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\RoleController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('/changePassword', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('user')->group(function () {
    Route::resource('categories', CategoriesController::class)->only(['index', 'show']);
    Route::resource('utilities', UtilitiesController::class)->only(['index', 'show']);
    Route::post('/houses/store', [HouseController::class, 'store'])->name('houses.store');
Route::post('/houses/{id}/process-payment', [HouseController::class, 'processPayment'])->name('houses.payment.process');
});

Route::prefix('admin')->group(function () {
    Route::apiResource('deposits', DepositHistoryController::class)->except(['show']);
    Route::apiResource('user', UserController::class);
    Route::resource('categories', CategoriesController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('utilities', UtilitiesController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('users/{identifier}', [UserController::class, 'findUser']);    
});

