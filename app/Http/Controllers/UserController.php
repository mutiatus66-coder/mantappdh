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
class UserController extends Authenticatable
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

    // ─── Role Checks ───────────────────────────────────────────

    public function isAdminBapperida(): bool
    {
        return $this->hak_akses === 'admin_bapperida';
    }

    public function isAdminKecamatan(): bool
    {
        return $this->hak_akses === 'admin_kecamatan';
    }

    public function isAdminOpd(): bool
    {
        return $this->hak_akses === 'admin_opd';
    }

    public function isPenilai(): bool
    {
        return $this->hak_akses === 'penilai';
    }

    public function isPeserta(): bool
    {
        return $this->hak_akses === 'peserta';
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->hak_akses, (array) $roles);
    }

    public function isAnyAdmin(): bool
    {
        return in_array($this->hak_akses, [
            'admin_bapperida',
            'admin_kecamatan',
            'admin_opd',
        ]);
    }
}