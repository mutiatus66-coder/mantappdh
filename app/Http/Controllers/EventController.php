<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    private static array $data = [
        // bisa isi data awal di sini, atau biarkan kosong
    ];

    private function getData(): array
    {
        return session('events', self::$data);
    }

    private function saveData(array $data): void
    {
        session(['events' => array_values($data)]);
    }
    private static array $data = [
    ['id' => 1, 'nama_event' => 'LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)', 'jenis' => 'INOTEK'],
    ['id' => 2, 'nama_event' => 'INOVASI DAERAH KAB. MAGETAN',                'jenis' => 'INODA'],
    ['id' => 3, 'nama_event' => 'KOMPETISI INOVASI DIGITAL',                  'jenis' => 'INOTEK'],
];

    public function index()
    {
        $events = $this->getData();
        return view('master.event', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $data  = $this->getData();
        $maxId = count($data) ? max(array_column($data, 'id')) : 0;

        $data[] = [
            'id'         => $maxId + 1,
            'nama_event' => $request->nama_event,
            'jenis'      => $request->jenis,
        ];

        $this->saveData($data);
        return redirect()->back()->with('success', 'Event berhasil ditambahkan!');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        $data = $this->getData();
        foreach ($data as &$row) {
            if ($row['id'] === $id) {
                $row['nama_event'] = $request->nama_event;
                $row['jenis']      = $request->jenis;
                break;
            }
        }

        $this->saveData($data);
        return redirect()->back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $data = array_filter($this->getData(), fn($r) => $r['id'] !== $id);
        $this->saveData($data);
        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }
}