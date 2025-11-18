<?php

namespace Hasab\Tests\Feature;

use Hasab\Facades\Hasab;
use Hasab\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class TranscriptionTest extends TestCase
{
    public function test_transcription_sends_request()
    {
        // Fake all HTTP requests
        Http::fake([
            '*' => Http::response([
                'transcription' => 'Hello world',
                'audio' => ['id' => 1]
            ], 200)
        ]);

        // Call the service using a URL 
        $response = Hasab::transcription()->upload([
            'url' => 'https://example.com/audio.mp3'
        ]);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), 'upload-audio');
        });

        $this->assertEquals('Hello world', $response['transcription']);
    }
}
