<?php

require_once __DIR__ . '/../php/SupabaseClient.php';
require_once __DIR__ . '/../php/Auth.php';

requireAuthJson();

$supabase = new SupabaseClient();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $result = $supabase->clearChatHistory();
    sendJson([
        'cleared' => $result['ok'],
        'warning' => $result['ok'] ? null : $result['error'],
    ], $result['ok'] ? 200 : 503);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(['error' => 'Method not allowed.'], 405);
}

$result = $supabase->fetchChatHistory();
if (!$result['ok']) {
    sendJson([
        'history' => [],
        'warning' => $result['error'],
    ], 200);
}

$history = is_array($result['data']) ? array_reverse($result['data']) : [];
sendJson(['history' => $history]);
