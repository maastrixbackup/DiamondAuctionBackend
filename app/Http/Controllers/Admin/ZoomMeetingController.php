<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class ZoomMeetingController extends Controller
{
    public function generateSignature(Request $request)
    {
        // $envData = $this->getEnvData();
        // $sdkKey = $envData['ZOOM_SDK_KEY'];
        // $sdkSecret = $envData['ZOOM_SDK_SECRET'];

        $sdkKey = 'svbyx5yLKRI5yULPMGOx7ZSgUwTLDwHRxrIz';
        $sdkSecret = '2Ge043KpHP7qJbtvwoG2oiPmD7vaDyjrJvBl';

        // dd($sdkKey, $sdkSecret);
        $sessionName = $request->query('sessionName') ?? 'diamond-room-1';
        $userIdentity = 'host-' . uniqid();

        $iat = time();
        $exp = $iat + 3600;

        $payload = [
            'app_key' => $sdkKey,
            'iat' => $iat,
            'exp' => $exp,
            'tpc' => $sessionName,
            'user_identity' => $userIdentity,
            'role_type' => 1,
            'version' => 1,
        ];

        $signature = JWT::encode($payload, $sdkSecret, 'HS256');

        // dd([
        //     'app_key' => $sdkKey,
        //     'tpc' => $sessionName,
        //     'user_identity' => $userIdentity,
        //     'signature' => $signature
        // ]);

        return view('admin.zoom_meeting', [
            'signature' => $signature,
            'sessionName' => $sessionName,
            'userIdentity' => $userIdentity,
        ]);
    }

    public function createZoomMeeting()
    {
        $accessToken = $this->getZoomAccessToken();
        $envData = $this->getEnvData();
        // dd($envData['ZOOM_CLIENT_ID'], $envData['ZOOM_CLIENT_SECRET'], $envData['ZOOM_ACCOUNT_ID']);
        // dd($accessToken);
        $payload = json_encode([
            'topic' => 'Test Meeting',
            'type' => 2, // Scheduled meeting
            'start_time' => now()->addMinutes(10)->toIso8601String(),
            'duration' => 30,
            'timezone' => 'Asia/Kolkata',
            'settings' => [
                'join_before_host' => true,
                'approval_type' => 0,
                'waiting_room' => false,
            ],
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.zoom.us/v2/users/me/meetings');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);

        // Disable SSL verification (Not recommended in production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);

        $decodeData = json_decode($response, true);
        // dd($decodeData);

        // return json_decode($response, true);
        return view('admin.zoom_meeting', compact('decodeData'));
    }

    public function getZoomAccessToken()
    {
        $envData = $this->getEnvData();
        $clientId = $envData['ZOOM_CLIENT_ID'];
        $clientSecret = $envData['ZOOM_CLIENT_SECRET'];
        $accountId = $envData['ZOOM_ACCOUNT_ID'];

        $authorization = base64_encode("$clientId:$clientSecret");

        $postFields = http_build_query([
            'grant_type' => 'account_credentials',
            'account_id' => $accountId,
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://zoom.us/oauth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $authorization,
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        // Disable SSL verification — for local dev only
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }

        curl_close($ch);

        $data = json_decode($response, true);

        return $data['access_token'] ?? null;
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
