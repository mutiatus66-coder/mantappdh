<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemenang extends Model
{
    protected $table = 'pemenang';

    protected $fillable = [
        'usulan_id',
        'penilai_id',
        'keterangan_tahap2_id',
        'nilai',
    ];

    public function usulan()
    {
        return $this->belongsTo(Usulan::class, 'usulan_id');
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