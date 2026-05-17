<?php

require_once __DIR__ . '/config.php';

class NlpProcessor
{
    public function analyze(string $message): array
    {
        $command = escapeshellcmd(PYTHON_BIN) . ' ' . escapeshellarg(NLP_PROCESSOR);
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start NLP processor.');
        }

        fwrite($pipes[0], json_encode(['message' => $message]));
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        if ($exitCode !== 0) {
            throw new RuntimeException(trim($stderr) ?: 'NLP processor failed.');
        }

        $analysis = json_decode($stdout, true);
        if (!is_array($analysis)) {
            throw new RuntimeException('NLP processor returned invalid JSON.');
        }

        return $analysis;
    }
}
