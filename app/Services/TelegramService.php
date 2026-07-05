<?php

namespace App\Services;

use App\Models\TelegramLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TelegramService
{
    protected string $token;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN', '8955298990:AAFxpUNcSDmoFNrEAM8nvJN-VcV2ihE00uo');
    }

    public function sendMessage(string $message, ?int $heaterId = null, ?string $chatId = null): array
    {
        $targetChatId = $chatId ?? env('TELEGRAM_CHAT_ID') ?? $this->fetchLatestChatId();

        if (!$targetChatId) {
            $log = TelegramLog::create([
                'heater_id' => $heaterId ?? 1,
                'message' => $message,
                'status' => 'FAILED',
                'sent_at' => Carbon::now(),
            ]);

            return [
                'success' => false,
                'message' => 'Chat ID Telegram (Grup) belum terdeteksi.'
            ];
        }

        try {
            $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
            $response = Http::post($url, [
                'chat_id' => $targetChatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            // Handle Telegram migration parameters (group -> supergroup migration)
            if (!$response->successful() && isset($response->json()['parameters']['migrate_to_chat_id'])) {
                $targetChatId = (string) $response->json()['parameters']['migrate_to_chat_id'];
                $response = Http::post($url, [
                    'chat_id' => $targetChatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);
            }

            $status = $response->successful() ? 'SUCCESS' : 'FAILED';

            TelegramLog::create([
                'heater_id' => $heaterId ?? 1,
                'message' => $message,
                'status' => $status,
                'sent_at' => Carbon::now(),
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => "Notifikasi Telegram berhasil terkirim ke Grup! (ID: {$targetChatId})"];
            } else {
                return ['success' => false, 'message' => 'Gagal mengirim notifikasi Telegram: ' . $response->body()];
            }

        } catch (\Exception $e) {
            Log::error('Telegram API Error: ' . $e->getMessage());

            TelegramLog::create([
                'heater_id' => $heaterId ?? 1,
                'message' => $message,
                'status' => 'FAILED',
                'sent_at' => Carbon::now(),
            ]);

            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function fetchLatestChatId(): ?string
    {
        if (env('TELEGRAM_CHAT_ID')) {
            return env('TELEGRAM_CHAT_ID');
        }

        try {
            $url = "https://api.telegram.org/bot{$this->token}/getUpdates";
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['result'])) {
                    foreach (array_reverse($data['result']) as $update) {
                        // Check for migrate_to_chat_id
                        if (isset($update['message']['migrate_to_chat_id'])) {
                            return (string) $update['message']['migrate_to_chat_id'];
                        }
                        if (isset($update['message']['chat']['id'])) {
                            return (string) $update['message']['chat']['id'];
                        }
                        if (isset($update['my_chat_member']['chat']['id'])) {
                            return (string) $update['my_chat_member']['chat']['id'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Telegram getUpdates Error: ' . $e->getMessage());
        }

        return '-1003969013563';
    }
}