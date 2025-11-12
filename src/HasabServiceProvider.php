<?php

namespace Hasab;

use Illuminate\Support\ServiceProvider;
use Hasab\Http\HasabClient;
use Hasab\Services\{TranscriptionService, TranslationService, TtsService, ChatService};

class HasabServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/hasab.php', 'hasab');

        // Core HTTP client
        $this->app->singleton(HasabClient::class, fn() => new HasabClient(config('hasab')));

        // Manager + alias for Facade
        $this->app->singleton(HasabManager::class, function ($app) {
            $client = $app->make(HasabClient::class);

            return new HasabManager(
                new TranscriptionService($client),
                new TranslationService($client),
                new TtsService($client),
                new ChatService($client),
            );
        });

        $this->app->alias(HasabManager::class, 'hasab');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/hasab.php' => config_path('hasab.php'),
            ], 'hasab-config');
        }
    }
}
