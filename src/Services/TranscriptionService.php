<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;
use InvalidArgumentException;

/**
 * Transcription Service
 * 
 * ⚠️ ONGOING IMPLEMENTATION
 * This service is currently under active development.
 * The API interface may change in future releases.
 * 
 * Converts audio files to text with optional translation and summarization.
 * Supported formats: MP3, WAV, M4A
 */
class TranscriptionService
{
    public function __construct(protected HasabClient $http) {}

    /**
     * Upload and transcribe an audio file
     * 
     * @param array $options Available options:
     *   - file (string|required): Path to the audio file
     *   - url (string|optional): URL to the audio file (alternative to file)
     *   - is_meeting (bool|optional): Whether this is a meeting recording (default: false)
     *   - translate (bool): Enable translation (default: false)
     *   - summarize (bool): Enable summarization (default: false)
     *   - language (string): Language code (eng, amh, orm, etc.)
     * 
     * @return array Response includes:
     *   - success: Operation success status
     *   - message: Success/error message
     *   - audio: File metadata including id, filename, duration, etc.
     *   - transcription: The transcribed text
     *   - translation: Translated text (if translate=true)
     *   - summary: Summary text (if summarize=true)
     *   - metadata: Usage information (tokens_charged, remaining_tokens)
     * 
     * @throws InvalidArgumentException
     */
    public function upload(array $options): array
    {
        $data = [
            'is_meeting' => $options['is_meeting'] ?? false,
            'translate' => $options['translate'] ?? false,
            'summarize' => $options['summarize'] ?? false,
        ];

        if (isset($options['language'])) {
            $data['language'] = $options['language'];
        }

        if (isset($options['url'])) {
            $data['url'] = $options['url'];
            return $this->http->post('upload-audio', $data);
        }

        if (isset($options['file'])) {
            $file = $options['file'];
            
            if (!file_exists($file)) {
                throw new InvalidArgumentException("Audio file not found: {$file}");
            }

            return $this->http->postMultipart('upload-audio', [
                'audio' => $file,
            ], $data);
        }

        throw new InvalidArgumentException('Either "file" (path) or "url" must be provided');
    }

    /**
     * Get transcription history
     * 
     * @param int $page Page number for pagination
     * @return array
     */
    public function history(int $page = 1): array
    {
        return $this->http->get('audios', ['page' => $page]);
    }

    /**
     * Get a specific audio transcription by ID
     * 
     * @param int $id The audio ID
     * @return array
     */
    public function get(int $id): array
    {
        return $this->http->get("audios/{$id}");
    }

    /**
     * Delete an audio transcription
     * 
     * @param int $id The audio ID to delete
     * @return array
     */
    public function delete(int $id): array
    {
        return $this->http->delete("audios/{$id}");
    }
}
