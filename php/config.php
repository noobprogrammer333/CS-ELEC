<?php

function loadEnvFile(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($trimmed, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $trimmed, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if (getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

loadEnvFile(dirname(__DIR__) . '/.env');

define('SUPABASE_URL', rtrim((string) getenv('SUPABASE_URL'), '/'));
define('SUPABASE_KEY', (string) getenv('SUPABASE_KEY'));
define('SUPABASE_NOTES_TABLE', getenv('SUPABASE_NOTES_TABLE') ?: 'notes');
define('SUPABASE_CHATS_TABLE', getenv('SUPABASE_CHATS_TABLE') ?: 'assistant_chats');
define('PYTHON_BIN', getenv('PYTHON_BIN') ?: 'python3');
define('NLP_PROCESSOR', dirname(__DIR__) . '/nlp/nlp_processor.py');

function sendJson(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_PRETTY_PRINT);
    exit;
}

function readJsonBody(): array
{
    $rawBody = file_get_contents('php://input') ?: '';
    $data = json_decode($rawBody, true);
    return is_array($data) ? $data : [];
}
