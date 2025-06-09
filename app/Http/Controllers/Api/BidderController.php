<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidder;
use App\Models\Booking;
use App\Models\Lot;
use App\Models\Room;
use App\Models\Slot;
use App\Models\SlotBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BidderController extends Controller
{
    public function createBidder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'type' => 'required|in:company,individual',

            'name' => 'required|string',
            'email' => 'required|email|unique:bidders,email_address',
            'phone' => 'required|string',
            'country' => 'required|string',
            'password' => 'required|string|min:6',

            // Company fields
            'company_name' => 'required_if:type,company|string',
            'registration_number' => 'required_if:type,company|string',
            'director_name' => 'required_if:type,company|string',
            'director_email' => 'required_if:type,company|email',
            'director_phone' => 'required_if:type,company|string',
            'certificate_of_incorporation' => 'required_if:type,company|file',
            'valid_trade_license' => 'required_if:type,company|file',
            'passport_copy_authorised' => 'required_if:type,company|file',
            'ubo_declaration' => 'required_if:type,company|file',

            // Individual fields
            'passport_copy' => 'required_if:type,individual|file',
            'proof_of_address' => 'required_if:type,individual|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bidder = new Bidder();

        // Common fields
        $bidder->type = $request->type == "company" ? 1 : 2;
        $bidder->full_name = $request->name;
        $bidder->email_address = $request->email;
        $bidder->phone_number = $request->phone;
        $bidder->country = $request->country;
        $bidder->password = Hash::make($request->password);
        $bidder->kyc_status = 0;
        $bidder->account_status = 0;

        $destinationPath = public_path('storage/document/bidder/');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $handleUpload = function ($field) use ($request, $destinationPath) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                return $filename;
            }
            return null;
        };

        // Company-specific logic
        if ($request->type == "company") {
            $bidder->company_name = $request->company_name;
            $bidder->registration_number = $request->registration_number;
            $bidder->director_name = $request->director_name;
            $bidder->director_email = $request->director_email;
            $bidder->director_phone = $request->director_phone;

            $bidder->certificate_of_incorporation = $handleUpload('certificate_of_incorporation');
            $bidder->certificate_of_incorporation_status = 0;

            $bidder->valid_trade_license = $handleUpload('valid_trade_license');
            $bidder->valid_trade_license_status = 0;

            $bidder->passport_copy_authorised = $handleUpload('passport_copy_authorised');
            $bidder->passport_copy_authorised_status = 0;

            $bidder->ubo_declaration = $handleUpload('ubo_declaration');
            $bidder->ubo_declaration_status = 0;
        }

        // Individual-specific logic
        if ($request->type == "individual") {
            $bidder->passport_copy = $handleUpload('passport_copy');
            $bidder->passport_copy_status = 0;

            $bidder->proof_of_address = $handleUpload('proof_of_address');
            $bidder->proof_of_address_status = 0;
        }

        $bidder->save();

        return response()->json([
            'status' => true,
            'message' => 'Bidder created successfully',
            'data' => $bidder
        ], 201);
    }

    public function bidderLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_address' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $bidder = Bidder::where('email_address', $request->email_address)->first();
        if (!$bidder || !Hash::check($request->password, $bidder->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $token = $bidder->createToken('bidder_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $bidder,
        ], 200);
    }

    public function bidderDashboard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:bidders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $bidder = Bidder::find($request->id);
        $dashboardData = [
            'full_name' => $bidder->full_name,
            'email_address' => $bidder->email_address,
        ];
        return response()->json([
            'status' => true,
            'message' => 'Bidder dashboard data fetched successfully.',
            'data' => $dashboardData,
        ], 200);
    }

    public function reuploadBidderDocument(Request $request)
    {
        $request->validate([
            'bidder_id' => 'required|exists:bidders,id',
            'field' => 'required|string|in:certificate_of_incorporation,valid_trade_license,passport_copy_authorised,ubo_declaration,passport_copy,proof_of_ownership',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $bidder = Bidder::findOrFail($request->bidder_id);
        $field = $request->field;
        $statusField = $field . '_status';

        if ($bidder->$statusField != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Only rejected documents can be re-uploaded.',
            ], 403);
        }

        $destinationPath = public_path('storage/document/bidder/');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if ($bidder->$field && file_exists($destinationPath . $bidder->$field)) {
            unlink($destinationPath . $bidder->$field);
        }

        $file = $request->file('document');
        $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $filename);

        $bidder->$field = $filename;
        $bidder->$statusField = 0;
        $bidder->kyc_status = 0;
        $bidder->save();

        return response()->json([
            'status' => true,
            'message' => 'Document re-uploaded successfully.',
        ], 200);
    }

    public function availableSlots(Request $request)
    {
        try {
            $request->validate([
                'room_type' => 'required|in:Physical,Virtual',
                'date' => 'required|date',
            ]);

            $roomType = $request->room_type;
            $date = $request->date;

            $roomIds = Room::where('room_type', $roomType)->pluck('id');

            if ($roomIds->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No rooms found for the specified room type.',
                ], 404);
            }

            $availableSlots = Slot::whereIn('room_id', $roomIds)
                ->where('date_for_reservation', $date)
                ->where('slot_status', 1)
                ->pluck('start_time')
                ->toArray();

            $startTime = Carbon::createFromTimeString('09:00:00');
            $endTime = Carbon::createFromTimeString('18:00:00');

            $timeBlocks = [];
            while ($startTime < $endTime) {
                $timeStr = $startTime->format('H:i:s');
                $timeBlocks[$timeStr] = in_array($timeStr, $availableSlots) ? 'available' : 'unavailable';
                $startTime->addMinutes(30);
            }

            return response()->json([
                'status' => true,
                'message' => 'Slot availability for the day',
                'slots' => $timeBlocks
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching slot availability: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching slot availability.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function availableLots(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'time' => 'required|date_format:H:i:s',
                'room_type' => 'required|in:Physical,Virtual',
            ]);

            $date = $request->date;
            $time = $request->time;
            $roomType = $request->room_type;

            $bookedLotIds = SlotBooking::where('start_time', $time)
                ->where('date_for_reservation', $date)
                ->where('room_type', $roomType)
                ->where('status', 1)
                ->pluck('lot_id')
                ->toArray();

            $availableLots = Lot::whereNotIn('id', $bookedLotIds)->get();

            if ($availableLots->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No available lots found.',
                    'available_lots' => []
                ], 200);
            }

            $availableLots = $availableLots->map(function ($lot) {
                if (is_array($lot->images)) {
                    $lot->images = array_map(function ($image) {
                        return 'storage/images/lots/' . ltrim($image, '/');
                    }, $lot->images);
                } else {
                    $lot->images = [];
                }
                unset($lot->image_urls);
                return $lot;
            });

            return response()->json([
                'status' => true,
                'message' => 'Available lots fetched successfully.',
                'available_lots' => $availableLots,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available lots: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while fetching available lots.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function slotBooking(Request $request)
    {
        try {
            $request->validate([
                'room_type' => 'required|string|in:Physical,Virtual',
                'time' => 'required|date_format:H:i:s',
                'date' => 'required|date',
                'lot_id' => 'required',
            ]);

            $lotIds = is_array($request->lot_id)
                ? $request->lot_id
                : explode(',', $request->lot_id);

            $lotIds = array_filter(array_map('trim', $lotIds));

            foreach ($lotIds as $lotId) {
                if (!is_numeric($lotId)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid lot ID.',
                    ], 400);
                }
            }

            $existingLotIds = Lot::whereIn('id', $lotIds)->pluck('id')->toArray();
            $invalidLotIds = array_diff($lotIds, $existingLotIds);

            if (!empty($invalidLotIds)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid lot IDs found.',
                    'invalid_lot_ids' => array_values($invalidLotIds),
                ], 400);
            }

            $bidder = Bidder::find($request->user()->id);
            if (!$bidder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found.',
                ], 404);
            }
            DB::beginTransaction();
            // Generate dynamic booking_id
            $latestBooking = SlotBooking::whereNotNull('booking_id')
                ->where('booking_id', 'like', 'DA-%')
                ->orderByDesc('id')
                ->first();

            if ($latestBooking && preg_match('/DA-(\d+)/', $latestBooking->booking_id, $matches)) {
                $nextNumber = str_pad(((int) $matches[1]) + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $bookingId = 'DA-' . $nextNumber;
            Booking::create([
                'booking_id' => $bookingId,
                'bidder_id' => $bidder->id,
                'room_name' => null,
                'room_type' => $request->room_type,
                'start_time' => $request->time,
                'date_for_reservation' => $request->date,
                'booking_lot_id' => $lotIds,
                'lot_booking_flag' => count($lotIds) >= 15 ? 1 : 0,
                'requested_lot_id' => null,
                'lot_requested_flag' => 0,
                'timer' => null,
                'timer_status' => 0,
            ]);
            $insertData = [];
            foreach ($lotIds as $lotId) {
                $insertData[] = [
                    'lot_id' => $lotId,
                    'booking_id' => $bookingId,
                    'start_time' => $request->time,
                    'date_for_reservation' => $request->date,
                    'bidder_id' => $bidder->id,
                    'bidder_name' => $bidder->full_name,
                    'room_type' => $request->room_type,
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            SlotBooking::insert($insertData);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Slot booked successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Slot booking error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error booking slots.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bidderAssignedLotsBySlot(Request $request, $slotId)
    {
        $bidderId = $request->user()->id;

        // Ensure the bidder has this slot assigned
        $hasAccess = SlotBooking::where('bidder_id', $bidderId)
            ->where('slot_id', $slotId)
            ->where('status', 1)
            ->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access or slot not approved.'
            ], 403);
        }

        // Get lot IDs for this slot
        $lotIds = SlotBooking::where('slot_id', $slotId)
            ->where('status', 1)
            ->pluck('lot_id');

        // Get actual lot records
        $lots = Lot::whereIn('id', $lotIds)->get();

        $lots = Lot::whereIn('id', $lotIds)->get()->map(function ($lot) {
            // Ensure images are decoded
            $images = is_array($lot->images) ? $lot->images : json_decode($lot->images, true);

            // Convert to full asset URLs
            $lot->images = collect($images)->map(function ($image) {
                return 'storage/images/lots/' . ltrim($image, '/');
                // return asset('storage/images/lots/' . $image);
            })->toArray();

            return $lot;
        });


        return response()->json([
            'status' => true,
            'message' => 'Bidder assigned lots fetched for slot.',
            'lots' => $lots
        ]);
    }

    public function getBidderSlots(Request $request)
    {
        try {
            $bidderId = $request->user()->id;
            $currDate = date('Y-m-d');
            $bidderExists = DB::table('bidders')->where('id', $bidderId)->exists();
            if (!$bidderExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found.'
                ], 404);
            }

            $bookings = Booking::where('bidder_id', $bidderId)
                ->where('date_for_reservation', $currDate)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Bidder slots fetched successfully.',
                'bookings' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching bidder slots.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBookingDetails(Request $request, $id)
    {
        $bidderId = $request->user()->id;

        try {
            $booking = Booking::where('booking_id', $id)
                ->where('bidder_id', $bidderId)
                ->whereNotNull('room_name')
                ->first();

            if (!$booking) {
                return response()->json(['status' => false, 'message' => 'Booking Not Found'], 404);
            }

            // Get lots
            $lotIds = $booking->booking_lot_id ?? [];
            $lots = Lot::whereIn('id', $lotIds)->get();

            // Pre-fetch all slot bookings for performance (avoid N+1)
            $slotBookings = SlotBooking::whereIn('lot_id', $lotIds)
                ->where('booking_id', $booking->booking_id)
                ->where('bidder_id', $bidderId)
                ->get()
                ->keyBy('lot_id'); // Map by lot_id

            $lotDetails = $lots->map(function ($lot) use ($slotBookings) {
                $images = is_array($lot->images) ? $lot->images : [];
                $imageUrls = array_map(fn($img) => asset('storage/images/lots/' . $img), $images);

                $biddingPrice = optional($slotBookings->get($lot->id))->bidding_price ?? null;

                return [
                    "id" => $lot->id,
                    "seller_id" => $lot->seller_id,
                    "category_id" => $lot->category_id,
                    "title" => $lot->title,
                    "description" => $lot->description,
                    "type" => $lot->type,
                    "color" => $lot->color,
                    "weight" => $lot->weight,
                    "size" => $lot->size,
                    "stone" => $lot->stone,
                    "shape" => $lot->shape,
                    "notes" => $lot->notes,
                    "batch_code" => $lot->batch_code,
                    "bidding_price" => $biddingPrice,
                    "status" => $lot->status,
                    "report_number" => $lot->report_number,
                    "colour_grade" => $lot->colour_grade,
                    "colour_origin" => $lot->colour_origin,
                    "colour_distribution" => $lot->colour_distribution,
                    "polish" => $lot->polish,
                    "symmetry" => $lot->symmetry,
                    "fluorescence" => $lot->fluorescence,
                    "images" => $imageUrls,
                    "video" => $lot->video,
                ];
            });

            $bookingDetails = [
                'id' => $booking->id,
                'booking_id' => $booking->booking_id,
                'bidder_id' => $booking->bidder_id,
                'room_name' => $booking->room_name,
                'room_type' => $booking->room_type,
                'start_time' => $booking->start_time,
                'date_for_reservation' => $booking->date_for_reservation,
                'lot_details' => $lotDetails,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Booking Details Fetched',
                'booking' => $bookingDetails,
            ]);
        } catch (\Throwable $th) {
            Log::error('Failed to Fetch Booking Details: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
            ], 500);
        }
    }


    public function updateBiddetails(Request $request)
    {
        $bidderId = $request->user()->id;
        $bookingId = $request->booking_id;
        $lotId = $request->lot_id;
        $price = $request->bidding_price;

        try {
            $bookingSlot = SlotBooking::where('booking_id', $bookingId)
                ->where('lot_id', $lotId)
                ->where('bidder_id', $bidderId)
                ->first();

            if (!$bookingSlot) {
                return response()->json(['status' => false, 'message' => 'Lot Details not found'], 404);
            }

            $bookingSlot->bidding_price = $price;
            $bookingSlot->save();
            return response()->json(['status' => true, 'message' => 'Price Updated']);
        } catch (\Throwable $th) {
            Log::error('Fails to update:' . $th->getMessage());
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }
    public function bidderLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
