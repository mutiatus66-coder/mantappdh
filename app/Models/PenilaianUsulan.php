<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianUsulan extends Model
{
    protected $table = 'penilaian_usulan';

    protected $fillable = [
        'usulan_id',
        'penilai_id',
        'keterangan_indikator_id',
        'nilai',
    ];

    public function usulan()
    {
        return $this->belongsTo(Usulan::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function keteranganIndikator()
    {
        return $this->belongsTo(KeteranganIndikator::class);
    }
}