<?php

use App\Http\Controllers\Api\BidderController;
use App\Http\Controllers\Api\SellerController;
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



Route::post('/createSeller', [SellerController::class, 'createSeller']);
Route::post('/createBidder', [BidderController::class, 'createBidder']);
Route::post('/sellerlogin', [SellerController::class, 'sellerLogin']);
Route::post('/bidderlogin', [BidderController::class, 'bidderLogin']);

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
    Route::get('/get-bidder-assigned-slots', [BidderController::class, 'getBidderAssignedSlots']);
    Route::get('/bidder-assigned-lots-by-slots/{slotId}', [BidderController::class, 'bidderAssignedLotsBySlot']);
    Route::post('/reupload-bidder-document', [BidderController::class, 'reuploadBidderDocument'])->name('admin.reupload-bidder-document');
    Route::post('/reupload-seller-document', [SellerController::class, 'reuploadSellerDocument'])->name('admin.reupload-seller-document');

    // Bibhu
    Route::get('/get-booking-details/{booking_id}', [BidderController::class, 'getBookingDetails']);
    Route::post('/update-bid-details', [BidderController::class, 'updateBiddetails']);
});
