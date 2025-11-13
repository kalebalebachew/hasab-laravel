<?php

namespace Hasab\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;

class HasabClient
{
    public function __construct(protected array $config)
    {
    }

    protected function base(): PendingRequest
    {
        $base = rtrim($this->config['base_url'], '/') . '/';

        return Http::withOptions([
            'base_uri' => $base,
        ])
        ->acceptJson()
        ->withHeaders([
            'Authorization' => 'Bearer ' . $this->config['key'],
        ]);
    }

    public function get(string $uri, array $query = []): array
    {
        return $this->base()
            ->get($uri, $query)
            ->throw()
            ->json();
    }

    public function post(string $uri, array $data = []): array
    {
        return $this->base()
            ->post($uri, $data)
            ->throw()
            ->json();
    }

    public function postJson(string $uri, array $data = []): array
    {
        $response = $this->base()
            ->asJson()
            ->post($uri, $data)
            ->throw();
        
        // Check content type
        $contentType = $response->header('Content-Type');
        
        // If response is not JSON (e.g., audio/binary), return metadata
        if ($contentType && !str_contains($contentType, 'json')) {
            return [
                'content_type' => $contentType,
                'body' => base64_encode($response->body()),
                'size' => strlen($response->body()),
                'headers' => $response->headers(),
            ];
        }
            
        // Try to parse as JSON
        $json = $response->json();
        return is_array($json) ? $json : [];
    }

    public function postMultipart(string $uri, array $files = [], array $data = []): array
    {
        $request = $this->base();

        foreach ($files as $key => $file) {
            $path = is_array($file) ? $file['path'] : $file;
            $name = is_array($file) && isset($file['name']) ? $file['name'] : basename($path);
            $mime = is_array($file) && isset($file['mime']) ? $file['mime'] : null;

            $request = $request->attach($key, fopen($path, 'r'), $name, [
                'Content-Type' => $mime,
            ]);
        }

        return $request
            ->asMultipart()
            ->post($uri, $data)
            ->throw()
            ->json();
    }

    public function delete(string $uri): array
    {
        return $this->base()
            ->delete($uri)
            ->throw()
            ->json();
    }
}
