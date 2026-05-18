<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeteranganTahap2 extends Model
{
    protected $table = 'keterangan_tahap2';

    protected $fillable = [
        'indikator_tahap2_id',
        'keterangan',
        'nilai_minimal',
        'nilai_maksimal',
    ];

    public function indikator()
    {
        return $this->belongsTo(IndikatorTahap2::class, 'indikator_tahap2_id');
    }
}