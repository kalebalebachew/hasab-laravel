<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;

/**
 * Translation Service
 * 
 * ⚠️ ONGOING IMPLEMENTATION
 * This service is currently under active development.
 * The API interface may change in future releases.
 * 
 * Provides audio-to-text translation by automatically transcribing and translating audio in one step.
 */
class TranslationService
{
    public function __construct(protected HasabClient $http) {}

    public function upload(array $options): array
    {
        $options['translate'] = true;

        return (new TranscriptionService($this->http))->upload($options);
    }

    public function history(int $page = 1): array
    {
        return $this->http->get('translations', ['page' => $page]);
    }
}
