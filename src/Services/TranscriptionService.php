<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;

class TranscriptionService
{
    public function __construct(protected HasabClient $http) {}

    public function upload(array $options): array
    {
        $file = $options['file'];

        return $this->http->postMultipart('upload-audio', [
            'file' => $file,
        ], [
            'transcribe' => $options['transcribe'] ?? true,
            'translate' => $options['translate'] ?? false,
            'summarize' => $options['summarize'] ?? false,
            'language' => $options['language'] ?? 'auto',
            'source_language' => $options['source_language'] ?? null,
        ]);
    }

    public function history(int $page = 1): array
    {
        return $this->http->get('audios', ['page' => $page]);
    }
}
