<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilai extends Model
{
    protected $table = 'penilai';
    protected $fillable = ['nama', 'email', 'sub_event_id', 'user_id'];

    public function subEvent(): BelongsTo
    {
        return $this->belongsTo(SubEvent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}