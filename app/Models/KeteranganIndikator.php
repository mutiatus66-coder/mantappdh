<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeteranganIndikator extends Model
{
    protected $table = 'keterangan_indikators';

    protected $fillable = [
        'indikator_id',
        'keterangan',
        'nilai_minimal',
        'nilai_maksimal',
    ];

    // Relasi ke tabel indikator
    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }
}