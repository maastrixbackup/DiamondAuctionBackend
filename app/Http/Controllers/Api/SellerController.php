<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{
    public function createSeller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'type' => 'required|in:company,individual',
            'name' => 'required|string',
            'email' => 'required|email|unique:sellers,email_address',
            'phone' => 'required|string',
            'country' => 'required|string',
            'password' => 'required|string|min:6',

            'company_name' => 'required_if:type,company|string',
            'registration_number' => 'required_if:type,company|string',
            'director_name' => 'required_if:type,company|string',
            'director_email' => 'required_if:type,company|email',
            'director_phone' => 'required_if:type,company|string',
            'certificate_of_incorporation' => 'required_if:type,company|file',
            'valid_trade_license' => 'required_if:type,company|file',
            'passport_copy_authorised' => 'required_if:type,company|file',
            'ubo_declaration' => 'required_if:type,company|file',

            'source_of_goods' => 'required_if:type,individual|string',
            'passport_copy' => 'required_if:type,individual|file',
            'proof_of_ownership' => 'required_if:type,individual|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seller = new Seller();

        // Convert type to int for DB
        $seller->type = $request->type === 'company' ? 1 : 2;

        $seller->full_name = $request->name;
        $seller->email_address = $request->email;
        $seller->phone_number = $request->phone;
        $seller->country = $request->country;
        $seller->password = Hash::make($request->password);
        $seller->kyc_status = 0;
        $seller->account_status = 0;

        $destinationPath = public_path('storage/document/seller/');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // File upload handler
        $handleUpload = function ($field) use ($request, $destinationPath) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                return $filename;
            }
            return null;
        };

        // Company logic
        if ($request->type === 'company') {
            $seller->company_name = $request->company_name;
            $seller->registration_number = $request->registration_number;
            $seller->director_name = $request->director_name;
            $seller->director_email = $request->director_email;
            $seller->director_phone = $request->director_phone;

            $seller->certificate_of_incorporation = $handleUpload('certificate_of_incorporation');
            $seller->certificate_of_incorporation_status = 0;

            $seller->valid_trade_license = $handleUpload('valid_trade_license');
            $seller->valid_trade_license_status = 0;

            $seller->passport_copy_authorised = $handleUpload('passport_copy_authorised');
            $seller->passport_copy_authorised_status = 0;

            $seller->ubo_declaration = $handleUpload('ubo_declaration');
            $seller->ubo_declaration_status = 0;
        }

        // Individual logic
        if ($request->type === 'individual') {
            $seller->source_of_goods = $request->source_of_goods;

            $seller->passport_copy = $handleUpload('passport_copy');
            $seller->passport_copy_status = 0;

            $seller->proof_of_ownership = $handleUpload('proof_of_ownership');
            $seller->proof_of_ownership_status = 0;
        }

        $seller->save();

        return response()->json([
            'status' => true,
            'message' => 'Seller created successfully',
            'data' => $seller
        ], 201);
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
        $dashboardData = [
            'full_name' => $seller->full_name,
            'email_address' => $seller->email_address,
        ];
        return response()->json([
            'status' => true,
            'message' => 'Seller dashboard data fetched successfully.',
            'data' => $dashboardData,
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

    public function sellerLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
