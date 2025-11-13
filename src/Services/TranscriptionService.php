<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;
use InvalidArgumentException;

class TranscriptionService
{
    public function __construct(protected HasabClient $http) {}

    /**
     * Upload and transcribe an audio file
     * 
     * @param array $options Available options:
     *   - file (string|required): Path to the audio file
     *   - url (string|optional): URL to the audio file (alternative to file)
     *   - key (string|optional): Unique identifier for the audio
     *   - is_meeting (bool|optional): Whether this is a meeting recording
     *   - transcribe (bool): Enable transcription (default: true)
     *   - translate (bool): Enable translation (default: false)
     *   - summarize (bool): Enable summarization (default: false)
     *   - language (string): Target language (default: 'auto')
     *   - source_language (string): Source language code (amh, eng, orm, etc.)
     *   - timestamps (bool): Include word-level timestamps (default: false)
     * 
     * @return array
     * @throws InvalidArgumentException
     */
    public function upload(array $options): array
    {
        $data = [
            'key' => $options['key'] ?? uniqid('audio_', true),
            'is_meeting' => $options['is_meeting'] ?? false,
            'transcribe' => $options['transcribe'] ?? true,
            'translate' => $options['translate'] ?? false,
            'summarize' => $options['summarize'] ?? false,
            'language' => $options['language'] ?? 'auto',
            'timestamps' => $options['timestamps'] ?? false,
        ];

        if (isset($options['source_language'])) {
            $data['source_language'] = $options['source_language'];
        }

        if (isset($options['url'])) {
            $data['url'] = $options['url'];
            return $this->http->post('upload-audio', $data);
        }

        // Check if file path is provi  ded (file upload)
        if (isset($options['file'])) {
            $file = $options['file'];
            
            if (!file_exists($file)) {
                throw new InvalidArgumentException("Audio file not found: {$file}");
            }

            return $this->http->postMultipart('upload-audio', [
                'file' => $file,
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
