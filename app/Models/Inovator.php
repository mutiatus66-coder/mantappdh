<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inovator extends Model
{
    protected $table = 'inovator';

    protected $fillable = [
        'sub_event_id',
        'inovator',
        'nama_inovasi',
        'kategori',
    ];

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class, 'sub_event_id');
    }
}