<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramLog extends Model
{
    protected $fillable = [
        'heater_id',
        'message',
        'status',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    public function heater(): BelongsTo
    {
        return $this->belongsTo(Heater::class);
    }
}