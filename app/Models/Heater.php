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

        $localTime = $this->last_received_at;
        if ($localTime instanceof \Carbon\Carbon) {
            $localTime = $localTime->setTimezone('Asia/Jakarta');
        } elseif (is_string($localTime)) {
            $localTime = \Carbon\Carbon::parse($localTime)->setTimezone('Asia/Jakarta');
        }

        return (object)[
            'current' => $this->last_current,
            'status' => $this->last_status,
            'received_at' => $localTime ? $localTime->toIso8601String() : null,
            'received_at_formatted' => $localTime ? $localTime->format('d-m-Y H:i:s') : null,
            'time_only' => $localTime ? $localTime->format('H:i:s') : null,
            'date_only' => $localTime ? $localTime->translatedFormat('d F Y') : null,
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