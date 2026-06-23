<?php
// app/Models/RankingTahap2.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankingTahap2 extends Model
{
    protected $table = 'ranking_tahap2';

    protected $fillable = [
        'usulan_id',
        'penilai_id',
        'ranking',
    ];

    public function usulan()
    {
        return $this->belongsTo(Usulan::class);
    }

    public function penilai()
    {
        return $this->belongsTo(Penilai::class);
    }
}