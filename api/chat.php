<?php

require_once __DIR__ . '/../php/NlpProcessor.php';
require_once __DIR__ . '/../php/SupabaseClient.php';

function normalizeJsonArray(mixed $value): array
{
    if (is_array($value)) {
        return array_values($value);
    }

    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return array_values($decoded);
        }
    }

    return [];
}

function findMatchingNotes(array $notes, array $queryKeywords, string $query): array
{
    $queryTerms = array_unique(array_map('strtolower', $queryKeywords));
    $queryWords = preg_split('/\W+/', strtolower($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $queryTerms = array_unique(array_merge($queryTerms, $queryWords));

    $matches = [];
    foreach ($notes as $note) {
        $noteKeywords = array_map('strtolower', normalizeJsonArray($note['keywords'] ?? []));
        $noteLemmas = array_map('strtolower', normalizeJsonArray($note['lemmas'] ?? []));
        $noteText = strtolower(($note['title'] ?? '') . ' ' . ($note['content'] ?? ''));
        $score = 0;

        foreach ($queryTerms as $term) {
            if ($term === '' || strlen($term) < 3) {
                continue;
            }

            if (in_array($term, $noteKeywords, true)) {
                $score += 3;
            }

            if (in_array($term, $noteLemmas, true)) {
                $score += 2;
            }

            if (str_contains($noteText, $term)) {
                $score += 1;
            }
        }

        if ($score > 0) {
            $note['match_score'] = $score;
            $matches[] = $note;
        }
    }

    usort($matches, fn ($a, $b) => ($b['match_score'] ?? 0) <=> ($a['match_score'] ?? 0));
    return array_slice($matches, 0, 3);
}

function buildAssistantAnswer(string $question, array $matches): string
{
    if (!$matches) {
        return 'I scanned your saved notes but did not find a matching lesson yet. Try adding a note with related keywords first.';
    }

    $answer = "I scanned your notes and found these relevant lesson notes:\n";
    foreach ($matches as $index => $note) {
        $title = $note['title'] ?? 'Untitled Note';
        $content = trim((string) ($note['content'] ?? ''));
        $snippet = substr($content, 0, 280);
        $keywords = implode(', ', array_slice(normalizeJsonArray($note['keywords'] ?? []), 0, 5));
        $answer .= "\n" . ($index + 1) . ". " . $title . "\n";
        $answer .= $snippet . (strlen($content) > 280 ? '...' : '') . "\n";
        if ($keywords !== '') {
            $answer .= 'Keywords: ' . $keywords . "\n";
        }
    }

    $answer .= "\nQuestion asked: " . $question;
    return $answer;
}

try {
    $payload = readJsonBody();
    $message = trim((string) ($payload['message'] ?? ''));

    if ($message === '') {
        sendJson(['error' => 'Message is required.'], 400);
    }

    $processor = new NlpProcessor();
    $analysis = $processor->analyze($message);
    $supabase = new SupabaseClient();
    $notesResult = $supabase->fetchNotes();
    $notes = $notesResult['ok'] && is_array($notesResult['data']) ? $notesResult['data'] : [];
    $matches = findMatchingNotes($notes, $analysis['keywords'], $message);
    $assistantAnswer = buildAssistantAnswer($message, $matches);
    $analysis['response'] = $assistantAnswer;

    $record = [
        'question' => $message,
        'answer' => $assistantAnswer,
        'query_tokens' => $analysis['tokens'],
        'query_keywords' => $analysis['keywords'],
        'matched_note_ids' => array_values(array_filter(array_map(fn ($note) => $note['id'] ?? null, $matches))),
    ];

    $storageResult = $supabase->insertChat($record);

    sendJson([
        'question' => $message,
        'bot_response' => $assistantAnswer,
        'analysis' => $analysis,
        'matches' => $matches,
        'storage' => [
            'saved' => $storageResult['ok'],
            'status' => $storageResult['status'],
            'warning' => $storageResult['ok'] ? ($notesResult['ok'] ? null : $notesResult['error']) : $storageResult['error'],
        ],
    ]);
} catch (Throwable $error) {
    sendJson(['error' => $error->getMessage()], 500);
}
