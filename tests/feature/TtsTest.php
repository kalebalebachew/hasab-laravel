<?php

namespace Hasab\Tests\Feature;

use Hasab\Facades\Hasab;
use Hasab\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class TtsTest extends TestCase
{
    public function test_synthesize_sends_request()
    {
        // Fake all HTTP requests for synthesize
        Http::fake(['*' => Http::response(['audio_url' => 'https://fake.com/audio.mp3'], 200)]);

        // Call TTS synthesize method
        $response = Hasab::tts()->synthesize('Hello', 'eng', 'default');

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), 'tts/synthesize');
        });

        $this->assertEquals('https://fake.com/audio.mp3', $response['audio_url']);
    }

    public function test_speakers_sends_request()
    {
        // Fake the HTTP request for getting speakers
        Http::fake(['*' => Http::response(['speakers' => ['default']], 200)]);

        // Call TTS speakers method
        $response = Hasab::tts()->speakers();

        Http::assertSent(function ($request) {
            return $request->method() === 'GET' &&
                   str_contains($request->url(), 'tts/speakers');
        });

        $this->assertEquals(['default'], $response['speakers']);
    }

    public function test_history_sends_request()
    {
        // Fake HTTP request for history
        Http::fake(['*' => Http::response(['history' => [1, 2, 3]], 200)]);

        // Call TTS history method
        $response = Hasab::tts()->history();

        Http::assertSent(function ($request) {
            return $request->method() === 'GET' &&
                   str_contains($request->url(), 'tts/history');
        });

        $this->assertEquals([1, 2, 3], $response['history']);
    }

    public function test_delete_sends_request()
    {
        // Fake HTTP request for deleting a TTS record
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        // Call TTS delete method
        $response = Hasab::tts()->delete(123);

        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE' &&
                   str_contains($request->url(), 'tts/record/123');
        });

        $this->assertTrue($response['success']);
    }
}
