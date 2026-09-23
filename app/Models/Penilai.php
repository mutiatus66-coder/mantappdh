<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilai extends Model
{
    protected $table = 'penilai';
    protected $fillable = ['nama', 'email', 'sub_event_id', 'user_id'];

    public function subEvent()
    {
        return $this->belongsTo(SubEvent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
