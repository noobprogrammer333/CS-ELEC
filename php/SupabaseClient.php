<?php

require_once __DIR__ . '/config.php';

class SupabaseClient
{
    private string $baseUrl;
    private string $apiKey;
    private string $notesTable;
    private string $chatsTable;

    public function __construct(
        string $baseUrl = SUPABASE_URL,
        string $apiKey = SUPABASE_KEY,
        string $notesTable = SUPABASE_NOTES_TABLE,
        string $chatsTable = SUPABASE_CHATS_TABLE
    )
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->notesTable = $notesTable;
        $this->chatsTable = $chatsTable;
    }

    public function isConfigured(): bool
    {
        return $this->baseUrl !== '' && $this->apiKey !== '';
    }

    public function insertNote(array $record): array
    {
        return $this->request('POST', '/' . $this->notesTable, $record, ['Prefer: return=representation']);
    }

    public function fetchNotes(int $limit = 100): array
    {
        $query = sprintf('/%s?select=*&order=updated_at.desc&limit=%d', $this->notesTable, $limit);
        return $this->request('GET', $query);
    }

    public function clearNotes(): array
    {
        return $this->request('DELETE', '/' . $this->notesTable . '?id=not.is.null', null);
    }

    public function insertChat(array $record): array
    {
        return $this->request('POST', '/' . $this->chatsTable, $record, ['Prefer: return=representation']);
    }

    public function fetchChatHistory(int $limit = 50): array
    {
        $query = sprintf('/%s?select=*&order=created_at.desc&limit=%d', $this->chatsTable, $limit);
        return $this->request('GET', $query);
    }

    public function clearChatHistory(): array
    {
        return $this->request('DELETE', '/' . $this->chatsTable . '?id=not.is.null', null);
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
