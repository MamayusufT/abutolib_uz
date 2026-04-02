<?php

// ══════════════════════════════════════════════════════════════
// App/Services/TelegramService.php
// ══════════════════════════════════════════════════════════════

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected string $token;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token  = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function sendMessage(string|int $chatId, string $text): bool
    {
        $res = Http::post("{$this->apiUrl}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ]);

        return $res->successful();
    }

    public function setWebhook(string $url): bool
    {
        $res = Http::post("{$this->apiUrl}/setWebhook", ['url' => $url]);
        return $res->successful();
    }
}
