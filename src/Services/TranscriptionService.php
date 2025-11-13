<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;

class TranscriptionService
{
    public function __construct(protected HasabClient $http) {}

    public function upload(array $options): array
    {
        // If local file is provided, upload it first
        if (isset($options['file']) && !isset($options['url'])) {
            return $this->uploadFile($options);
        }

        // Process audio from URL (S3 or external)
        return $this->http->postJson('upload-audio', [
            'url' => $options['url'],
            'key' => $options['key'],
            'uuid' => $options['uuid'] ?? null,
            'is_meeting' => $options['is_meeting'] ?? false,
            'transcribe' => $options['transcribe'] ?? true,
            'translate' => $options['translate'] ?? false,
            'summarize' => $options['summarize'] ?? false,
            'language' => $options['language'] ?? 'auto',
            'source_language' => $options['source_language'] ?? null,
            'timestamps' => $options['timestamps'] ?? false,
        ]);
    }

    protected function uploadFile(array $options): array
    {
        // Upload file to get S3 URL first (if you have a file upload endpoint)
        // For now, we'll use multipart directly
        return $this->http->postMultipart('upload-audio', [
            'file' => $options['file'],
        ], [
            'uuid' => $options['uuid'] ?? null,
            'is_meeting' => $options['is_meeting'] ?? false,
            'transcribe' => $options['transcribe'] ?? true,
            'translate' => $options['translate'] ?? false,
            'summarize' => $options['summarize'] ?? false,
            'language' => $options['language'] ?? 'auto',
            'source_language' => $options['source_language'] ?? null,
            'timestamps' => $options['timestamps'] ?? false,
        ]);
    }

    public function history(int $page = 1): array
    {
        return $this->http->get('audios', ['page' => $page]);
    }
}
