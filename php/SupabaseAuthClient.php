<?php

require_once __DIR__ . '/config.php';

class SupabaseAuthClient
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(string $baseUrl = SUPABASE_URL, string $apiKey = SUPABASE_KEY)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->apiKey !== '';
    }

    public function signInWithPassword(string $email, string $password): array
    {
        return $this->request(
            'POST',
            '/auth/v1/token?grant_type=password',
            [
                'email' => $email,
                'password' => $password,
            ]
        );
    }

    public function signUp(string $email, string $password): array
    {
        return $this->request(
            'POST',
            '/auth/v1/signup',
            [
                'email' => $email,
                'password' => $password,
            ]
        );
    }

    private function request(string $method, string $path, array $payload): array
    {
        if (!$this->isConfigured()) {
            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => 'Supabase Auth is not configured. Set SUPABASE_URL and SUPABASE_KEY in .env.',
            ];
        }

        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);

        $responseBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($responseBody === false) {
            return [
                'ok' => false,
                'status' => $statusCode,
                'data' => null,
                'error' => $curlError ?: 'Supabase Auth request failed.',
            ];
        }

        $decoded = json_decode($responseBody, true);
        $ok = $statusCode >= 200 && $statusCode < 300;

        return [
            'ok' => $ok,
            'status' => $statusCode,
            'data' => $decoded,
            'error' => $ok ? null : ($decoded['msg'] ?? $decoded['message'] ?? $responseBody),
        ];
    }
}
