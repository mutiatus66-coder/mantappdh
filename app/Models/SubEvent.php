<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class);
    }

    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class);
    }

    public function indikatorTahap2(): HasMany
    {
        return $this->hasMany(IndikatorTahap2::class);
    }

    public function formulasiTahap1(): HasOne
    {
        return $this->hasOne(FormulasiTahap1::class);
    }

    public function formulasiTahap2(): HasOne
    {
        return $this->hasOne(FormulasiTahap2::class);
    }

    public function penilai(): HasMany
    {
        return $this->hasMany(Penilai::class);
    }
}