<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Replacement extends Model
{
    protected $fillable = [
        'heater_id',
        'old_heater_code',
        'new_heater_code',
        'reason',
        'replaced_by',
        'replacement_date',
        'notes'
    ];

    protected $casts = [
        'replacement_date' => 'datetime'
    ];

    public function heater(): BelongsTo
    {
        return $this->belongsTo(Heater::class);
    }
}
