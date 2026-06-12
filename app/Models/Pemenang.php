<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianPemenang extends Model
{
    protected $table = 'penilaian_pemenang';

    protected $fillable = [
        'inovator_id',
        'penilai_id',
        'keterangan_tahap2_id',
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

    public function keteranganTahap2()
    {
        return $this->belongsTo(KeteranganTahap2::class);
    }
}