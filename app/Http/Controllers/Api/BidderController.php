<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class BidderController extends Controller
{
    public function createBidder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',

            'full_name' => 'required|string',
            'email_address' => 'required|email|unique:bidders,email_address',
            'phone_number' => 'required|string',
            'country' => 'required|string',
            'password' => 'required|string|min:6',

            // Company fields
            'company_name' => 'required_if:type,1|string',
            'registration_number' => 'required_if:type,1|string',
            'director_name' => 'required_if:type,1|string',
            'director_email' => 'required_if:type,1|email',
            'director_phone' => 'required_if:type,1|string',
            'certificate_of_incorporation' => 'required_if:type,1|file',
            'valid_trade_license' => 'required_if:type,1|file',
            'passport_copy_authorised' => 'required_if:type,1|file',
            'ubo_declaration' => 'required_if:type,1|file',

            // Individual fields
            'passport_copy' => 'required_if:type,2|file',
            'proof_of_address' => 'required_if:type,2|file',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bidder = new Bidder();

        // Common fields
        $bidder->type = $request->type;
        $bidder->full_name = $request->full_name;
        $bidder->email_address = $request->email_address;
        $bidder->phone_number = $request->phone_number;
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

        // Company-specific
        if ($request->type == 1) {
            $bidder->company_name = $request->company_name;
            $bidder->registration_number = $request->registration_number;
            $bidder->director_name = $request->director_name;
            $bidder->director_email = $request->director_email;
            $bidder->director_phone = $request->director_phone;

            $bidder->certificate_of_incorporation = $handleUpload('certificate_of_incorporation');
            $bidder->valid_trade_license = $handleUpload('valid_trade_license');
            $bidder->passport_copy_authorised = $handleUpload('passport_copy_authorised');
            $bidder->ubo_declaration = $handleUpload('ubo_declaration');
        }

        // Individual-specific
        if ($request->type == 2) {
            $bidder->passport_copy = $handleUpload('passport_copy');
            $bidder->proof_of_address = $handleUpload('proof_of_address');
        }

        $bidder->save();

        return response()->json([
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

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => $bidder,
        ], 200);
    }
}
