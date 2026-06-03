<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::latest('created_at')->get();
        return view('master.pengumuman', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Published,Draft',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = $request->only('judul', 'deskripsi', 'status');

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('pengumuman', 'public');
            $data['file_path'] = $path;
        }

        Pengumuman::create($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Published,Draft',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = $request->only('judul', 'deskripsi', 'status');

        if ($request->hasFile('file')) {
            if ($pengumuman->file_path) {
                Storage::disk('public')->delete($pengumuman->file_path);
            }
            $path = $request->file('file')->store('pengumuman', 'public');
            $data['file_path'] = $path;
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diupdate.');
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        if ($pengumuman->file_path) {
            Storage::disk('public')->delete($pengumuman->file_path);
        }
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
