<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bidang extends Model
{
    
    protected $table = 'bidangs';

    protected $fillable = ['sub_event_id', 'nama', 'status'];

    public function subEvent(): BelongsTo
    {
        return $this->belongsTo(SubEvent::class);
        
    }
    
}