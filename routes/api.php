<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CategoriesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('user')->group(function () {
    Route::resource('categories', CategoriesController::class)->only(['index', 'show']);

});


Route::prefix('admin')->group(function () {
    Route::resource('categories', CategoriesController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('deposit-history', [DepositController::class, 'store']);
});
