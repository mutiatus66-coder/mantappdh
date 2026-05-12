<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Admin extends Controller
{
    private static array $data = [
        ['id'=>1,'tahun'=>2022,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOTEK 2022','kategori'=>'SEMUA BIDANG','mulai'=>'2022-08-12','berakhir'=>'2022-10-03'],
        ['id'=>2,'tahun'=>2023,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOTEK (INOTEK AWARD) 2023','kategori'=>'SEMUA BIDANG','mulai'=>'2023-03-15','berakhir'=>'2023-07-23'],
        ['id'=>3,'tahun'=>2023,'event'=>'INOVASI DAERAH KAB. MAGETAN','sub_event'=>'PELAPORAN INOVASI DAERAH 2023','kategori'=>'SEMUA BIDANG','mulai'=>'2023-09-09','berakhir'=>'2023-12-20'],
        ['id'=>4,'tahun'=>2024,'event'=>'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)','sub_event'=>'LOMBA INOVASI DAN TEKNOLOGI 2024','kategori'=>'SEMUA','mulai'=>'2024-04-01','berakhir'=>'2025-03-31'],
    ];
    private function getData(): array
    {
        return session('sub_events', self::$data);
    }

    private function saveData(array $data): void
    {
        session(['sub_events' => array_values($data)]);
    }
    private static array $bidang = [
    1 => [
        [
            'id' => 1,
            'nama' => 'Teknologi Informasi',
            'status' => 'aktif'
        ],
        [
            'id' => 2,
            'nama' => 'Pertanian',
            'status' => 'tidak_aktif'
        ],
    ],

    2 => [
        [
            'id' => 3,
            'nama' => 'Kesehatan',
            'status' => 'aktif'
        ],
    ],
];
private static array $penilai = [
    ['id' => 1, 'nama' => 'Muhammad Fauzi',  'nama_singkat' => 'muhammad'],
    ['id' => 2, 'nama' => 'Mujiono',          'nama_singkat' => 'Mujiono'],
    ['id' => 3, 'nama' => 'Moch. Hasan',      'nama_singkat' => 'Moch.'],
    ['id' => 4, 'nama' => 'Jatmiko Wibowo',   'nama_singkat' => 'Jatmiko'],
    ['id' => 5, 'nama' => 'Andi Prasetyo',    'nama_singkat' => 'Andi'],
    ['id' => 6, 'nama' => 'Joko Susanto',     'nama_singkat' => 'Joko'],
    ['id' => 7, 'nama' => 'Heru Prasetyo',    'nama_singkat' => 'Heru'],
];

private static array $nominasi = [
    4 => [
        [
            'id'           => 1,
            'inovator'     => 'RSUD dr. Sayidiman Magetan',
            'nama_inovasi' => 'JERIGEN BEKAS JADI SAFETY BOX "RIKA D\'BOX"',
            'kategori'     => 'umum',
            'rangking'     => null,
            'total_nilai'  => 430.8,
            'nilai'        => [1 => null, 2 => 72.4, 3 => 65.8, 4 => 69.8, 5 => 70.0, 6 => 72.4, 7 => 80.4],
        ],
        [
            'id'           => 2,
            'inovator'     => 'Puskesmas Karangrejo',
            'nama_inovasi' => 'INOVASI DETEKSI DINI STUNTING BERBASIS DIGITAL',
            'kategori'     => 'umum',
            'rangking'     => null,
            'total_nilai'  => 0,
            'nilai'        => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null],
        ],
        [
            'id'           => 3,
            'inovator'     => 'Widhi Rahman Hardani',
            'nama_inovasi' => 'Inovasi Apartemen Lele Vertikal dengan Sistem Pemberian Pakan Otomatis',
            'kategori'     => 'pelajar',
            'rangking'     => null,
            'total_nilai'  => 406.8,
            'nilai'        => [1 => null, 2 => 69.0, 3 => 72.8, 4 => 59.0, 5 => 73.6, 6 => 64.0, 7 => 68.4],
        ],
        [
            'id'           => 4,
            'inovator'     => 'SMA Negeri 1 Magetan',
            'nama_inovasi' => 'Robot Pemilah Sampah Otomatis Berbasis AI',
            'kategori'     => 'pelajar',
            'rangking'     => null,
            'total_nilai'  => 0,
            'nilai'        => [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null],
        ],
    ],
];

public function penilaianTahap2()
{
    $subEvents    = $this->getData();

    $nominasiData = [];
    foreach ($subEvents as $se) {
        $nominasiData[$se['id']] = self::$nominasi[$se['id']] ?? [];
    }
    return view('master.penilaian.tahap2.index', compact('subEvents', 'nominasiData'));
}
public function penilaianTahap2Show(int $id)
{
    $subEvent = collect($this->getData())->firstWhere('id', $id);
    abort_unless($subEvent, 404);

    $allNominasi    = self::$nominasi[$id] ?? [];
    $nominasiUmum   = array_values(array_filter($allNominasi, fn($n) => $n['kategori'] === 'umum'));
    $nominasiPelajar= array_values(array_filter($allNominasi, fn($n) => $n['kategori'] === 'pelajar'));
    $penilai        = self::$penilai;

    return view('master.penilaian.tahap2.show', compact(
        'subEvent',
        'nominasiUmum',
        'nominasiPelajar',
        'penilai'
    ));
}

    public function bidang()
{
    $subEvents = $this->getData();

    $bidangData = self::$bidang;

    return view('master.bidang', compact(
        'subEvents',
        'bidangData'
    ));
}
    public function index()
    {
        $subEvents = $this->getData();
        $events = [
            'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)',
            'INOVASI DAERAH KAB. MAGETAN',
            'KOMPETISI INOVASI DIGITAL',
        ];
        return view('master.sub-event', compact('subEvents', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event'      => 'required|string',
            'tahun'      => 'required|digits:4',
            'sub_event'  => 'required|string|max:255',
            'mulai'      => 'required|date',
            'berakhir'   => 'required|date|after_or_equal:mulai',
        ]);

        $data = $this->getData();
        $maxId = count($data) ? max(array_column($data, 'id')) : 0;

        $data[] = [
            'id'       => $maxId + 1,
            'tahun'    => (int) $request->tahun,
            'event'    => $request->event,
            'sub_event'=> $request->sub_event,
            'kategori' => $request->kategori ?? '',
            'mulai'    => $request->mulai,
            'berakhir' => $request->berakhir,
        ];

        $this->saveData($data);
        return redirect()->route('admin.sub-event.index')->with('success', 'Sub Event berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = collect($this->getData())->firstWhere('id', $id);
        abort_unless($item, 404);
        return response()->json($item);
    }
    public function update(Request $request, int $id)
    {
        $request->validate([
            'event'     => 'required|string',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $data = $this->getData();
        foreach ($data as &$row) {
            if ($row['id'] === $id) {
                $row = array_merge($row, [
                    'tahun'    => (int) $request->tahun,
                    'event'    => $request->event,
                    'sub_event'=> $request->sub_event,
                    'kategori' => $request->kategori ?? '',
                    'mulai'    => $request->mulai,
                    'berakhir' => $request->berakhir,
                ]);
                break;
            }
        }

        $this->saveData($data);
        return redirect()->route('admin.sub-event.index')->with('success', 'Sub Event berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $data = array_filter($this->getData(), fn($r) => $r['id'] !== $id);
        $this->saveData($data);
        return redirect()->route('admin.sub-event.index')->with('success', 'Sub Event berhasil dihapus.');
    }
    public function penilaianTahap1()
{
    $subEvents    = $this->getData();

    $nominasiData = [];
    foreach ($subEvents as $se) {
        $nominasiData[$se['id']] = self::$nominasi[$se['id']] ?? [];
    }

    return view('master.penilaian.tahap1.index', compact('subEvents', 'nominasiData'));
}

public function penilaianTahap1Show(int $id)
{
    $subEvent = collect($this->getData())->firstWhere('id', $id);
    abort_unless($subEvent, 404);

    $allNominasi     = self::$nominasi[$id] ?? [];
    $nominasiUmum    = array_values(array_filter($allNominasi, fn($n) => $n['kategori'] === 'umum'));
    $nominasiPelajar = array_values(array_filter($allNominasi, fn($n) => $n['kategori'] === 'pelajar'));
    $penilai         = self::$penilai;

    return view('master.penilaian.tahap1.show', compact(
        'subEvent',
        'nominasiUmum',
        'nominasiPelajar',
        'penilai'
    ));
}
}
