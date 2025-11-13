<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;

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
