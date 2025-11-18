<?php

namespace Hasab\Tests\Feature;

use Hasab\Facades\Hasab;
use Hasab\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class ChatTest extends TestCase
{
    public function test_chat_complete_sends_correct_request()
    {
        // Fake all HTTP requests
        Http::fake([
            '*' => Http::response(['reply' => 'Hello from Hasab'], 200)
        ]);

        // Call the service
        $response = Hasab::chat()->complete([
            'message' => 'Hello'
        ]);

        Http::assertSent(function ($request) {
        $body = json_decode($request->body(), true);
        return $request->method() === 'POST' &&
            str_contains($request->url(), 'chat') &&
            isset($body['message']); // optional.. checks POST payload
    });

        $this->assertEquals('Hello from Hasab', $response['reply']);
    }
}
