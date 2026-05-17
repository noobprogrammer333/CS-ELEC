<?php

require_once __DIR__ . '/config.php';

class SupabaseClient
{
    private string $baseUrl;
    private string $apiKey;
    private string $table;

    public function __construct(string $baseUrl = SUPABASE_URL, string $apiKey = SUPABASE_KEY, string $table = SUPABASE_CHATS_TABLE)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->table = $table;
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->apiKey !== '';
    }

    public function insertChat(array $record): array
    {
        return $this->request('POST', '/' . $this->table, $record, ['Prefer: return=representation']);
    }

    public function fetchHistory(int $limit = 50): array
    {
        $query = sprintf('/%s?select=*&order=created_at.desc&limit=%d', $this->table, $limit);
        return $this->request('GET', $query);
    }

    public function clearHistory(): array
    {
        return $this->request('DELETE', '/' . $this->table . '?id=not.is.null', null);
    }

    private function request(string $method, string $path, ?array $payload = null, array $extraHeaders = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => 'Supabase is not configured. Set SUPABASE_URL and SUPABASE_KEY.',
            ];
        }

        $url = $this->baseUrl . '/rest/v1' . $path;
        $headers = array_merge([
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ], $extraHeaders);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $responseBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($responseBody === false) {
            return [
                'ok' => false,
                'status' => $statusCode,
                'data' => null,
                'error' => $curlError ?: 'Supabase request failed.',
            ];
        }

        $decoded = json_decode($responseBody, true);
        $ok = $statusCode >= 200 && $statusCode < 300;

        return [
            'ok' => $ok,
            'status' => $statusCode,
            'data' => $decoded,
            'error' => $ok ? null : ($decoded['message'] ?? $responseBody),
        ];
    }
}
