<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'normal_min',
        'warning_min',
        'telegram_enabled',
        'sampling_interval'
    ];

    protected $casts = [
        'telegram_enabled' => 'boolean'
    ];
}