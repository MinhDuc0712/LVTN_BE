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
use App\Http\Controllers\FavouriteHouseController;
use App\Http\Controllers\ZaloPayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\KhachController;
use App\Http\Controllers\HopdongController;
use App\Http\Controllers\GiadichvuController;
use App\Http\Controllers\PhieudienController;
use App\Http\Controllers\PhieunuocController;
use App\Http\Controllers\phieuthutienController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('/updateProfile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
    Route::post('/changePassword', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('user')->group(function () {
    Route::resource('categories', CategoriesController::class)
        ->only(['index'])
        ->names([
            'index' => 'user.categories.index',
        ]);
    Route::get('/categories/{id}', [CategoriesController::class, 'show']);
    Route::resource('utilities', UtilitiesController::class)->only(['index', 'show']);
    Route::get('deposits/check/{ma_giao_dich}', [DepositHistoryController::class, 'checkTransaction']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/houses', [HouseController::class, 'store']);
        Route::put('/houses/{id}', [HouseController::class, 'update']);

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
        Route::apiResource('deposits', DepositHistoryController::class)->names([
            'index' => 'user.deposits.index',
            'store' => 'user.deposits.store',
            'show' => 'user.deposits.show',
            'update' => 'user.deposits.update',
            'destroy' => 'user.deposits.destroy',
        ]);

        Route::post('/houses/payment', [HouseController::class, 'handlePayment']);
        Route::get('/houses/user-posts', [HouseController::class, 'getUserHouses']);
        Route::put('/houses/{id}/hide', [HouseController::class, 'hide']);
        Route::put('/houses/{id}/relist', [HouseController::class, 'relist']);
        // Route::get('/houses/{id}', [HouseController::class, 'show']);
        // Route::put('/houses/{id}', [HouseController::class, 'update']);
        Route::apiResource('/ratings', RatingController::class);
        Route::apiResource('/favorites', FavouriteHouseController::class);
        Route::apiResource('/payments', PaymentsController::class);
        Route::post('/wallet-payments', [PaymentsController::class, 'storeWalletPayment']);
    });

    Route::post('/zalopay/create-payment', [ZaloPayController::class, 'createPayment']);
    Route::post('/zalopay/callback', [ZaloPayController::class, 'handleCallback'])->name('zalopay.callback');
    Route::get('/zalopay/check-transaction/{ma_giao_dich}', [ZaloPayController::class, 'checkZaloTransaction']);

    // Route::get('/ratings', [RatingController::class, 'index'])
    // Route::get('/houses', [HouseController::class, 'index']);
    // Route::get('/houses/{id}', [HouseController::class, 'show']);
    Route::get('/houses/featured', [HouseController::class, 'featured']);
    Route::get('/houses/category/{id}', [HouseController::class, 'getByCategory']);
    Route::apiResource('/houses', HouseController::class)->only(['index', 'show']);
    Route::apiResource('rooms', PhongController::class);
    Route::apiResource('khach', KhachController::class);
    Route::apiResource('hopdong', HopdongController::class);
});

Route::prefix('admin')->group(function () {
    Route::apiResource('deposits', DepositHistoryController::class)
        ->except(['show'])
        ->names([
            'index' => 'admin.deposits.index',
            'store' => 'admin.deposits.store',
            'update' => 'admin.deposits.update',
            'destroy' => 'admin.deposits.destroy',
        ]);
    Route::apiResource('user', UserController::class);
    Route::resource('categories', CategoriesController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'admin.categories.index',
            'store' => 'admin.categories.store',
            'update' => 'admin.categories.update',
            'destroy' => 'admin.categories.destroy',
        ]);
    Route::post('/user/{id}/ban', [UserController::class, 'ban']);
    Route::post('/user/{id}/unban', [UserController::class, 'unban']);
    Route::resource('utilities', UtilitiesController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'admin.utilities.index',
            'store' => 'admin.utilities.store',
            'update' => 'admin.utilities.update',
            'destroy' => 'admin.utilities.destroy',
        ]);
    Route::get('users/{identifier}', [UserController::class, 'findUser']);
    Route::apiResource('roles', RoleController::class);
    Route::get('/houses', [HouseController::class, 'getAllForAdmin']);
    Route::put('/houses/{id}/approve', [HouseController::class, 'approve']);
    Route::post('/houses/{id}/reject', [HouseController::class, 'reject']);
    Route::get('/dashboard-stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard-charts', [DashboardController::class, 'charts']);
    Route::apiResource('rooms', PhongController::class);
    Route::delete('/rooms/{id}', [PhongController::class, 'destroy']);
    Route::post('rooms/{phong}/images', [PhongController::class, 'uploadImages'])->name('admin.rooms.uploadImages');
    Route::delete('room-images/{id}', [PhongController::class, 'destroyImage']);
    Route::apiResource('hopdong', HopdongController::class);
    Route::apiResource('giadichvu', GiadichvuController::class);
    Route::apiResource('electric-bills', PhieudienController::class);
    Route::get('/electric/last-reading/{hopdong_id}', [PhieuDienController::class, 'getLastReading']);
    Route::put('/electric-bills/{id}/toggle-status', [PhieudienController::class, 'toggleStatus']);
    Route::apiResource('water-bills', PhieunuocController::class);
    Route::get('/water/last-reading/{hopdong_id}', [PhieunuocController::class, 'getLastReading']);
    Route::put('/water/{id}/toggle-status', [PhieunuocController::class, 'toggleStatus']);
    Route::apiResource('phieuthutien', phieuthutienController::class);
    Route::delete('phieuthutien/{id}', [phieuthutienController::class, 'destroy']);
    Route::put('phieuthutien/{id}', [phieuthutienController::class, 'update']);

});
