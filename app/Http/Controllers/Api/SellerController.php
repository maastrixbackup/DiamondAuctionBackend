<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Seller;
use App\Models\SlotBooking;
use App\Notifications\SellerPasswordResetNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class SellerController extends Controller
{
    public function createSeller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:company,individual',

            //Company
            'companyName' => 'required_if:type,company|string',
            'regNumber' => 'required_if:type,company|string',
            'directorName' => 'required_if:type,company|string',
            'directorEmail' => 'required_if:type,company|email',
            'directorPhone' => 'required_if:type,company|string',
            'companyfullName' => 'required_if:type,company|string',
            'companyEmail' => 'required_if:type,company|email|unique:sellers,email_address',
            'companyPhone' => 'required_if:type,company|string',
            'companyCountry' => 'required_if:type,company|string',
            'companyPassword' => 'required_if:type,company|string|min:6',

            'incorporation' => 'required_if:type,company|file',
            'trade_license' => 'required_if:type,company|file',
            'passport_signatory' => 'required_if:type,company|file',
            'ubo_declaration' => 'required_if:type,company|file',

            //Individual
            'name' => 'required_if:type,individual|string',
            'email' => 'required_if:type,individual|email|unique:sellers,email_address',
            'phone' => 'required_if:type,individual|string',
            'country' => 'required_if:type,individual|string',
            'password' => 'required_if:type,individual|string|min:6',

            'passport_ind' => 'required_if:type,individual|file',
            'ownership_proof' => 'required_if:type,individual|file',
            'source_goods' => 'required_if:type,individual|string',

            //Kyc document for both
            'kyc_document' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $seller = new Seller();
            $seller->type = $request->type === 'company' ? 1 : 2;   // 1 = company, 2 = individual
            $seller->kyc_status = 0;
            $seller->account_status = 0;

            $destinationPath = public_path('storage/document/seller/');
            if (! is_dir($destinationPath)) {
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
                $seller->full_name = $request->companyfullName;
                $seller->email_address = $request->companyEmail;
                $seller->phone_number = $request->companyPhone;
                $seller->country = $request->companyCountry;
                $seller->password = Hash::make($request->companyPassword);

                $seller->company_name = $request->companyName;
                $seller->registration_number = $request->regNumber;
                $seller->director_name = $request->directorName;
                $seller->director_email = $request->directorEmail;
                $seller->director_phone = $request->directorPhone;

                $seller->certificate_of_incorporation = $handleUpload('incorporation');
                $seller->certificate_of_incorporation_status = 0;

                $seller->valid_trade_license = $handleUpload('trade_license');
                $seller->valid_trade_license_status = 0;

                $seller->passport_copy_authorised = $handleUpload('passport_signatory');
                $seller->passport_copy_authorised_status = 0;

                $seller->ubo_declaration = $handleUpload('ubo_declaration');
                $seller->ubo_declaration_status = 0;

                $seller->kyc_document = $handleUpload('kyc_document');
                $seller->kyc_document_status = 0;
            } else {
                //Individual
                $seller->full_name = $request->name;
                $seller->email_address = $request->email;
                $seller->phone_number = $request->phone;
                $seller->country = $request->country;
                $seller->password = Hash::make($request->password);

                $seller->source_of_goods = $request->source_goods;

                $seller->passport_copy = $handleUpload('passport_ind');
                $seller->passport_copy_status = 0;

                $seller->proof_of_ownership = $handleUpload('ownership_proof');
                $seller->proof_of_ownership_status = 0;

                $seller->kyc_document = $handleUpload('kyc_document');
                $seller->kyc_document_status = 0;
            }

            $seller->save();

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
                                    Hi ' . htmlspecialchars($seller->full_name) . ',
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
                                      Username: <strong>' . htmlspecialchars($seller->email_address) . '</strong>
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
                                    You are receiving this email at <a href="mailto:' . htmlspecialchars($seller->email_address) . '" style="color: #999;">' . htmlspecialchars($seller->email_address) . '</a>
                                  </p>
                                </td>
                              </tr>
                            </table>
                          </body>
                        </html>';

            Mail::html($messageText, function ($message) use ($seller, $subject) {
                $message->to($seller->email_address)
                    ->subject($subject);
            });

            // Notify internal team
            $internalSubject = "New Seller Registration: {$seller->full_name}";
            $internalMessage = "A new seller has registered on Dexterous Tender.\n\n" .
                "Name: {$seller->full_name}\n" .
                "Email: {$seller->email_address}\n" .
                "Phone: {$seller->phone_number}\n" .
                "Type: " . ($seller->type == 1 ? 'Company' : 'Individual');

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
                'message' => 'Seller created successfully',
                'data' => $seller
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create seller',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function sellerLogin(Request $request)
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

        $seller = Seller::where('email_address', $request->email_address)->first();
        if (!$seller || !Hash::check($request->password, $seller->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        // if ($seller->account_status !== 1) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Account is not active.'
        //     ], 403); // 403 Forbidden
        // }

        $token = $seller->createToken('seller_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $seller,
        ], 200);
    }

    public function sellerDashboard(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:sellers,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $seller = Seller::find($request->id);

            if (!$seller) {
                return response()->json([
                    'status' => false,
                    'message' => 'Seller not found.',
                ], 404);
            }

            $docBase = asset('storage/document/seller');

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

            $total_lots = Lot::where('seller_id', $seller->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $lotIds = Lot::where('seller_id', $seller->id)->pluck('id');
            $totalBidsReceived = SlotBooking::whereIn('lot_id', $lotIds)
                ->whereNotNull('bidding_price')
                ->count();

            $dashboardData = [
                'full_name' => $seller->full_name,
                'email_address' => $seller->email_address,
                'certificate_of_incorporation' => $docUrl($seller->certificate_of_incorporation),
                'certificate_of_incorporation_status' => $documentStatus[$seller->certificate_of_incorporation_status],
                'valid_trade_license' => $docUrl($seller->valid_trade_license),
                'valid_trade_license_status' => $documentStatus[$seller->valid_trade_license_status],
                'passport_copy_authorised' => $docUrl($seller->passport_copy_authorised),
                'passport_copy_authorised_status' => $documentStatus[$seller->passport_copy_authorised_status],
                'ubo_declaration' => $docUrl($seller->ubo_declaration),
                'ubo_declaration_status' => $documentStatus[$seller->ubo_declaration_status],
                'passport_copy' => $docUrl($seller->passport_copy),
                'passport_copy_status' => $documentStatus[$seller->passport_copy_status],
                'proof_of_ownership' => $docUrl($seller->proof_of_ownership),
                'proof_of_ownership_status' => $documentStatus[$seller->proof_of_ownership_status],
                'kyc_document' => $docUrl($seller->kyc_document),
                'kyc_document_status' => $documentStatus[$seller->kyc_document_status],
                'kyc_status' => $kycStatus[$seller->kyc_status],
                'account_status' => $accountStatus[$seller->account_status],
                'type' => $seller->type,
                'type_name' => $typeStatus[$seller->type],
                'total_lots_listed' => [
                    'pending' => $total_lots[0] ?? 0,
                    'live' => $total_lots[1] ?? 0,
                    'sold' => $total_lots[2] ?? 0,
                ],
                'bids_received' => $totalBidsReceived,

            ];

            return response()->json([
                'status' => true,
                'message' => 'Seller dashboard data fetched successfully.',
                'data' => $dashboardData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching seller dashboard: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the seller dashboard.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reuploadSellerDocument(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'field' => 'required|string|in:certificate_of_incorporation,valid_trade_license,passport_copy_authorised,ubo_declaration,passport_copy,proof_of_ownership,kyc_document',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $seller = Seller::findOrFail($request->seller_id);
        $field = $request->field;
        $statusField = $field . '_status';

        if ($seller->$statusField != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Only rejected documents can be re-uploaded.',
            ], 403);
        }

        $destinationPath = public_path('storage/document/seller/');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        if ($seller->$field && file_exists($destinationPath . $seller->$field)) {
            unlink($destinationPath . $seller->$field);
        }

        $file = $request->file('document');
        $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $filename);

        $seller->$field = $filename;
        $seller->$statusField = 0;
        $seller->kyc_status = 0;
        $seller->save();

        return response()->json([
            'status' => true,
            'message' => 'Document re-uploaded successfully.',
        ], 200);
    }


    public function viewSellerLots(Request $request)
    {
        $seller = $request->user(); //logged in seller id

        $lots = Lot::where('seller_id', $seller->id)
            ->with('category')
            ->latest()
            ->get()
            ->map(function ($lot) {
                // image
                $lot->images = collect($lot->images, true)
                    // ->map(fn($img) => url('storage/images/lots/' . $img));
                    ->map(fn($img) => 'storage/images/lots/' . ltrim($img, '/'));

                // Category name
                $lot->category_name = optional($lot->category)->name;

                return $lot;
            });

        return response()->json([
            'status' => true,
            'message' => 'Seller lots listing fetched successfully',
            'data' => $lots
        ], 200);
    }

    // public function viewSellerLots(Request $request)
    // {
    //     $seller = $request->user(); // logged in seller

    //     $lots = Lot::where('seller_id', $seller->id)
    //         ->with('category')
    //         ->latest()
    //         ->paginate(10);

    //     // Transform paginated items
    //     $lots->getCollection()->transform(function ($lot) {
    //         $lot->images = collect($lot->images ?: [])
    //             ->map(fn($img) => 'storage/images/lots/' . ltrim($img, '/'));

    //         $lot->category_name = optional($lot->category)->name;
    //         return $lot;
    //     });

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Seller lots listing fetched successfully',
    //         'data' => $lots
    //     ], 200);
    // }

    public function sellerLotDetails(Request $request, $id)
    {
        $seller = $request->user();
        $lot = Lot::where('id', $id)
            ->where('seller_id', $seller->id)
            ->with('category')
            ->first();
        if (!$lot) {
            return response()->json([
                'status' => false,
                'message' => 'Lot not found or unauthorized access',
            ], 404);
        }

        $lot->images = collect($lot->images)
            // ->map(fn($img) => url('storage/images/lots/' . $img));
            ->map(fn($img) => 'storage/images/lots/' . ltrim($img, '/'));

        $lot->category_name = optional($lot->category)->name;

        return response()->json([
            'status' => true,
            'message' => 'Seller lot details fetched successfully',
            'data' => $lot,
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $email = $request->email;

        if (!$email) {
            return response()->json(['status' => false, 'message' => 'The email is required']);
        }

        // Check if seller exists
        $sellerExists = DB::table('sellers')
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
            $resetUrl = config('app.site_url') . "/seller/reset-password?token=$token&email=$email";

            // Send email
            Mail::raw("Click here to reset your password: $resetUrl", function ($message) use ($email) {
                $message->to($email)
                    ->subject('Reset Your Seller Account Password');
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
        Seller::where('email_address', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Cleanup
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['status' => true, 'message' => 'Password reset successful.'], 200);
    }


    public function getLotsBidDetails(Request $request)
    {
        $sellerId = $request->user()->id;
        $lots = Lot::where('seller_id', $sellerId)->get();

        if ($lots->count() > 0) {
            $lots = $lots->map(function ($lot) {
                return [
                    'id' => $lot->id,
                    'lot_name' => $lot->title,
                    'total_bids' => SlotBooking::where('lot_id', $lot->id)->whereNotNull('bidding_price')->count(),
                    'highest_bid' => SlotBooking::where('lot_id', $lot->id)
                        ->whereNotNull('bidding_price')
                        ->max('bidding_price') ?? 0,
                    'status' => $lot->status,
                ];
            });
            return response()->json(['status' => true, 'message' => 'Data Fetched', 'data' => $lots]);
        } else {
            return response()->json(['status' => false, 'message' => 'Lots Details Not Found']);
        }
    }

    public function sellerChangePassword(Request $request)
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

        $seller = $request->user();

        if (!Hash::check($request->current_password, $seller->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 401);
        }

        if (Hash::check($request->new_password, $seller->password)) {
            return response()->json([
                'status' => false,
                'message' => 'New password must be different from the current password.',
            ], 422);
        }

        $seller->password = Hash::make($request->new_password);
        $seller->save();

        // Revoke old tokens
        $seller->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully. Please log in again.',
        ], 200);
    }

    public function sellerLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
