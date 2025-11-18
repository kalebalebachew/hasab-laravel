<?php

namespace Hasab\Tests\Feature;

use Hasab\Facades\Hasab as FacadesHasab;
use Hasab\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class TranslationTest extends TestCase
{
    public function test_translation_sends_correct_request()
    {
        // Fake all HTTP requests to avoid hitting the real API
        Http::fake([
            '*' => Http::response(['translated_text' => 'Selam'], 200)
        ]);

        // Call the translation service via the Hasab facade
        $response = FacadesHasab::translation()->translate([
            'text' => "Hello",
            'source_language' => 'eng',
            'target_language' => 'amh'
        ]);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                   str_contains($request->url(), 'translate');
        });

        $this->assertEquals('Selam', $response['translated_text']);
    }
}
