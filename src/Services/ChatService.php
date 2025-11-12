<?php

namespace Hasab\Services;

use Hasab\Http\HasabClient;

class ChatService
{
    public function __construct(protected HasabClient $http) {}

    public function complete(array $params): array
    {
        if (isset($params['image'])) {
            return $this->http->postMultipart('/chat', [
                'image' => $params['image'],
            ], [
                'message' => $params['message'] ?? '',
                'model' => $params['model'] ?? 'hasab-1-lite',
            ]);
        }

        return $this->http->postJson('/chat', $params);
    }

    public function history(): array
    {
        return $this->http->get('/chat/history');
    }

    public function clear(): array
    {
        return $this->http->postJson('/chat/clear');
    }

    public function title(): array
    {
        return $this->http->get('/chat/title');
    }

    public function updateTitle(string $title): array
    {
        return $this->http->postJson('/chat/title', ['title' => $title]);
    }
}
