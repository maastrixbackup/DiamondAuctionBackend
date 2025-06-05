<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BidderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LotController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/optimize', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return 'Command executed successfully!';
});
Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::prefix('admin')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');

    Route::middleware(['admin', 'admin.role:superadmin'])->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [DashboardController::class, 'profileDetails'])->name('admin.profile');
        Route::resource('lots', LotController::class)->names('admin.lots');
        Route::get('/seller', [SellerController::class, 'sellerList'])->name('admin.seller');
        Route::get('/sellerDetails/{id}', [SellerController::class, 'sellerDetails'])->name('admin.sellerDetails');
        // Route::get('change-seller-status/{id}', [SellerController::class, 'changeSellerStatus'])->name('admin.change-seller-status');
        Route::get('change-seller-kyc-status/{id}', [SellerController::class, 'changeSellerKycStatus'])->name('admin.change-seller-kyc-status');
        Route::get('change-seller-account-status/{id}/{status}', [SellerController::class, 'changeSellerAccountStatus'])->name('admin.change-seller-account-status');
        Route::resource('category', CategoryController::class)->names('admin.category');
        Route::get('/bidder', [BidderController::class, 'bidderList'])->name('admin.bidder');
        Route::get('/bidderDetails/{id}', [BidderController::class, 'bidderDetails'])->name('admin.bidderDetails');
        Route::get('change-bidder-kyc-status/{id}', [BidderController::class, 'changeBidderKycStatus'])->name('admin.change-bidder-kyc-status');
        Route::get('change-bidder-account-status/{id}/{status}', [BidderController::class, 'changeBidderAccountStatus'])->name('admin.change-bidder-account-status');
        Route::get('/admin', [AdminController::class, 'adminList'])->name('admin.admin');
        Route::get('/adminDetails/{id}', [AdminController::class, 'adminDetails'])->name('admin.adminDetails');
        Route::get('/viewingRequest', [LotController::class, 'viewingRequest'])->name('admin.viewingRequest');
        Route::get('/viewingRequestLots/{slotId}', [LotController::class, 'viewingRequestLots'])->name('admin.viewingRequestLots');
        // Route::post('/update-request-lots-status/{bookingId}', [LotController::class, 'updateRequestLotStatus'])->name('admin.update-request-lots-status');
        Route::post('/updateLotsStatus', [LotController::class, 'updateLotsStatus'])->name('admin.updateLotsStatus');
        Route::post('/update-seller-document-status', [SellerController::class, 'updateSellerDocumentStatus'])->name('admin.update-seller-document-status');
        Route::post('/update-bidder-document-status', [BidderController::class, 'updateBidderDocumentStatus'])->name('admin.update-bidder-document-status');

        // Route::get('/profile', [UserController::class, 'index'])->name('admin.profile');
    });
});

require __DIR__ . '/auth.php';
