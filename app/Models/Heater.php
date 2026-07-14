<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Heater extends Model
{
    protected $fillable = [
        'heater_code',
        'machine_name',
        'heater_name',
        'zone',
        'description',
        'is_active',
        'last_current',
        'last_status',
        'last_received_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_received_at' => 'datetime'
    ];

    protected $appends = ['latest_log'];

    public function getLatestLogAttribute()
    {
        if ($this->last_current === null) {
            return null;
        }

        return (object)[
            'current' => $this->last_current,
            'status' => $this->last_status,
            'received_at' => $this->last_received_at ? $this->last_received_at->toIso8601String() : null,
            'received_at_formatted' => $this->last_received_at ? $this->last_received_at->format('d-m-Y H:i:s') : null,
            'time_only' => $this->last_received_at ? $this->last_received_at->format('H:i:s') : null,
            'date_only' => $this->last_received_at ? $this->last_received_at->translatedFormat('d F Y') : null,
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HeaterLog::class);
    }

    public function latestLog()
    {
        return $this->hasOne(HeaterLog::class)
            ->latestOfMany('received_at');
    }

    public function telegramLogs(): HasMany
    {
        return $this->hasMany(TelegramLog::class);
    }

    public function replacements(): HasMany
    {
        return $this->hasMany(Replacement::class);
    }

    public function latestReplacement()
    {
        return $this->hasOne(Replacement::class)->latestOfMany('replacement_date');
    }


}