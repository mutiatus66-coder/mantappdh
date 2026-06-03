<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int    $id
 * @property string $nama
 * @property string $name
 * @property string $email
 * @property string $hak_akses
 * @property string $status
 * @property string $password
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'name',
        'email',
        'hak_akses',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    public function isAdmin(): bool
    {
        return $this->hak_akses === 'admin';
    }

    public function isUser(): bool
    {
        return $this->hak_akses === 'user';
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->hak_akses, (array) $roles);
    }
}