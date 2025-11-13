<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;
use InvalidArgumentException;

/**
 * Translation Service
 * 
 * ⚠️ ONGOING IMPLEMENTATION
 * This service is currently under active development.
 * The API interface may change in future releases.
 * 
 * Provides text-to-text translation between supported languages.
 */
class TranslationService
{
    public function __construct(protected HasabClient $http) {}

    /**
     * Translate text from one language to another
     * 
     * @param array $options Available options:
     *   - text (string|array|required): Text to translate (string or array of strings)
     *   - source_language (string|required): Source language ISO code (e.g., 'eng', 'amh', 'orm')
     *   - target_language (string|required): Target language ISO code (e.g., 'eng', 'amh', 'orm')
     * 
     * @return array
     * @throws InvalidArgumentException
     */
    public function translate(array $options): array
    {
        if (!isset($options['text'])) {
            throw new InvalidArgumentException('The "text" field is required');
        }

        if (!isset($options['source_language'])) {
            throw new InvalidArgumentException('The "source_language" field is required');
        }

        if (!isset($options['target_language'])) {
            throw new InvalidArgumentException('The "target_language" field is required');
        }

        $text = $options['text'];
        if (is_string($text)) {
            $text = [$text];
        }

        $multipartData = [
            [
                'name' => 'text',
                'contents' => json_encode($text),
            ],
            [
                'name' => 'source_language',
                'contents' => $options['source_language'],
            ],
            [
                'name' => 'target_language',
                'contents' => $options['target_language'],
            ],
        ];

        return $this->http->postMultipartForm('translate', $multipartData);
    }

    /**
     * Get translation history
     * 
     * @param int $page Page number for pagination
     * @return array
     */
    public function history(int $page = 1): array
    {
        return $this->http->get('translations', ['page' => $page]);
    }
}
