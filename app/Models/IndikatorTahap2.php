<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorTahap2 extends Model
{
    protected $table = 'indikator_tahap2';

    protected $fillable = [
        'sub_event_id',
        'nama_indikator',
        'jenis',
    ];

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class);
    }

    public function keterangans()
    {
        return $this->hasMany(KeteranganTahap2::class, 'indikator_tahap2_id');
    }
}