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
HASAB_BASE_URL=https://api.hasab.co/api
HASAB_API_VERSION=v1
```

The configuration file (`config/hasab.php`) will be published to your application:

```php
return [
    'key' => env('HASAB_API_KEY', ''),
    'base_url' => env('HASAB_BASE_URL', 'https://api.hasab.co/api'),
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

The Transcription Service converts audio files to text.

### Transcribe Audio File

```php
use Hasab\Facades\Hasab;

$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/recording.mp3'),
    'transcribe' => true,
    'translate' => false,
    'summarize' => false,
    'language' => 'auto', // or specific language code like 'en', 'am', etc.
]);

// Response includes transcription text and metadata
```

### Transcribe with Translation

```php
$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/speech.mp3'),
    'transcribe' => true,
    'translate' => true,
    'language' => 'am', // Amharic
    'source_language' => 'am', // Optional: specify source language
]);
```

### Transcribe with Summarization

```php
$result = Hasab::transcription()->upload([
    'file' => storage_path('app/audio/meeting.mp3'),
    'transcribe' => true,
    'summarize' => true,
    'language' => 'en',
]);
```

### Get Transcription History

```php
// Get first page
$transcriptions = Hasab::transcription()->history();

// Get specific page
$page2 = Hasab::transcription()->history(page: 2);
```


## Translation Service

The Translation Service provides audio translation capabilities.

### Translate Audio File

```php
use Hasab\Facades\Hasab;

$result = Hasab::translation()->upload([
    'file' => storage_path('app/audio/speech.mp3'),
    'language' => 'am', // Target language
    'source_language' => 'en', // Optional: source language
]);
```

### Get Translation History

```php
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
            ]);

            // Store results in database
            \App\Models\Transcription::create([
                'user_id' => $this->userId,
                'file_path' => $this->filePath,
                'text' => $result['text'] ?? '',
                'summary' => $result['summary'] ?? '',
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


## Tips & Best Practices

1. **Store API Keys Securely**: Never commit your `.env` file or hardcode API keys
2. **Use Queues**: Process long-running operations (transcription, translation) in background jobs
3. **Cache Results**: Cache frequently accessed data like TTS speakers list
4. **Error Handling**: Always implement proper error handling and user feedback
5. **File Validation**: Validate file types and sizes before uploading

---

## Support

For issues, questions, or contributions, please visit:

- GitHub: [kalebalebachew/hasab-laravel](https://github.com/kalebalebachew/hasab-laravel)
- Email: kalebalebachew4@gmail.com

