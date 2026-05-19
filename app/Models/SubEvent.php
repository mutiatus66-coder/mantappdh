<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

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

    private static array $staticData = [
        ['id' => 1, 'sub_event' => 'LOMBA INOTEK 2022'],
        ['id' => 2, 'sub_event' => 'LOMBA INOTEK (INOTEK AWARD) 2023'],
        ['id' => 3, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2023'],
        ['id' => 4, 'sub_event' => 'LOMBA INOVASI DAN TEKNOLOGI 2024'],
        ['id' => 5, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025'],
        ['id' => 6, 'sub_event' => 'PAMERAN INOTEK 2025'],
        ['id' => 7, 'sub_event' => 'KOMPETISI INOVASI DIGITAL 2025'],
    ];

    public static function getStaticData(): array
    {
        return self::$staticData;
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}