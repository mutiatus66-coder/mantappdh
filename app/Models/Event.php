<?php

namespace App\Models;

use App\Http\Controllers\SubEventController;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['nama_event', 'jenis'];
    public function subEvents()
        {
            return $this->hasMany(SubEventController::class, 'event', 'nama_event');
        }
}