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
}