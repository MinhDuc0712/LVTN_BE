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
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\PaymentsController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('/updateProfile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
    Route::post('/changePassword', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('user')->group(function () {
    Route::resource('categories', CategoriesController::class)->only(['index']);
    Route::get('/categories/{id}', [CategoriesController::class, 'show']);
    Route::resource('utilities', UtilitiesController::class)->only(['index', 'show']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/houses', [HouseController::class, 'store']);
        // Route::apiResource('user', UserController::class);
        Route::get('/me', function (Request $request) {
            return response()->json([
                'user' => $request->user(),
                'MaNguoiDung' => $request->user()->MaNguoiDung,
                'HoTen' => $request->user()->HoTen,
                'SDT' => $request->user()->SDT,
                'so_du' => $request->user()->so_du,
                'HinhDaiDien' => $request->user()->HinhDaiDien,
            ]);
        });
        Route::post('/houses/payment', [HouseController::class, 'handlePayment']);
        Route::get('/payments', [PaymentsController::class, 'getUserPayments']);
        Route::get('/houses/user-posts', [HouseController::class, 'getUserHouses']);
        // Route::get('/houses/{id}', [HouseController::class, 'show']);
        // Route::put('/houses/{id}', [HouseController::class, 'update']);
        Route::apiResource('/ratings', RatingController::class);
    });
    
    // Route::get('/ratings', [RatingController::class, 'index'])
    // Route::get('/houses', [HouseController::class, 'index']);
    // Route::get('/houses/{id}', [HouseController::class, 'show']);
    Route::get('/houses/featured', [HouseController::class, 'featured']);
    Route::get('/houses/category/{id}', [HouseController::class, 'getByCategory']);
    Route::apiResource('/houses', HouseController::class)->only(['index', 'show']);
});

Route::prefix('admin')->group(function () {
    Route::apiResource('deposits', DepositHistoryController::class)->except(['show']);
    Route::apiResource('user', UserController::class);
    Route::resource('categories', CategoriesController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/user/{id}/ban', [UserController::class, 'ban']);
    Route::post('/user/{id}/unban', [UserController::class, 'unban']);
    Route::resource('utilities', UtilitiesController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('users/{identifier}', [UserController::class, 'findUser']);
    Route::apiResource('roles', RoleController::class)->except(['show', 'edit']);
    Route::get('/houses', [HouseController::class, 'getAllForAdmin']);
    Route::put('/houses/{id}/approve', [HouseController::class, 'approve']);
    Route::post('/houses/{id}/reject', [HouseController::class, 'reject']);
});
