<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidder;
use App\Models\BiddingPrice;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            DB::beginTransaction();
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

            // Send registration confirmation email
            $subject = "Thank You for Registering with Dexterous Tender";
            $messageText = '
            <html>
              <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
                <table align="center" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; padding: 20px; border: 1px solid #ddd;">
                  <tr>
                    <td style="text-align: center; padding-bottom: 20px;">
                      <img src="https://dexterousdmcc.gemxchange.com/assets/logo-Dhtvyvby.png" alt="Dexterous Tender" style="height: 100px;" />
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <h2 style="color: #333;">Thank you for registering with Dexterous Tender!</h2>
                      <p style="font-size: 16px; color: #555;">
                        Hi ' . htmlspecialchars($bidder->full_name) . ',
                      </p>
                      <p style="font-size: 16px; color: #555;">
                        Your registration has been received and your account is being reviewed by our team.
                      </p>
                      <p style="font-size: 16px; color: #555;">
                        Please sit tight while we verify your documents and approve your account. You will receive another email as soon as your account is activated.
                      </p>

                      <div style="background-color: #f9f9f9; padding: 15px; border: 1px solid #ccc; margin: 20px 0;">
                        <h3 style="color: #444;">Your Login Details</h3>
                        <p style="font-size: 15px;">
                          Username: <strong>' . htmlspecialchars($bidder->email_address) . '</strong>
                        </p>

                      </div>

                      <p style="font-size: 16px; color: #555;">
                        Your Dexterous Tender account is the simplest way to track your submissions, manage invoices, and participate in upcoming tenders.
                      </p>

                      <p style="font-size: 16px; color: #555;">
                        If you have any questions or require help, please call us on
                        <a href="tel:+4400000000" style="color: #007bff;">+44 xxx xxx xxxx</a> or email us at
                        <a href="mailto:support@dexteroustender.com" style="color: #007bff;">support@dexteroustender.com</a>.
                      </p>

                      <hr style="margin: 30px 0;" />

                      <h3 style="color: #333;">Buyer\'s Premium</h3>
                      <p style="font-size: 15px; color: #555;">
                        On the first £100,000 of the Hammer Price (of any individual lot), the buyer will pay the hammer price and a premium of
                        <strong>25% (plus VAT)</strong> or <strong>30% (inclusive of VAT)</strong>.<br />
                        On the excess over £100,001 of the hammer price (of any individual lot), the buyer will pay the hammer price and a premium of
                        <strong>15% (plus VAT)</strong> or <strong>18% (inclusive of VAT)</strong>.
                      </p>

                      <h3 style="color: #333;">Seller\'s Commission</h3>
                      <p style="font-size: 15px; color: #555;">
                        Our seller’s commission charge is <strong>15% (plus VAT)</strong>. A marketing fee is charged at <strong>£10 (plus VAT)</strong> per lot.<br />
                        There is also a loss/liability charge of <strong>1.5% (plus VAT)</strong> per lot.<br />
                        We offer free worldwide shipping subject to our T&Cs.
                      </p>

                      <p style="font-size: 14px; color: #999; margin-top: 30px;">--<br />Team Dexterous</p>
                    </td>
                  </tr>
                </table>

                <table align="center" width="600" style="font-size: 12px; color: #999; text-align: center; margin-top: 20px;">
                  <tr>
                    <td>
                      <p style="margin-bottom: 5px;">
                        © ' . date('Y') . ' Dexterous Tender. All rights reserved.
                      </p>
                      <p>
                        You are receiving this email at <a href="mailto:' . htmlspecialchars($bidder->email_address) . '" style="color: #999;">' . htmlspecialchars($bidder->email_address) . '</a>
                      </p>
                    </td>
                  </tr>
                </table>
              </body>
            </html>';

            Mail::html($messageText, function ($message) use ($bidder, $subject) {
                $message->to($bidder->email_address)
                    ->subject($subject);
            });

            // Notify internal team
            $internalSubject = "New Bidder Registration: {$bidder->full_name}";
            $internalMessage = "A new bidder has registered on Dexterous Tender.\n\n" .
                "Name: {$bidder->full_name}\n" .
                "Email: {$bidder->email_address}\n" .
                "Phone: {$bidder->phone_number}\n" .
                "Type: " . ($bidder->type == 1 ? 'Company' : 'Individual');

            Mail::raw($internalMessage, function ($message) use ($internalSubject) {
                $message->to([
                    'conner@dexterousdmcc.com',
                    'abdul@dexterousdmcc.com',
                    // 'diana@dextrousdmcc.com',
                    'sam.miah@bbndry.com',
                ])->subject($internalSubject);
            });

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Bidder created successfully',
                'data' => $bidder
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
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

        // if ($bidder->account_status !== 1) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Account is not active.'
        //     ], 403); // 403 Forbidden
        // }

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
            $typeStatus = [
                1 => 'Company',
                2 => 'Individual',
            ];

            $totalLotsListed = SlotBooking::where('bidder_id', $bidder->id)
                ->distinct('lot_id')
                ->count('lot_id');

            // $bidsReceived = SlotBooking::where('bidder_id', $bidder->id)
            //     ->whereNotNull('bidding_price')
            //     ->count();
            // $maxBidAmount = SlotBooking::where('bidder_id', $bidder->id)
            //     ->whereNotNull('bidding_price')
            //     ->max('bidding_price');
            $result = SlotBooking::where('bidder_id', $bidder->id)
                ->whereNotNull('bidding_price')
                ->selectRaw('COUNT(*) as total_bids, MAX(bidding_price) as max_bid')
                ->first();

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
                'type' => $bidder->type,
                'type_name' => $typeStatus[$bidder->type],
                'total_lots_listed' => $totalLotsListed,
                'bids_received' => $result->total_bids,
                'highest_bid_amount' => $result->max_bid,
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
            $bidderId = $request->user()->id;

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
                    // $date and $startTime
                    $booking = Booking::where('bidder_id', $bidderId)
                        ->where('start_time', $startTime)
                        ->where('date_for_reservation', $date)
                        ->first();
                    if ($booking) {
                        $timeBlocks[$timeStr] = 'Disabled';
                    } else {
                        $timeBlocks[$timeStr] = in_array($timeStr, $availableSlots) ? 'available' : 'unavailable';
                    }
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

            // $availableLots = Lot::whereNotIn('id', $bookedLotIds)->get();
            $availableLots = Lot::whereNotIn('id', $bookedLotIds)
                ->orderByRaw('CAST(weight AS DECIMAL(10,2)) DESC')
                ->get();

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
                    $lot->images = ['storage/images/lots/sample.jpg'];
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
            $latestBooking = Booking::whereNotNull('booking_id')
                ->orderByDesc('id')
                ->value('booking_id');

            if ($latestBooking && preg_match('/DA-(\d+)/', $latestBooking, $matches)) {
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
            $currentDate = now()->format('Y-m-d');
            // $currentTime = now()->format('H:i:s');
            $currentTime = now()->addMinutes(21)->format('H:i:s');

            $bidderExists = DB::table('bidders')->where('id', $bidderId)->exists();
            if (!$bidderExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found.'
                ], 404);
            }

            $bookings = Booking::where('bidder_id', $bidderId)
                ->where('date_for_reservation', '>=', $currentDate)
                // ->where('start_time', '<=', $currentTime)
                // ->orderBy('date_for_reservation')
                // ->orderBy('start_time')
                ->orderByDesc('booking_id')
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

            // Decode JSON array from DB
            $lotIds = is_array($booking->booking_lot_id)
                ? $booking->booking_lot_id
                : json_decode($booking->booking_lot_id, true) ?? [];

            $lots = Lot::whereIn('id', $lotIds)->get();

            $requestlotIds = is_array($booking->requested_lot_id)
                ? $booking->requested_lot_id
                : json_decode($booking->requested_lot_id, true) ?? [];


            // // Ensure booking_lot_id is an array
            // $lotIds = is_array($booking->booking_lot_id)
            //     ? $booking->booking_lot_id
            //     : explode(',', $booking->booking_lot_id ?? '');

            // $lots = Lot::whereIn('id', $lotIds)->get();

            // // Ensure requested_lot_id is an array
            // $requestlotIds = is_array($booking->requested_lot_id)
            //     ? $booking->requested_lot_id
            //     : explode(',', $booking->requested_lot_id ?? '');

            $requestedLotDetails = Lot::whereIn('id', $requestlotIds)->get()->map(function ($rlot) {
                $images = is_array($rlot->images) ? $rlot->images : [];
                $imageUrls = array_map(fn($img) => asset('storage/images/lots/' . $img), $images);

                return [
                    "id" => $rlot->id,
                    "seller_id" => $rlot->seller_id,
                    "category_id" => $rlot->category_id,
                    "title" => $rlot->title,
                    "description" => $rlot->description,
                    "type" => $rlot->type,
                    "color" => $rlot->color,
                    "weight" => $rlot->weight,
                    "size" => $rlot->size,
                    "stone" => $rlot->stone,
                    "shape" => $rlot->shape,
                    "notes" => $rlot->notes,
                    "batch_code" => $rlot->batch_code,
                    "bidding_price" => 0,
                    "status" => $rlot->status,
                    "report_number" => $rlot->report_number,
                    "colour_grade" => $rlot->colour_grade,
                    "colour_origin" => $rlot->colour_origin,
                    "colour_distribution" => $rlot->colour_distribution,
                    "polish" => $rlot->polish,
                    "symmetry" => $rlot->symmetry,
                    "fluorescence" => $rlot->fluorescence,
                    "images" => $imageUrls,
                    "video" => $rlot->video,
                ];
            });

            // Pre-fetch all slot bookings for performance
            $slotBookings = SlotBooking::whereIn('lot_id', $lotIds)
                ->where('booking_id', $booking->booking_id)
                ->where('bidder_id', $bidderId)
                ->where('status', 1)
                ->get()
                ->keyBy('lot_id');

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
                'requested_lots' => $requestedLotDetails
            ];

            if ($booking->room_type === 'Virtual') {
                $meetingLink = SlotBooking::where('booking_id', $booking->booking_id)
                    ->where('status', 1)
                    ->latest('id')
                    ->first();
                $bookingDetails['meeting_link'] = $meetingLink->meeting_link ?? '';
            }

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
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'lot_id' => 'required|integer',
            'bidding_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bidding = BiddingPrice::create([
                'booking_id' => $request->booking_id,
                'lot_id' => $request->lot_id,
                'price' => $request->bidding_price,
                'bidding_time' => Carbon::now(),
            ]);

            DB::table('slot_bookings')
                ->where('booking_id', $request->booking_id)
                ->where('lot_id', $request->lot_id)
                ->update(['bidding_price' => $request->bidding_price]);

            return response()->json([
                'status' => true,
                'message' => 'Bidding price updated successfully',
                'data' => $bidding
            ]);
        } catch (\Throwable $th) {
            Log::error('Failed to insert bidding price: ' . $th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $th->getMessage()
            ], 500);
        }
    }

    public function getBiddingHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'lot_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $biddingHistory = BiddingPrice::where('booking_id', $request->booking_id)
                ->where('lot_id', $request->lot_id)
                ->orderBy('bidding_time', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Bidding history fetched successfully',
                'data' => $biddingHistory
            ]);
        } catch (\Throwable $th) {
            Log::error('Failed to fetch bidding history: ' . $th->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $th->getMessage()
            ], 500);
        }
    }

    public function getBiddingSummary(Request $request)
    {
        $bidderId = $request->user()->id;
        $biddingSummary = SlotBooking::where('bidder_id', $bidderId)
            ->whereNotNull('bidding_price')
            ->where('status', 1)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Bidding summary fetched',
            'summary' => $biddingSummary
        ]);
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
        $meetingLink = $request->meeting_link;

        DB::beginTransaction();
        try {
            // Update booking record
            $booking = Booking::find($bookingId);
            if (!$booking) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Booking not found']);
            }

            // Decode existing requested lot IDs (if cast in model, this is auto-array)
            $currentLots = is_array($booking->requested_lot_id)
                ? $booking->requested_lot_id
                : json_decode($booking->requested_lot_id, true);

            $currentLots = is_array($currentLots) ? $currentLots : [];

            // Merge existing with new and remove duplicates
            $updatedLots = array_values(array_unique(array_merge($currentLots, $lotIds)));

            // Update booking with merged requested_lot_id
            $booking->update([
                'requested_lot_id' => $updatedLots,
                'lot_requested_flag' => count($updatedLots) >= 6 ? 1 : 0,
            ]);

            // Fetch already inserted slot lots for this booking to avoid duplicates
            $existingLotIds = SlotBooking::where('booking_id', $bookingNumber)
                ->pluck('lot_id')
                ->toArray();

            // Only insert new lot IDs
            $newLotIds = array_diff($lotIds, $existingLotIds);

            $insertData = [];
            foreach ($newLotIds as $lotId) {
                $insertData[] = [
                    'lot_id' => $lotId,
                    'booking_id' => $bookingNumber,
                    'start_time' => $request->time,
                    'date_for_reservation' => $request->date,
                    'bidder_id' => $bidder->id,
                    'bidder_name' => $bidder->full_name,
                    'room_type' => $request->room_type,
                    'room_name' => $request->room_name,
                    'status' => 3, // 3 = Requested
                    'meeting_link' => $meetingLink,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insertData)) {
                SlotBooking::insert($insertData);
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Request Created Successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }


    public function forgotPassword(Request $request)
    {
        $email = $request->email;

        if (!$email) {
            return response()->json(['status' => false, 'message' => 'The email is required']);
        }

        // Check if seller exists
        $sellerExists = DB::table('bidders')
            ->where('email_address', $email)
            ->exists();

        if (!$sellerExists) {
            return response()->json(['status' => false, 'message' => 'Email not found'], 404);
        }

        $token = Str::random(64);

        try {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Build reset URL (optional)
            $resetUrl = config('app.site_url') . "/bidder/reset-password?token=$token&email=$email";

            // Send email
            Mail::raw("Click here to reset your password: $resetUrl", function ($message) use ($email) {
                $message->to($email)
                    ->subject('Reset Your Bidder Account Password');
            });

            return response()->json(['status' => true, 'message' => 'Reset token sent to your email.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function resetPassword(Request $request)
    {
        if (!$request->input('email')) {
            return response()->json(['status' => false, 'message' => 'The email is required']);
        }

        if (!$request->input('token')) {
            return response()->json(['status' => false, 'message' => 'The token is required']);
        }

        if (!$request->input('password')) {
            return response()->json(['status' => false, 'message' => 'The password is required']);
        }

        if (strlen($request->input('password')) < 6) {
            return response()->json(['status' => false, 'message' => 'The password must be at least 6 characters']);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['status' => false, 'message' => 'Invalid reset request.'], 404);
        }

        // Check token expiry
        $tokenLifetime = 60; // minutes
        if (Carbon::parse($record->created_at)->addMinutes($tokenLifetime)->isPast()) {
            Log::warning('Password reset token expired', [
                'email' => $request->email,
                'expired_at' => $record->created_at,
                'current_time' => now(),
            ]);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json(['status' => false, 'message' => 'Reset token has expired.'], 403);
        }

        // Token invalid
        if (!Hash::check($request->token, $record->token)) {
            Log::notice('Invalid password reset token attempt', [
                'email' => $request->email,
                'attempted_token' => $request->token,
                'created_at' => $record->created_at,
            ]);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json(['status' => false, 'message' => 'Invalid token.'], 403);
        }

        // Update password
        Bidder::where('email_address', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Cleanup
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['status' => true, 'message' => 'Password reset successful.'], 200);
    }

    public function bidderChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bidder = $request->user();

        if (!Hash::check($request->current_password, $bidder->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 401);
        }

        if (Hash::check($request->new_password, $bidder->password)) {
            return response()->json([
                'status' => false,
                'message' => 'New password must be different from the current password.',
            ], 422);
        }

        $bidder->password = Hash::make($request->new_password);
        $bidder->save();

        // Revoke old tokens
        $bidder->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully. Please log in again.',
        ], 200);
    }

    public function bidderLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }


    private function getEnvData()
    {
        $envLines = file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $envVars = [];
        foreach ($envLines as $line) {
            // Ignore comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse key=value
            [$key, $value] = array_pad(explode('=', $line, 2), 2, null);
            $envVars[trim($key)] = trim($value);
        }
        return $envVars;
        // Now you can access the value like this:
        // dd($envVars['ZOOM_CLIENT_ID'] ?? 'Not found');
    }
}
