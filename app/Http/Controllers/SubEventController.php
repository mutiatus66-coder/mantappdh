<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\SubEvent;

class SubEventController extends Controller
{
    private function getEvents()
    {
        return Event::query()->orderBy('nama_event', 'asc')->get();
    }

    private function validationRules(): array
    {
        return [
            'event_id'  => 'required|integer|exists:events,id',
            'tahun'     => 'required|digits:4',
            'sub_event' => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:100',
            'mulai'     => 'required|date',
            'berakhir'  => 'required|date|after_or_equal:mulai',
        ];
    }

    public function index()
    {
        return view('master.sub-event', [
            'subEvents' => SubEvent::with('event')->orderBy('tahun', 'desc')->get(),
            'events'    => $this->getEvents(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->validationRules());

        SubEvent::create($request->only([
            'event_id', 'tahun', 'sub_event', 'kategori', 'mulai', 'berakhir',
        ]));

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return response()->json(SubEvent::with('event')->findOrFail($id));
    }

    public function update(Request $request, int $id)
    {
        $request->validate($this->validationRules());

        SubEvent::findOrFail($id)->update($request->only([
            'event_id', 'tahun', 'sub_event', 'kategori', 'mulai', 'berakhir',
        ]));

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        SubEvent::findOrFail($id)->delete();

        return redirect()->route('sub-event.index')->with('success', 'Sub Event berhasil dihapus.');
    }
}