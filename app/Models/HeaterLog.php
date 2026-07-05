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

    public function heater(): BelongsTo
    {
        return $this->belongsTo(Heater::class);
    }
}