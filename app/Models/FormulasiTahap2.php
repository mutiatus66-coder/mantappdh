<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulasiTahap2 extends Model
{
    protected $table = 'formulasi_tahap2';

    protected $fillable = [
        'sub_event_id',
        'nilai_inovasi',
        'nilai_peragaan',
    ];

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class, 'sub_event_id');
    }
}