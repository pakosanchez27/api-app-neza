<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TranslateController extends Controller
{
    public function translate(Request $request)
    {
        $data = $request->validate([
            'q' => ['required', 'array', 'min:1'],
            'q.*' => ['required', 'string'],
            'target' => ['required', 'string'],
            'source' => ['nullable', 'string'],
            'format' => ['nullable', 'string'],
        ]);

        $credentialsPath = env('GOOGLE_TRANSLATE_CREDENTIALS');

        if (!$credentialsPath || !file_exists($credentialsPath)) {
            return response()->json([
                'error' => ['message' => 'No se encontraron las credenciales de Google Translate.'],
            ], 500);
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);

        if (!$credentials || empty($credentials['client_email']) || empty($credentials['private_key'])) {
            return response()->json([
                'error' => ['message' => 'Las credenciales de Google Translate son invalidas.'],
            ], 500);
        }

        $accessToken = $this->getGoogleAccessToken($credentials);

        $response = Http::withToken($accessToken)
            ->withOptions(['verify' => false])
            ->post('https://translation.googleapis.com/language/translate/v2', [
                'q' => $data['q'],
                'target' => $data['target'],
                'source' => $data['source'] ?? 'es',
                'format' => $data['format'] ?? 'text',
            ]);

        return response()->json($response->json(), $response->status());
    }

    private function getGoogleAccessToken(array $credentials): string
    {
        $now = time();

        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $payload = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-translation',
            'aud' => $credentials['token_uri'],
            'exp' => $now + 3600,
            'iat' => $now,
        ]));

        $unsignedJwt = $header . '.' . $payload;

        openssl_sign($unsignedJwt, $signature, $credentials['private_key'], 'sha256WithRSAEncryption');

        $jwt = $unsignedJwt . '.' . $this->base64UrlEncode($signature);

        $tokenResponse = Http::asForm()
            ->withOptions(['verify' => false])
            ->post($credentials['token_uri'], [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

        if (!$tokenResponse->ok()) {
            abort(response()->json([
                'error' => ['message' => 'No fue posible autenticar con Google.'],
            ], 500));
        }

        return $tokenResponse->json('access_token');
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
