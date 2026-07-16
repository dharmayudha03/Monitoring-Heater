<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeaterLog extends Model
{
    protected $fillable = [
        'heater_id',
        'firebase_key',
        'adc_value',
        'current',
        'voltage',
        'temperature',
        'status',
        'received_at',
        'zone'
    ];

    protected $casts = [
        'received_at' => 'datetime'
    ];

    public function getReceivedAtAttribute($value)
    {
        if (!$value) {
            return null;
        }
        $dateStr = $value instanceof \Carbon\Carbon ? $value->format('Y-m-d H:i:s') : (string)$value;
        return \Carbon\Carbon::parse($dateStr, 'Asia/Jakarta');
    }

    public function heater(): BelongsTo
    {
        return $this->belongsTo(Heater::class);
    }
}