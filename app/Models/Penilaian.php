<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianUsulan extends Model
{
    protected $table = 'penilaian_usulan';

    protected $fillable = [
        'inovator_id',
        'penilai_id',
        'keterangan_indikator_id',
        'nilai',
    ];

    public function inovator()
    {
        return $this->belongsTo(Inovator::class);
    }

    public function penilai()
    {
        return $this->belongsTo(Penilai::class);
    }

    public function keteranganIndikator()
    {
        return $this->belongsTo(KeteranganIndikator::class);
    }
}