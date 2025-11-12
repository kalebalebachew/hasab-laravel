<?php

namespace Hasab;

use Hasab\Services\{TranscriptionService, TranslationService, TtsService, ChatService};

class HasabManager
{
    public function __construct(
        protected TranscriptionService $transcription,
        protected TranslationService $translation,
        protected TtsService $tts,
        protected ChatService $chat
    ) {}

    public function transcription(): TranscriptionService
    {
        return $this->transcription;
    }

    public function translation(): TranslationService
    {
        return $this->translation;
    }

    public function tts(): TtsService
    {
        return $this->tts;
    }

    public function chat(): ChatService
    {
        return $this->chat;
    }
}
