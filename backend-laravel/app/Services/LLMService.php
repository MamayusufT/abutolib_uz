<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LLMService
{
    protected $apiKey;
    protected $baseUrl = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
    }

    public function generateTests($model, $prompt, $content)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'HTTP-Referer' => config('app.url'),
            'X-Title' => 'AI Test Generator',
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl, [
            'model' => $model, // Masalan: "anthropic/claude-3.5-sonnet"
            'messages' => [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $content],
            ],
            'response_format' => ['type' => 'json_object']
        ]);

        return $response->json();
    }
}
