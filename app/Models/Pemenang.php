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
        return $this->belongsTo(Usulan::class);
    }

    /**
     * FIX: penilai_id menyimpan Penilai.id (bukan User.id), konsisten dengan
     * PenilaianController & FK migration (constrained('penilai')).
     */
    public function penilai()
    {
        return $this->belongsTo(Penilai::class, 'penilai_id');
    }

    public function keteranganTahap2()
    {
        return $this->belongsTo(KeteranganTahap2::class, 'keterangan_tahap2_id');
    }
}
