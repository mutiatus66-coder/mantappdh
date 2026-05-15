<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $table = 'indikators';

    protected $fillable = [
        'sub_event_id',
        'nama_indikator',
    ];

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class, 'sub_event_id');
    }
}