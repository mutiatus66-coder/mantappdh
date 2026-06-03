<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = ['nama_event', 'jenis'];

    public function subEvents(): HasMany
    {
        return $this->hasMany(SubEvent::class);
    }
}