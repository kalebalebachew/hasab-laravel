<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;

class TranscriptionService
{
    public function __construct(protected HasabClient $http) {}

    public function upload(array $options): array
    {
        if (isset($options['file'])) {
            return $this->http->postMultipart('upload-audio', [
                'file' => $options['file'],
            ], array_filter([
                'url' => $options['url'] ?? null,
                'key' => $options['key'] ?? null,
                'is_meeting' => $options['is_meeting'] ?? false,
                'transcribe' => $options['transcribe'] ?? true,
                'translate' => $options['translate'] ?? false,
                'summarize' => $options['summarize'] ?? false,
                'language' => $options['language'] ?? 'auto',
                'source_language' => $options['source_language'] ?? null,
            ], fn($value) => $value !== null));
        }

        return $this->http->postJson('upload-audio', array_filter([
            'url' => $options['url'] ?? null,
            'key' => $options['key'] ?? null,
            'is_meeting' => $options['is_meeting'] ?? false,
            'transcribe' => $options['transcribe'] ?? true,
            'translate' => $options['translate'] ?? false,
            'summarize' => $options['summarize'] ?? false,
            'language' => $options['language'] ?? 'auto',
            'source_language' => $options['source_language'] ?? null,
        ], fn($value) => $value !== null));
    }

    public function history(int $page = 1): array
    {
        return $this->http->get('audios', ['page' => $page]);
    }
}
