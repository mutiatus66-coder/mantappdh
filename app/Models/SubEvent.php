<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SubEvent extends Model
{
    protected $table = 'sub_events';

    protected $fillable = [
        'event_id',
        'tahun',
        'sub_event',
        'kategori',
        'mulai',
        'berakhir',
    ];

    // Relasi ke Event (Induk)
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Relasi ke Bidang
    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class);
    }

    // Relasi ke Indikator Tahap 1
    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class);
    }

    // Relasi ke Indikator Tahap 2
    public function indikatorTahap2(): HasMany
    {
        return $this->hasMany(IndikatorTahap2::class);
    }

    // Relasi ke Formulasi Tahap 1
    public function formulasiTahap1(): HasOne
    {
        return $this->hasOne(FormulasiTahap1::class);
    }

    // Relasi ke Formulasi Tahap 2
    public function formulasiTahap2(): HasOne
    {
        return $this->hasOne(FormulasiTahap2::class);
    }

    // ═══════ RELASI ONE-TO-MANY KE PENILAI ═══════
    // Satu Sub Event bisa memiliki banyak Penilai
    public function penilai(): HasMany
    {
        return $this->hasMany(Penilai::class);
    }
}
