<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidder;
use App\Models\Lot;
use App\Models\Room;
use App\Models\Slot;
use App\Models\SlotBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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


    // public function availableSlots()
    // {
    //     try {
    //         $today = Carbon::today()->toDateString();
    //         $now = Carbon::now();

    //         // Round up to the next 30-minute block
    //         $minute = $now->minute;
    //         $roundedTime = $now->copy()->minute($minute < 30 ? 30 : 0)->addMinutes($minute < 30 ? 0 : 30)->second(0)->format('H:i:s');

    //         $slots = Slot::where('date_for_reservation', $today)
    //             // ->where('start_time', '>=', $roundedTime)
    //             ->get();
    //         // $slots = Slot::all();

    //         // Return specific fields only
    //         $data = $slots->map(function ($slot) {
    //             return [
    //                 'slot_id' => $slot->id,
    //                 'room_id' => $slot->room_id,
    //                 'start_time' => $slot->start_time,
    //                 'end_time' => $slot->end_time,
    //                 'date' => $slot->date_for_reservation,
    //                 'slot_status' => $slot->slot_status,
    //             ];
    //         });

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Available slots from current time',
    //             'data' => $data
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching available slots: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong while fetching available slots.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

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
            $slotId = $request->slot_id;
            $slot = Slot::select('start_time', 'date_for_reservation')
                ->find($slotId);

            if (!$slot) {
                return response()->json([
                    'status' => false,
                    'message' => 'Slot not found.',
                ], 404);
            }

            // Fetch booked lot_ids for this time and date from SlotBooking table
            $bookedLotIds = SlotBooking::where('start_time', $slot->start_time)
                ->where('date_for_reservation', $slot->date_for_reservation)
                ->where('status', 1)
                ->pluck('lot_id')
                ->toArray();
            // Get available lots
            $availableLots = Lot::whereNotIn('id', $bookedLotIds)->get();

            $availableLots = $availableLots->map(function ($lot) {
                if (is_array($lot->images)) {
                    $lot->images = array_map(function ($image) {
                        // return asset('storage/images/lots/' . $image);
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
                'slot_id' => 'required|exists:slots,id',
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

            $slot = Slot::find($request->slot_id);
            if (!$slot) {
                return response()->json([
                    'status' => false,
                    'message' => 'Slot not found.',
                ], 404);
            }

            // $bidderId = $request->user()->id;
            $bidder = Bidder::find($request->user()->id);
            if (!$bidder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found.',
                ], 404);
            }

            $room = Room::find($slot->room_id);
            if (!$room) {
                return response()->json([
                    'status' => false,
                    'message' => 'Room not found for the slot.',
                ], 404);
            }

            $insertData = [];
            foreach ($lotIds as $lotId) {
                $insertData[] = [
                    'slot_id' => $slot->id,
                    'lot_id' => $lotId,
                    'start_time' => $slot->start_time,
                    'date_for_reservation' => $slot->date_for_reservation,
                    'bidder_id' => $bidder->id,
                    'bidder_name' => $bidder->full_name,
                    'room_name' => $room->room_name,
                    'room_type' => $room->room_type,
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert
            SlotBooking::insert($insertData);

            return response()->json([
                'status' => true,
                'message' => 'Slot booked successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Slot booking error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error booking slots.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBidderAssignedSlots(Request $request)
    {
        $bidderId = $request->user()->id;

        $slotIds = SlotBooking::where('bidder_id', $bidderId)
            ->where('status', 1) // approved
            ->pluck('slot_id')
            ->unique();

        $slots = Slot::whereIn('id', $slotIds)->get();

        return response()->json([
            'status' => true,
            'message' => 'Bidder assigned slots fetched.',
            'slots' => $slots
        ]);
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

    public function bidderLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
