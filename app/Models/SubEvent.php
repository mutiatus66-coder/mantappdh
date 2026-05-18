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

    protected $casts = [
        'mulai'    => 'date',
        'berakhir' => 'date',
        'tahun'    => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}