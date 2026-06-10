<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return view('master.event', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        // Cek duplikat
        $exists = Event::whereRaw('LOWER(nama_event) = ?', [strtolower($request->nama_event)])
            ->where('jenis', $request->jenis)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Event dengan nama dan jenis yang sama sudah ada.',
            ]);
        }

        $event = Event::create([
            'nama_event' => $request->nama_event,
            'jenis'      => $request->jenis,
        ]);

        return response()->json([
            'success' => true,
            'event'   => array_merge($event->toArray(), [
                'update_url'  => route('event.update', $event->id),
                'destroy_url' => route('event.destroy', $event->id),
            ]),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'nama_event' => 'required|string|max:255',
            'jenis'      => 'required|in:INOTEK,INODA',
        ]);

        // Cek duplikat (exclude diri sendiri)
        $exists = Event::whereRaw('LOWER(nama_event) = ?', [strtolower($request->nama_event)])
            ->where('jenis', $request->jenis)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Event dengan nama dan jenis yang sama sudah ada.',
            ]);
        }

        $event->update([
            'nama_event' => $request->nama_event,
            'jenis'      => $request->jenis,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(int $id)
    {
        Event::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}