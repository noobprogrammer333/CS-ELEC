<?php

require_once __DIR__ . '/../php/NlpProcessor.php';
require_once __DIR__ . '/../php/SupabaseClient.php';

$supabase = new SupabaseClient();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $supabase->fetchNotes();
    if (!$result['ok']) {
        sendJson([
            'notes' => [],
            'warning' => $result['error'],
        ]);
    }

    $notes = is_array($result['data']) ? $result['data'] : [];
    sendJson(['notes' => $notes]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $result = $supabase->clearNotes();
    sendJson([
        'cleared' => $result['ok'],
        'warning' => $result['ok'] ? null : $result['error'],
    ], $result['ok'] ? 200 : 503);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['error' => 'Method not allowed.'], 405);
}

try {
    $payload = readJsonBody();
    $title = trim((string) ($payload['title'] ?? 'Untitled Note'));
    $content = trim((string) ($payload['content'] ?? ''));

    if ($content === '') {
        sendJson(['error' => 'Note content is required.'], 400);
    }

    $processor = new NlpProcessor();
    $analysis = $processor->analyze($content);

    $record = [
        'title' => $title !== '' ? $title : 'Untitled Note',
        'content' => $content,
        'tokens' => $analysis['tokens'],
        'lemmas' => $analysis['lemmas'],
        'keywords' => $analysis['keywords'],
        'sentiment' => $analysis['sentiment'],
        'sentiment_score' => $analysis['sentiment_score'],
        'classification' => $analysis['classification'],
        'entities' => $analysis['entities'],
    ];

    $storageResult = $supabase->insertNote($record);

    sendJson([
        'note' => $record,
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
