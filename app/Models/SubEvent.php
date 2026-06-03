<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubEvent extends Model
{
    protected $table = 'sub_events';

    protected $fillable = [
        'event_id', 'tahun', 'sub_event', 'kategori', 'mulai', 'berakhir',
    ];

    protected $casts = [
        'mulai'    => 'date',
        'berakhir' => 'date',
        'tahun'    => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class);
    }
}