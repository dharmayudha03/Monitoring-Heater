<?php

namespace App\Http\Controllers;

use App\Models\TelegramLog;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TelegramLogController extends Controller
{
    protected TelegramService $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function index()
    {
        $logs = TelegramLog::with('heater')->orderBy('sent_at', 'desc')->paginate(15);
        $latestChatId = $this->telegramService->fetchLatestChatId();

        return view('telegram.index', compact('logs', 'latestChatId'));
    }

    public function sendTest(Request $request)
    {
        $message = "🚨 <b>TEST NOTIFIKASI SYSTEM</b>\n\nSistem Monitoring Heater PT IRC INOAC Indonesia terhubung dengan Bot Telegram.\n\nWaktu Tes: " . now()->format('d-m-Y H:i:s') . "\nStatus: ONLINE & MONITORING";
        
        $chatId = $request->input('chat_id');
        $result = $this->telegramService->sendMessage($message, 1, $chatId);

        if ($result['success']) {
            return redirect()->route('telegram.index')->with('success', $result['message']);
        } else {
            return redirect()->route('telegram.index')->with('error', $result['message']);
        }
    }

    public function sendDangerReminders()
    {
        Artisan::call('heater:send-danger-reminders');
        return redirect()->route('telegram.index')->with('success', 'Reminder harian untuk Heater DANGER berhasil dikirim ke Grup Telegram!');
    }
}
