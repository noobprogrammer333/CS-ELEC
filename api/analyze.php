<?php

require_once __DIR__ . '/../php/NlpProcessor.php';
require_once __DIR__ . '/../php/Auth.php';

requireAuthJson();

try {
    $payload = readJsonBody();
    $message = trim((string) ($payload['message'] ?? ''));

    if ($message === '') {
        sendJson(['error' => 'Message is required.'], 400);
    }

    $processor = new NlpProcessor();
    sendJson(['analysis' => $processor->analyze($message)]);
} catch (Throwable $error) {
    sendJson(['error' => $error->getMessage()], 500);
}
