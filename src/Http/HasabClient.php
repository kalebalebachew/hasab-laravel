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
        $base = rtrim($this->config['base_url'], '/') . '/' . trim($this->config['version'], '/');

        return Http::withOptions([
            'base_uri' => $base,
            'timeout' => $this->config['timeout'] ?? 30,
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
        return $this->base()
            ->asJson()
            ->post($uri, $data)
            ->throw()
            ->json();
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
