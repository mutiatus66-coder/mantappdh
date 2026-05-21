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
    ['id' => 1, 'event_id' => null, 'event' => '-', 'tahun' => 2022, 'sub_event' => 'LOMBA INOTEK 2022',                              'kategori' => null, 'mulai' => '2022-01-01', 'berakhir' => '2022-12-31'],
    ['id' => 2, 'event_id' => null, 'event' => '-', 'tahun' => 2023, 'sub_event' => 'LOMBA INOTEK (INOTEK AWARD) 2023',               'kategori' => null, 'mulai' => '2023-01-01', 'berakhir' => '2023-12-31'],
    ['id' => 3, 'event_id' => null, 'event' => '-', 'tahun' => 2023, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2023',                  'kategori' => null, 'mulai' => '2023-01-01', 'berakhir' => '2023-12-31'],
    ['id' => 4, 'event_id' => null, 'event' => '-', 'tahun' => 2024, 'sub_event' => 'LOMBA INOVASI DAN TEKNOLOGI 2024',               'kategori' => null, 'mulai' => '2024-01-01', 'berakhir' => '2024-12-31'],
    ['id' => 5, 'event_id' => null, 'event' => '-', 'tahun' => 2024, 'sub_event' => 'PELAPORAN INOVASI DAERAH 2024 & INODA AWARD 2025', 'kategori' => null, 'mulai' => '2024-01-01', 'berakhir' => '2025-12-31'],
    ['id' => 6, 'event_id' => null, 'event' => '-', 'tahun' => 2025, 'sub_event' => 'PAMERAN INOTEK 2025',                            'kategori' => null, 'mulai' => '2025-01-01', 'berakhir' => '2025-12-31'],
    ['id' => 7, 'event_id' => null, 'event' => '-', 'tahun' => 2025, 'sub_event' => 'KOMPETISI INOVASI DIGITAL 2025',                 'kategori' => null, 'mulai' => '2025-01-01', 'berakhir' => '2025-12-31'],
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