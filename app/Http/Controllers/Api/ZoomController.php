<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    public function generateSignature(Request $request)
    {
        try {
            $envData = $this->getEnvData();
            $sdkKey = $envData['ZOOM_SDK_KEY'];
            $sdkSecret = $envData['ZOOM_SDK_SECRET'];

            $sessionName = $request->query('sessionName'); // e.g., diamond-room
            $role = $request->query('role') ?? 0; // 1 = host, 0 = participant
            $userIdentity = $request->query('user'); // any string identifier

            $iat = time();
            $exp = $iat + 3600; // valid for 1 hour

            $payload = [
                'app_key' => $sdkKey,
                'iat' => $iat,
                'exp' => $exp,
                'tpc' => $sessionName,
                'user_identity' => $userIdentity,
                'role_type' => (int) $role,
                'version' => 1,
            ];

            $jwt = JWT::encode($payload, $sdkSecret, 'HS256');

            return response()->json(['status' => true, 'signature' => $jwt]);

            // $topic = $request->input('topic', 'diamond-auction');
            // $topic = preg_replace('/[^a-zA-Z0-9-_]/', '', $topic);

            // $roleType = (int) $request->input('role', 0);
            // $roleType = in_array($roleType, [0, 1]) ? $roleType : 0;

            // $iat = time();
            // $exp = $iat + (2 * 60 * 60); // 2 hours

            // $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

            // $payload = json_encode([
            //     'app_key'   => $sdkKey,
            //     'iat'       => $iat,
            //     'exp'       => $exp,
            //     'tokenExp'  => $exp,
            //     'tpc'       => $topic,       // this is the session name
            //     'role_type' => $roleType,
            //     'version'   => 1
            // ]);

            // $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
            // $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
            // $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $sdkSecret, true);
            // $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

            // $jwt = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";

            // return response()->json([
            //     'status'    => true,
            //     'signature' => $jwt,
            //     'topic'     => $topic,
            //     'sdkKey'    => $sdkKey
            // ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
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
