<?php
namespace App\Http\Controllers;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    private function authorizeAdmin(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdminBapperida()) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->authorizeAdmin();
        $pengumuman = Pengumuman::latest('created_at')->get();
        return view('master.pengumuman', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'required|in:Published,Draft',
            'file'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $data = $request->only('judul', 'deskripsi', 'status');
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('pengumuman', 'public');
        }

        $pengumuman = Pengumuman::create($data);

        return response()->json([
            'success'    => true,
            'pengumuman' => array_merge($pengumuman->toArray(), [
                'file_url'    => $pengumuman->file_path
                                    ? asset('storage/' . $pengumuman->file_path)
                                    : null,
                'update_url'  => route('pengumuman.update', $pengumuman->id),
                'destroy_url' => route('pengumuman.destroy', $pengumuman->id),
            ]),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status'    => 'required|in:Published,Draft',
            'file'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $data = $request->only('judul', 'deskripsi', 'status');
        if ($request->hasFile('file')) {
            if ($pengumuman->file_path) {
                Storage::disk('public')->delete($pengumuman->file_path);
            }
            $data['file_path'] = $request->file('file')->store('pengumuman', 'public');
        }

        $pengumuman->update($data);
        $pengumuman->refresh();

        return response()->json([
            'success'  => true,
            'file_url' => $pengumuman->file_path
                            ? asset('storage/' . $pengumuman->file_path)
                            : null,
        ]);
    }

    public function destroy($id)
    {
        $this->authorizeAdmin();
        $pengumuman = Pengumuman::findOrFail($id);
        if ($pengumuman->file_path) {
            Storage::disk('public')->delete($pengumuman->file_path);
        }
        $pengumuman->delete();
        return response()->json(['success' => true]);
    }
}