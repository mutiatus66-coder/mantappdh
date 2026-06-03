<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilai extends Model
{
    protected $table = 'penilais';

    protected $fillable = [
        'nama',
        'email',
    ];
}   