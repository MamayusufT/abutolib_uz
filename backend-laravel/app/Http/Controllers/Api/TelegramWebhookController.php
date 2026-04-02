<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramWebhookController extends Controller
{
    public function __construct(protected TelegramService $telegram) {}

    public function handle(Request $request)
    {
        $data = $request->all();
        $message = $data['message'] ?? null;

        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'];
        $username = $message['from']['username'] ?? null;
        $text = trim($message['text'] ?? '');
        $firstName = $message['from']['first_name'] ?? '';
        $lastName = $message['from']['last_name'] ?? '';
        $name = trim($firstName . ' ' . $lastName);

        if (str_starts_with($text, '/start')) {
            $user = User::where('telegram_username', $username)->first();

            if ($user) {
                $user->update(['telegram_id' => $chatId]);
                $this->telegram->sendMessage($chatId, "✅ *Hisobingiz ulandi!*\n\nEndi saytda «Telegram orqali kirish» tugmasini bosing.");
            } else {
                if ($username) {
                    User::updateOrCreate(
                        ['telegram_username' => $username],
                        [
                            'telegram_id' => $chatId,
                            'name' => $name ?: $username,
                            'email' => $username . '@telegram.uz',
                            'password' => bcrypt(Str::random(16)),
                        ]
                    );

                    $this->telegram->sendMessage($chatId, "👋 *Xush kelibsiz, {$name}!*\n\nSaytga kirish uchun saytdagi «Telegram orqali kirish» tugmasini bosing.");
                } else {
                    $this->telegram->sendMessage($chatId, "❌ Telegram username o'rnatilmagan.\n\nIltimos, Telegram sozlamalarida username qo'shing.");
                }
            }
        }

        return response()->json(['ok' => true]);
    }
}
