<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirebaseLog extends Model
{
    protected $fillable = [
        'firebase_key',
        'payload',
        'received_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime'
    ];
}