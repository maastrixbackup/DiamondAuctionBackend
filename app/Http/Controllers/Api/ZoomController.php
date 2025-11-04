<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    public function generateSignature(Request $request)
    {
        $apiKey = env('ZOOM_API_KEY');
        $apiSecret = env('ZOOM_API_SECRET');
        $meetingNumber = $request->input('meetingNumber');
        $role = $request->input('role'); // 0 for participant, 1 for host

        $iat = time();
        $exp = $iat + 60 * 60 * 2;

        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'sdkKey' => $apiKey,
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $iat,
            'exp' => $exp,
            'appKey' => $apiKey,
            'tokenExp' => $exp
        ]);

        $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $signature = $base64UrlHeader . "." . $base64UrlPayload;

        $hash = hash_hmac('sha256', $signature, $apiSecret, true);
        $base64UrlHash = rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');

        return response()->json([
            'signature' => $signature . "." . $base64UrlHash
        ]);
    }
}
