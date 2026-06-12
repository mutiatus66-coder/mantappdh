<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usulan extends Model
{
    protected $table = 'usulans';

    protected $fillable = [
        // Identitas
        'user_id', 'sub_event_id', 'bidang_id',
        // Halaman 1
        'judul', 'inovator', 'nama_inovasi', 'interaksi',
        'nama_tim', 'ketua_nama', 'ketua_email', 'ketua_wa',
        'alamat_ketua', 'ktp', 'kategori', 'asal_sekolah', 'nama_guru',
        // Halaman 2
        'latar_belakang', 'kondisi_sebelumnya', 'sasaran_tujuan',
        'materi_inovasi', 'deskripsi', 'bahan_baku', 'cara_kerja',
        'keunggulan', 'hasil_diharapkan', 'manfaat', 'rencana_berkelanjutan',
        // Halaman 3
        'file_surat_pernyataan', 'file_proposal', 'file_gambar', 'link_video',
        // Status
        'status', 'is_submitted',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────

    public function subEvent(): BelongsTo
    {
        return $this->belongsTo(SubEvent::class, 'sub_event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function anggotaTim(): HasMany
    {
        return $this->hasMany(AnggotaTim::class);
    }

    // ── Scope ────────────────────────────────────────────────────────────

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('is_submitted', true);
    }

    public function scopeForSubEvent(Builder $query, int $subEventId): Builder
    {
        return $query->where('sub_event_id', $subEventId);
    }

    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    // ── Helper ───────────────────────────────────────────────────────────

    public function bisaDinilai(): bool
    {
        return $this->is_submitted && $this->status === 'Sedang Dinilai';
    }
}
