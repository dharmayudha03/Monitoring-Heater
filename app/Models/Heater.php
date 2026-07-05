<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Heater extends Model
{
    protected $fillable = [
        'heater_code',
        'heater_name',
        'zone',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

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