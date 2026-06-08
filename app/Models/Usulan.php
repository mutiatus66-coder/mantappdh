<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usulan extends Model
{
    protected $table = 'usulans';

    protected $fillable = [
        'user_id',
        'sub_event_id',
        'judul',
        'inovator',
        'nama_inovasi',
        'nama_tim',
        'ketua_nama',
        'ketua_email',
        'ketua_wa',
        'status',
        'kategori',
    ];

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class, 'sub_event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}