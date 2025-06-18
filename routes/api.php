<?php

use App\Http\Controllers\Api\BidderController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\ZoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Bidder
Route::post('/createBidder', [BidderController::class, 'createBidder']);
Route::post('/bidderlogin', [BidderController::class, 'bidderLogin']);
Route::post('/bidder-password-reset', [BidderController::class, 'forgotPassword']);
Route::post('/bidder-reset-password', [BidderController::class, 'resetPassword']);

// Seller
Route::post('/createSeller', [SellerController::class, 'createSeller']);
Route::post('/sellerlogin', [SellerController::class, 'sellerLogin']);
Route::post('/seller-password-reset', [SellerController::class, 'forgotPassword']);
Route::post('/seller-reset-password', [SellerController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/sellerdashboard', [SellerController::class, 'sellerDashboard']);
    Route::post('/bidderdashboard', [BidderController::class, 'bidderDashboard']);
    Route::post('/bidderlogout', [BidderController::class, 'bidderLogout']);
    Route::post('/sellerlogout', [SellerController::class, 'sellerLogout']);
    Route::get('/view-seller-lot', [SellerController::class, 'viewSellerLots']);
    Route::get('/seller-lot-details/{id}', [SellerController::class, 'sellerLotDetails']);
    Route::post('/available-slots', [BidderController::class, 'availableSlots']);
    Route::post('/available-lots', [BidderController::class, 'availableLots']);
    Route::get('/get-bidder-slots', [BidderController::class, 'getBidderSlots']);
    Route::post('/slot-booking', [BidderController::class, 'slotBooking']);
    Route::get('/bidder-assigned-lots-by-slots/{slotId}', [BidderController::class, 'bidderAssignedLotsBySlot']);
    Route::post('/reupload-bidder-document', [BidderController::class, 'reuploadBidderDocument']);
    Route::post('/reupload-seller-document', [SellerController::class, 'reuploadSellerDocument']);
    Route::post('/bidder-change-password', [BidderController::class, 'bidderChangePassword']);
    Route::post('/seller-change-password', [SellerController::class, 'sellerChangePassword']);

    // Bibhu
    Route::get('/get-booking-details/{booking_id}', [BidderController::class, 'getBookingDetails']);
    Route::post('/store-requested-lots', [BidderController::class, 'requestedLots']);
    Route::get('/lots-bid-details', [SellerController::class, 'getLotsBidDetails']);
    Route::post('/update-bid-details', [BidderController::class, 'updateBiddetails']);
    Route::post('/get-bidding-history', [BidderController::class, 'getBiddingHistory']);
    Route::post('/zoom-signature', [ZoomController::class, 'generateSignature']);
});
