<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{
    public function createSeller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',

            'full_name' => 'required|string',
            'email_address' => 'required|email|unique:sellers,email_address',
            'phone_number' => 'required|string',
            'country' => 'required|string',
            'password' => 'required|string|min:6',

            'company_name' => 'required_if:type,1|string',
            'registration_number' => 'required_if:type,1|string',
            'director_name' => 'required_if:type,1|string',
            'director_email' => 'required_if:type,1|email',
            'director_phone' => 'required_if:type,1|string',
            'certificate_of_incorporation' => 'required_if:type,1|file',
            'valid_trade_license' => 'required_if:type,1|file',
            'passport_copy_authorised' => 'required_if:type,1|file',
            'ubo_declaration' => 'required_if:type,1|file',

            'source_of_goods' => 'required_if:type,2|string',
            'passport_copy' => 'required_if:type,2|file',
            'proof_of_ownership' => 'required_if:type,2|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $seller = new Seller();

        // Common fields
        $seller->type = $request->type;
        $seller->full_name = $request->full_name;
        $seller->email_address = $request->email_address;
        $seller->phone_number = $request->phone_number;
        $seller->country = $request->country;
        $seller->password = Hash::make($request->password);
        $seller->kyc_status = 0;
        $seller->account_status = 0;

        $destinationPath = public_path('storage/document/seller/');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        // File handler
        $handleUpload = function ($field) use ($request, $destinationPath) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);
                return $filename;
            }
            return null;
        };

        // Company
        if ($request->type == 1) {
            $seller->company_name = $request->company_name;
            $seller->registration_number = $request->registration_number;
            $seller->director_name = $request->director_name;
            $seller->director_email = $request->director_email;
            $seller->director_phone = $request->director_phone;

            $seller->certificate_of_incorporation = $handleUpload('certificate_of_incorporation');
            $seller->valid_trade_license = $handleUpload('valid_trade_license');
            $seller->passport_copy_authorised = $handleUpload('passport_copy_authorised');
            $seller->ubo_declaration = $handleUpload('ubo_declaration');
        }

        // Individual
        if ($request->type == 2) {
            $seller->source_of_goods = $request->source_of_goods;

            $seller->passport_copy = $handleUpload('passport_copy');
            $seller->proof_of_ownership = $handleUpload('proof_of_ownership');
        }

        $seller->save();

        return response()->json([
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

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => $seller,
        ], 200);
    }
}
