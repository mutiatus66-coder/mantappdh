<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\SubEvent;

class SubEventController extends Controller
{
    public function index()
    {
        $subEvents = SubEvent::with('event')->orderBy('tahun', 'desc')->get();
        $events    = Event::all();
        return view('master.sub-event', compact('subEvents', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'  => 'required|integer|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $subEvent = SubEvent::create([
            'event_id'  => $request->event_id,
            'tahun'     => $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        $subEvent->load('event');

        return response()->json([
            'success'   => true,
            'subEvent'  => array_merge($subEvent->toArray(), [
                'event_nama'  => $subEvent->event->nama_event ?? '-',
                'update_url'  => route('sub-event.update', $subEvent->id),
                'destroy_url' => route('sub-event.destroy', $subEvent->id),
            ]),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'event_id'  => 'required|integer|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ]);

        $subEvent = SubEvent::findOrFail($id);
        $subEvent->update([
            'event_id'  => $request->event_id,
            'tahun'     => $request->tahun,
            'sub_event' => $request->sub_event,
            'kategori'  => $request->kategori ?? 'SEMUA BIDANG',
            'mulai'     => $request->mulai,
            'berakhir'  => $request->berakhir,
        ]);

        $subEvent->load('event');

        return response()->json([
            'success'    => true,
            'event_nama' => $subEvent->event->nama_event ?? '-',
        ]);
    }

    public function destroy(int $id)
    {
        SubEvent::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}