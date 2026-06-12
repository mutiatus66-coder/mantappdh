<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Usulan extends Model
{
    protected $table = 'usulans';

    protected $fillable = [
        'user_id',
        'sub_event_id',
        'judul',
        'inovator',
        'nama_inovasi',
        'nama_tim',
        'ketua_nama',
        'ketua_email',
        'ketua_wa',
        'status',
        'is_submitted',
        'kategori',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
    ];

    // ── Relasi ─────────────────────────────────────────────────────────────

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class, 'sub_event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scope ──────────────────────────────────────────────────────────────

    /** Hanya usulan yang sudah dikirim (sedang dinilai / selesai) */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('is_submitted', true);
    }

    /** Filter berdasarkan sub event */
    public function scopeForSubEvent(Builder $query, int $subEventId): Builder
    {
        return $query->where('sub_event_id', $subEventId);
    }

    /** Filter berdasarkan kategori (umum / pelajar) */
    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    // ── Helper ─────────────────────────────────────────────────────────────

    /** Cek apakah usulan sudah bisa dinilai */
    public function bisaDinilai(): bool
    {
        return $this->is_submitted && $this->status === 'Sedang Dinilai';
    }
}