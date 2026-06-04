<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bidangs()
    {
        return $this->hasMany(Bidang::class);
    }

    public function indikators()
    {
        return $this->hasMany(Indikator::class);
    }

    public function indikatorTahap2()
    {
        return $this->hasMany(IndikatorTahap2::class);
    }

    public function formulasiTahap1()
    {
        return $this->hasOne(FormulasiTahap1::class);
    }

    public function formulasiTahap2()
    {
        return $this->hasOne(FormulasiTahap2::class);
    }
}