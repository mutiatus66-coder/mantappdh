<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanPenilai extends Model
{
    protected $table = 'catatan_penilai';

    protected $fillable = ['usulan_id', 'penilai_id', 'catatan'];

    public function usulan(): BelongsTo
    {
        return $this->belongsTo(Usulan::class);
    }

    public function penilai(): BelongsTo
{
    return $this->belongsTo(Penilai::class, 'penilai_id');
}
}