<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;

class TtsService
{
    public function __construct(protected HasabClient $http) {}

    public function synthesize(string $text, string $language, ?string $speaker = null): array
    {
        return $this->http->postJson('tts/synthesize', [
            'text' => $text,
            'language' => $language,
            'speaker_name' => $speaker,
        ]);
    }

    public function speakers(?string $language = null): array
    {
        return $this->http->get('tts/speakers', array_filter(['language' => $language]));
    }

    public function history(): array
    {
        return $this->http->get('tts/history');
    }

    public function delete(int|string $id): array
    {
        return $this->http->delete("tts/record/{$id}");
    }
}
