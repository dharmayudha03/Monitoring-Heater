<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'plain_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isEngineering(): bool
    {
        return in_array($this->role, ['admin', 'engineering', 'super_admin']);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'engineering', 'super_admin']);
    }

    public function isOperator(): bool
    {
        return $this->role === 'user' || $this->role === 'operator';
    }
}
