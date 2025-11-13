# Hasab AI Laravel SDK - Usage Guide

This guide provides comprehensive examples for using the Hasab AI Laravel SDK in your Laravel applications.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage Patterns](#usage-patterns)
- [Chat Service](#chat-service)
- [Transcription Service](#transcription-service)
- [Translation Service](#translation-service)
- [Text-to-Speech (TTS) Service](#text-to-speech-tts-service)
- [Error Handling](#error-handling)

## Installation

Install the package via Composer:

```bash
composer require kalebalebachew/hasab-laravel
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=hasab-config
```

## Configuration

Add your Hasab AI API credentials to your `.env` file:

```env
HASAB_API_KEY=your-api-key-here
HASAB_BASE_URL=https://hasab.co/api
HASAB_API_VERSION=v1
```

The configuration file (`config/hasab.php`) will be published to your application:

```php
return [
    'key' => env('HASAB_API_KEY', ''),
    'base_url' => env('HASAB_BASE_URL', 'https://hasab.co/api'),
    'version' => env('HASAB_API_VERSION', 'v1'),
];
```

## Usage Patterns

The SDK supports three different usage patterns:

### 1. Facade (Recommended)

```php
use Hasab\Facades\Hasab;

$response = Hasab::chat()->complete([
    'message' => 'Hello, Hasab!',
    'model' => 'hasab-1-lite',
]);
```

### 2. Dependency Injection

```php
use Hasab\HasabManager;

class MyController extends Controller
{
    public function __construct(protected HasabManager $hasab)
    {
    }

    public function index()
    {
        $response = $this->hasab->chat()->complete([
            'message' => 'Hello, Hasab!',
        ]);

        return view('chat', compact('response'));
    }
}
```

### 3. Service Container

```php
$hasab = app(Hasab\HasabManager::class);

$response = $hasab->chat()->complete([
    'message' => 'Hello, Hasab!',
]);
```

---

## Chat Service

The Chat Service allows you to interact with Hasab AI's conversational models.

### Send a Chat Message

```php
use Hasab\Facades\Hasab;

$response = Hasab::chat()->complete([
    'message' => 'What is the capital of Ethiopia?',
    'model' => 'hasab-1-lite', // Optional, defaults to 'hasab-1-lite'
]);

// Response format:
// [
//     'response' => 'The capital of Ethiopia is Addis Ababa.',
//     'model' => 'hasab-1-lite',
//     ...
// ]
```

### Chat with Image (Vision)

```php
$response = Hasab::chat()->complete([
    'message' => 'What is in this image?',
    'image' => storage_path('app/images/photo.jpg'),
    'model' => 'hasab-vision', // Optional
]);
```

### Get Chat History

```php
$history = Hasab::chat()->history();

// Returns array of previous chat messages
```

### Clear Chat History

```php
$response = Hasab::chat()->clear();

// Clears all chat history for the current session
```

### Get Chat Title

```php
$title = Hasab::chat()->title();

// Returns the title of the current chat session
```

### Update Chat Title

```php
$response = Hasab::chat()->updateTitle('My Important Conversation');

// Sets a custom title for the chat session
```

## Transcription Service

The Transcription Service converts audio files to text. Supported formats: MP3, WAV, M4A.

> **Important**: The service automatically includes required fields (`key` and `is_meeting`) when you upload. You typically only need to provide the `file` or `url` parameter.

### Basic Transcription (File Upload)

```php
use Hasab\Facades\Hasab;

$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/recording.mp3'),
    // Optional: 'key' => 'unique-audio-id',     // Auto-generated if not provided
    // Optional: 'is_meeting' => false,           // Default: false
]);

// Response includes:
// - transcription: The transcribed text
// - audio: Audio file details and metadata
// - tokens_used: Number of tokens consumed
```

### Transcription from URL

If your audio file is already hosted online, you can provide a URL instead:

```php
$result = Hasab::transcription()->upload([
    'url' => 'https://example.com/audio/recording.mp3',
    'key' => 'my-unique-audio-key',    // Required when using URL
    'is_meeting' => false,              // Optional: mark as meeting recording
]);
```

### Transcribe with All Options

```php
$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/recording.wav'),
    
    // Identification (auto-generated if not provided)
    'key' => 'interview-2024-01-15',    // Unique identifier for this audio
    'is_meeting' => false,               // Is this a meeting recording?
    
    // Processing options
    'transcribe' => true,                // Enable transcription (default: true)
    'translate' => false,                // Enable translation (default: false)
    'summarize' => false,                // Enable summarization (default: false)
    'timestamps' => false,               // Include timestamps (default: false)
    
    // Language settings
    'language' => 'auto',                // Target language (default: 'auto')
    'source_language' => 'amh',          // Source language: 'amh', 'eng', 'orm', etc.
]);

// Response structure:
// [
//     'success' => true,
//     'message' => 'Audio uploaded and processed successfully',
//     'audio' => [
//         'id' => 8769,
//         'filename' => 'audio.mp3',
//         'transcription' => 'The transcribed text...',
//         'translation' => '',
//         'summary' => '',
//         'duration_in_seconds' => 23.98,
//         'audio_url' => 'https://hasab.s3.amazonaws.com/...',
//         ...
//     ],
//     'transcription' => 'The transcribed text...',
//     'timestamp' => [...], // If timestamps: true
//     'translation' => '',
//     'summary' => '',
//     'metadata' => [
//         'tokens_charged' => 6,
//         'remaining_tokens' => 150,
//         'charge_message' => 'Tokens charged successfully'
//     ]
// ]
```

### Transcribe with Translation

```php
$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/speech.mp3'),
    'transcribe' => true,
    'translate' => true,
    'source_language' => 'amh',        // Amharic audio
    'language' => 'eng',               // Translate to English
]);

// Access the translation
$translatedText = $result['translation'];
```

### Transcribe with Summarization

```php
$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/meeting.mp3'),
    'key' => 'meeting-jan-2024',        // Optional unique identifier
    'transcribe' => true,
    'summarize' => true,
    'language' => 'auto',
]);

// Access the summary
$summary = $result['summary'];
$fullTranscript = $result['transcription'];
```

### Transcribe with Timestamps

```php
$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/interview.wav'),
    'transcribe' => true,
    'timestamps' => true,              // Get word-level timestamps
    'source_language' => 'eng',
]);

// Access timestamps
foreach ($result['timestamp'] as $segment) {
    echo "[$segment[start]s - $segment[end]s]: $segment[content]\n";
}

// Output example:
// [0s - 1.52s]: የፋሲልለደስ ቤተመንግስት እዚህ ጋር
// [1.6s - 3.36s]: እንደምታዩት በፊት ከነበረው ከለር
```

### Transcribe Meeting Recordings

Mark audio files as meeting recordings for better processing:

```php
$result = Hasab::transcription()->upload([
    'file' => storage_path('app/meetings/team-standup.mp3'),
    'key' => 'standup-2024-01-15',
    'is_meeting' => true,               // Mark as meeting recording
    'transcribe' => true,
    'summarize' => true,                // Get meeting summary
    'timestamps' => true,               // Track who spoke when
]);

// Access meeting-specific data
$summary = $result['summary'];
$transcript = $result['transcription'];
```

### Get Transcription History

```php
// Get first page
$transcriptions = Hasab::transcription()->history();

// Get specific page
$page2 = Hasab::transcription()->history(page: 2);

// Response structure:
// [
//     'status' => 'success',
//     'data' => [
//         'current_page' => 1,
//         'data' => [...], // Array of transcription records
//         'total' => 439,
//         'per_page' => 15,
//         'last_page' => 30,
//         ...
//     ]
// ]
```

### Get Specific Transcription

Retrieve a previously processed transcription by its ID:

```php
$audio = Hasab::transcription()->get(8769);

// Returns:
// [
//     'id' => 8769,
//     'filename' => 'recording.mp3',
//     'transcription' => 'Full transcribed text...',
//     'translation' => '...',
//     'summary' => '...',
//     'duration_in_seconds' => 23.98,
//     'audio_url' => 'https://hasab.s3.amazonaws.com/...',
//     'created_at' => '2024-01-15T10:30:00Z',
//     ...
// ]
```

### Delete Transcription

Remove a transcription from your history:

```php
$result = Hasab::transcription()->delete(8769);

// Returns:
// [
//     'success' => true,
//     'message' => 'Audio file deleted successfully'
// ]
```

## Translation Service

The Translation Service provides audio-to-text translation. It automatically transcribes and translates audio in one step.

### Translate Audio File

```php
use Hasab\Facades\Hasab;

// Basic translation
$result = Hasab::translation()->upload([
    'file' => storage_path('app/audio/speech.mp3'),
    'source_language' => 'amh',        // Source language (Amharic)
    'language' => 'eng',               // Target language (English)
]);

// Access the translated text
$translatedText = $result['translation'];
```

### Translate with Additional Options

```php
$result = Hasab::translation()->upload([
    'file' => storage_path('app/audio/speech.mp3'),
    'source_language' => 'amh',
    'language' => 'eng',
    'summarize' => true,               // Also generate a summary
    'timestamps' => true,              // Include timestamps
]);

// Access results
$translation = $result['translation'];
$summary = $result['summary'];
$timestamps = $result['timestamp'];
```

### Get Translation History

```php
// Get first page
$translations = Hasab::translation()->history();

// With pagination
$page2 = Hasab::translation()->history(page: 2);
```

## Text-to-Speech (TTS) Service

The TTS Service converts text to natural-sounding speech.

### Synthesize Speech

```php
use Hasab\Facades\Hasab;

$result = Hasab::tts()->synthesize(
    text: 'እንኳን ደህና መጣህ። Welcome to Hasab AI.',
    language: 'am', // Language code
    speaker: 'default' // Optional: specific speaker name
);

// Response includes audio URL or audio data
```

### Get Available Speakers

```php
// Get all speakers
$speakers = Hasab::tts()->speakers();

// Get speakers for specific language
$amharicSpeakers = Hasab::tts()->speakers(language: 'am');
```

### Get TTS History

```php
$history = Hasab::tts()->history();

// Returns list of previously synthesized audio files
```

### Delete TTS Recording

```php
$result = Hasab::tts()->delete(id: 123);

// Deletes a specific TTS recording by ID
```

## Error Handling

The SDK throws exceptions when API requests fail. Always wrap your calls in try-catch blocks:

```php
use Hasab\Facades\Hasab;
use Illuminate\Http\Client\RequestException;

try {
    $response = Hasab::chat()->complete([
        'message' => 'Hello!',
    ]);

    // Process successful response
    return response()->json($response);

} catch (RequestException $e) {
    // Handle HTTP errors (4xx, 5xx)
    return response()->json([
        'error' => 'API request failed',
        'message' => $e->getMessage(),
        'status' => $e->response->status() ?? 500,
    ], $e->response->status() ?? 500);

} catch (\Exception $e) {
    // Handle general errors
    return response()->json([
        'error' => 'An unexpected error occurred',
        'message' => $e->getMessage(),
    ], 500);
}
```

### Handling Validation Errors

```php
try {
    $result = Hasab::transcription()->upload([
        'file' => $filePath,
        'language' => 'invalid-lang',
    ]);
} catch (RequestException $e) {
    if ($e->response->status() === 422) {
        // Validation error
        $errors = $e->response->json('errors');
        return back()->withErrors($errors);
    }

    throw $e;
}
```

### Logging API Calls

```php
use Illuminate\Support\Facades\Log;

try {
    $response = Hasab::chat()->complete([
        'message' => $message,
    ]);

    Log::info('Hasab API call successful', [
        'service' => 'chat',
        'message' => $message,
    ]);

    return $response;

} catch (\Exception $e) {
    Log::error('Hasab API call failed', [
        'service' => 'chat',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);

    throw $e;
}
```

---

## Advanced Examples

### Queue Processing for Long Audio Files

```php
namespace App\Jobs;

use Hasab\Facades\Hasab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAudioTranscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $filePath,
        protected int $userId
    ) {}

    public function handle(): void
    {
        try {
            $result = Hasab::transcription()->upload([
                'file' => $this->filePath,
                'transcribe' => true,
                'summarize' => true,
                'language' => 'auto',
                'timestamps' => true,
            ]);

            // Store results in database
            \App\Models\Transcription::create([
                'user_id' => $this->userId,
                'hasab_audio_id' => $result['audio']['id'],
                'file_path' => $this->filePath,
                'filename' => $result['audio']['filename'],
                'transcription' => $result['transcription'] ?? '',
                'summary' => $result['summary'] ?? '',
                'duration' => $result['audio']['duration_in_seconds'],
                'audio_url' => $result['audio']['audio_url'],
                'timestamps' => json_encode($result['timestamp'] ?? []),
                'tokens_used' => $result['metadata']['tokens_charged'],
                'status' => 'completed',
            ]);

        } catch (\Exception $e) {
            \Log::error('Transcription failed', [
                'error' => $e->getMessage(),
                'file' => $this->filePath,
            ]);

            throw $e;
        }
    }
}
```

## API Parameters Reference

### Transcription/Translation Parameters

| Parameter         | Type    | Required | Default  | Description                                 |
| ----------------- | ------- | -------- | -------- | ------------------------------------------- |
| `file`            | string  | Yes      | -        | Path to audio file (MP3, WAV, M4A)          |
| `transcribe`      | boolean | No       | `true`   | Enable transcription                        |
| `translate`       | boolean | No       | `false`  | Enable translation                          |
| `summarize`       | boolean | No       | `false`  | Generate summary                            |
| `language`        | string  | No       | `'auto'` | Target language for output                  |
| `source_language` | string  | No       | `null`   | Source audio language (`amh`, `eng`, `orm`) |
| `timestamps`      | boolean | No       | `false`  | Include word-level timestamps               |

### Supported Languages

- `amh` - Amharic (አማርኛ)
- `eng` - English
- `orm` - Oromo (Afaan Oromoo)
- `auto` - Auto-detect (for transcription)

## Tips & Best Practices

1. **Store API Keys Securely**: Never commit your `.env` file or hardcode API keys
2. **Use Queues**: Process long-running operations (transcription, translation) in background jobs
3. **Cache Results**: Cache frequently accessed data like TTS speakers list
4. **Error Handling**: Always implement proper error handling and user feedback
5. **File Validation**: Validate file types (MP3, WAV, M4A) and sizes before uploading
6. **Timestamps**: Enable `timestamps: true` only when needed as it increases processing time
7. **Source Language**: Specify `source_language` for better accuracy when the language is known
8. **Monitor Token Usage**: Check `metadata.remaining_tokens` in responses to track your usage

---

## Support

For issues, questions, or contributions, please visit:

- GitHub: [kalebalebachew/hasab-laravel](https://github.com/kalebalebachew/hasab-laravel)
- Email: kalebalebachew4@gmail.com
