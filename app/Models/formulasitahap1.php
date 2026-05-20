<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulasiTahap1 extends Model
{
    protected $table = 'formulasi_tahap1';

    protected $fillable = [
        'sub_event_id',
        'nilai_makalah',
        'nilai_substansi',
    ];

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class);
    }
}