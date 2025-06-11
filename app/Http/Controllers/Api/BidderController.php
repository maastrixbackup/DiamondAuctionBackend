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
            'type' => 'required|in:company,individual',

            // Company
            'companyName' => 'required_if:type,company|string',
            'regNumber' => 'required_if:type,company|string',
            'directorName' => 'required_if:type,company|string',
            'directorEmail' => 'required_if:type,company|email',
            'directorPhone' => 'required_if:type,company|string',
            'companyfullName' => 'required_if:type,company|string',
            'companyEmail' => 'required_if:type,company|email|unique:bidders,email_address',
            'companyPhone' => 'required_if:type,company|string',
            'companyCountry' => 'required_if:type,company|string',
            'companyPassword' => 'required_if:type,company|string|min:6',

            'incorporation' => 'required_if:type,company|file',
            'trade_license' => 'required_if:type,company|file',
            'passport_signatory' => 'required_if:type,company|file',
            'ubo_declaration' => 'required_if:type,company|file',

            // Individual
            'name' => 'required_if:type,individual|string',
            'email' => 'required_if:type,individual|email|unique:bidders,email_address',
            'phone' => 'required_if:type,individual|string',
            'country' => 'required_if:type,individual|string',
            'password' => 'required_if:type,individual|string|min:6',

            'passport_ind' => 'required_if:type,individual|file',
            'ownership_proof' => 'required_if:type,individual|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $bidder = new Bidder();
            $bidder->type = $request->type === 'company' ? 1 : 2;
            $bidder->kyc_status = 0;
            $bidder->account_status = 0;

            $destinationPath = public_path('storage/document/bidder/');
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $handleUpload = function (string $field) use ($request, $destinationPath) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $filename);
                    return $filename;
                }
                return null;
            };

            if ($request->type === 'company') {
                $bidder->full_name = $request->companyfullName;
                $bidder->email_address = $request->companyEmail;
                $bidder->phone_number = $request->companyPhone;
                $bidder->country = $request->companyCountry;
                $bidder->password = Hash::make($request->companyPassword);

                $bidder->company_name = $request->companyName;
                $bidder->registration_number = $request->regNumber;
                $bidder->director_name = $request->directorName;
                $bidder->director_email = $request->directorEmail;
                $bidder->director_phone = $request->directorPhone;

                $bidder->certificate_of_incorporation = $handleUpload('incorporation');
                $bidder->certificate_of_incorporation_status = 0;

                $bidder->valid_trade_license = $handleUpload('trade_license');
                $bidder->valid_trade_license_status = 0;

                $bidder->passport_copy_authorised = $handleUpload('passport_signatory');
                $bidder->passport_copy_authorised_status = 0;

                $bidder->ubo_declaration = $handleUpload('ubo_declaration');
                $bidder->ubo_declaration_status = 0;
            } else {
                $bidder->full_name = $request->name;
                $bidder->email_address = $request->email;
                $bidder->phone_number = $request->phone;
                $bidder->country = $request->country;
                $bidder->password = Hash::make($request->password);

                $bidder->passport_copy = $handleUpload('passport_ind');
                $bidder->passport_copy_status = 0;

                $bidder->proof_of_address = $handleUpload('ownership_proof');
                $bidder->proof_of_address_status = 0;
            }

            $bidder->save();

            return response()->json([
                'status' => true,
                'message' => 'Bidder created successfully',
                'data' => $bidder
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create bidder',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
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

            if (!$bidder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found.',
                ], 404);
            }

            $docBase = asset('storage/document/bidder');

            $docUrl = fn(string|null $file) => $file ? "{$docBase}/{$file}" : null;

            $documentStatus = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];

            $kycStatus = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];

            $accountStatus = [
                0 => 'Pending',
                1 => 'Active',
                2 => 'Suspended',
            ];

            $dashboardData = [
                'full_name' => $bidder->full_name,
                'email_address' => $bidder->email_address,
                'certificate_of_incorporation' => $docUrl($bidder->certificate_of_incorporation),
                'certificate_of_incorporation_status' => $documentStatus[$bidder->certificate_of_incorporation_status],
                'valid_trade_license' => $docUrl($bidder->valid_trade_license),
                'valid_trade_license_status' => $documentStatus[$bidder->valid_trade_license_status],
                'passport_copy_authorised' => $docUrl($bidder->passport_copy_authorised),
                'passport_copy_authorised_status' => $documentStatus[$bidder->passport_copy_authorised_status],
                'ubo_declaration' => $docUrl($bidder->ubo_declaration),
                'ubo_declaration_status' => $documentStatus[$bidder->ubo_declaration_status],
                'passport_copy' => $docUrl($bidder->passport_copy),
                'passport_copy_status' => $documentStatus[$bidder->passport_copy_status],
                'proof_of_address' => $docUrl($bidder->proof_of_address),
                'proof_of_address_status' => $documentStatus[$bidder->proof_of_address_status],
                'kyc_status' => $kycStatus[$bidder->kyc_status],
                'account_status' => $accountStatus[$bidder->account_status],
            ];

            return response()->json([
                'status' => true,
                'message' => 'Bidder dashboard data fetched successfully.',
                'data' => $dashboardData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching bidder dashboard: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the dashboard.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reuploadBidderDocument(Request $request)
    {
        $request->validate([
            'bidder_id' => 'required|exists:bidders,id',
            'field' => 'required|string|in:certificate_of_incorporation,valid_trade_license,passport_copy_authorised,ubo_declaration,passport_copy,proof_of_ownership',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
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
        } catch (\Exception $e) {
            Log::error('Reupload document error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while re-uploading the document.',
                'error' => $e->getMessage(),
            ], 500);
        }
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

            $now = Carbon::now();
            $queryDate = Carbon::parse($date)->toDateString();

            $timeBlocks = [];
            while ($startTime < $endTime) {
                $timeStr = $startTime->format('H:i:s');
                $slotDateTime = Carbon::parse($date . ' ' . $timeStr);

                if ($queryDate === $now->toDateString() && $slotDateTime->lessThan($now)) {
                    $timeBlocks[$timeStr] = 'Disabled';
                } else {
                    $timeBlocks[$timeStr] = in_array($timeStr, $availableSlots) ? 'available' : 'unavailable';
                }
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

            if ($bidder->kyc_status != 1 || $bidder->account_status != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not allowed. Your KYC or account is not approved.',
                ], 403);
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
            $currentDateTime = now();

            $bidderExists = DB::table('bidders')->where('id', $bidderId)->exists();
            if (!$bidderExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found.'
                ], 404);
            }

            $bookings = Booking::where('bidder_id', $bidderId)
                ->whereRaw("STR_TO_DATE(CONCAT(date_for_reservation, ' ', start_time), '%Y-%m-%d %H:%i:%s') >= ?", [$currentDateTime])
                ->orderBy('date_for_reservation')
                ->orderBy('start_time')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Upcoming bidder slots fetched successfully.',
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
                ->where('status', 1)
                ->get()
                ->keyBy('lot_id'); // Map by lot_id

            $lotDetails = $lots->map(function ($lot) use ($slotBookings) {
                $images = is_array($lot->images) ? $lot->images : [];
                $imageUrls = array_map(fn($img) => asset('storage/images/lots/' . $img), $images);

                $biddingPrice = optional($slotBookings->get($lot->id))->bidding_price ?? 0;

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

    public function requestedLots(Request $request)
    {
        $bidderId = $request->user()->id;
        $bidder = Bidder::find($bidderId);
        $lotIds = is_array($request->requested_lot_ids)
            ? $request->requested_lot_ids
            : explode(',', $request->requested_lot_ids);

        $lotIds = array_filter(array_map('trim', $lotIds));
        $bookingId = $request->booking_id;
        $bookingNumber = $request->booking_no;

        DB::beginTransaction();
        try {
            // Update booking record
            $booking = Booking::find($bookingId);
            if (!$booking) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Booking not found']);
            }

            $booking->update([
                'requested_lot_id' => $lotIds,
                'lot_requested_flag' => count($lotIds) >= 6 ? 1 : 0,
            ]);



            $insertData = [];
            foreach ($lotIds as $lotId) {
                $insertData[] = [
                    'lot_id' => $lotId,
                    'booking_id' => $bookingNumber,
                    'start_time' => $request->time,
                    'date_for_reservation' => $request->date,
                    'bidder_id' => $bidder->id,
                    'bidder_name' => $bidder->full_name,
                    'room_type' => $request->room_type,
                    'room_name' => $request->room_name,
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            SlotBooking::insert($insertData);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Request Created Successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
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
