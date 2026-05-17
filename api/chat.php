<?php

require_once __DIR__ . '/../php/NlpProcessor.php';
require_once __DIR__ . '/../php/SupabaseClient.php';

try {
    $payload = readJsonBody();
    $message = trim((string) ($payload['message'] ?? ''));

    if ($message === '') {
        sendJson(['error' => 'Message is required.'], 400);
    }

    $processor = new NlpProcessor();
    $analysis = $processor->analyze($message);

    $record = [
        'user_message' => $message,
        'bot_response' => $analysis['response'],
        'tokens' => $analysis['tokens'],
        'keywords' => $analysis['keywords'],
        'sentiment' => $analysis['sentiment'],
        'sentiment_score' => $analysis['sentiment_score'],
        'classification' => $analysis['classification'],
        'entities' => $analysis['entities'],
    ];

    $supabase = new SupabaseClient();
    $storageResult = $supabase->insertChat($record);

    sendJson([
        'user_message' => $message,
        'bot_response' => $analysis['response'],
        'analysis' => $analysis,
        'storage' => [
            'saved' => $storageResult['ok'],
            'status' => $storageResult['status'],
            'warning' => $storageResult['ok'] ? null : $storageResult['error'],
        ],
    ]);
} catch (Throwable $error) {
    sendJson(['error' => $error->getMessage()], 500);
}
