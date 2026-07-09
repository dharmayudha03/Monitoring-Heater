<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'normal_min',
        'warning_min',
        'm_ct1',
        'm_ct2',
        'm_ct3',
        'm_ct4',
        'm_ct5',
        'm_ct6',
        'upper_baseline',
        'lower_baseline',
        'telegram_enabled',
        'sampling_interval'
    ];

    protected $casts = [
        'telegram_enabled' => 'boolean'
    ];
}