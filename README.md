# Hasab Laravel SDK

A simple Laravel SDK for integrating with [Hasab AI](https://developer.hasab.ai).  
It gives you a clean way to access Hasab’s core features — transcription, translation, text-to-speech (TTS), and chat — without writing repetitive HTTP logic.

This package is intentionally small and follows normal Laravel patterns.  
You get config publishing, a service provider, a facade, and a few service classes. No extra layers just straightforward code that works.

---

## Why this exists

Hasab provides powerful AI endpoints, but using them directly from Laravel requires setting up headers, base URLs, and file uploads each time.  
This SDK wraps those calls with a consistent interface so you can work like this:

```php
Hasab::tts()->synthesize('Hello world', 'eng');
```
instead of manually handling authentication or request payloads.

## Requirements

- PHP 8.0 or higher
- A valid Hasab API key from [Hasab AI](https://developer.hasab.ai)
  
## Installation

Install with Composer:
``` bash
composer require kalebalebachew/hasab-laravel
```

## Configuration

You can publish the config file (optional):
``` bash
php artisan vendor:publish --tag=hasab-config
```
Then add these to your .env:
``` bash
HASAB_API_KEY=your_api_key_here
HASAB_BASE_URL=https://hasab.co/api
HASAB_API_VERSION=v1
```


