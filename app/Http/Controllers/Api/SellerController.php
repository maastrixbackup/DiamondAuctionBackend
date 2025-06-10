<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
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
            }

            $seller->save();

            return response()->json([
                'status'  => true,
                'message' => 'Seller created successfully',
                'data'    => $seller
            ], 201);
        } catch (\Exception $e) {
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
                'kyc_status' => $kycStatus[$seller->kyc_status],
                'account_status' => $accountStatus[$seller->account_status],

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
            'field' => 'required|string|in:certificate_of_incorporation,valid_trade_license,passport_copy_authorised,ubo_declaration,passport_copy,proof_of_ownership',
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

    // public function forgotPassword(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:sellers,email_address',
    //     ]);

    //     $token = Str::random(64);
    //     $email = $request->email;

    //     DB::table('password_resets')->updateOrInsert(
    //         ['email' => $email],
    //         [
    //             'token' => Hash::make($token),
    //             'created_at' => now()
    //         ]
    //     );

    //     // Send email
    //     Mail::raw("Use this token to reset your password: $token", function ($message) use ($email) {
    //         $message->to($email)
    //             ->subject('Seller Password Reset');
    //     });

    //     return response()->json(['message' => 'Reset token sent to your email.'], 200);
    // }

    public function sellerLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
