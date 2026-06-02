@extends('index', ['dummy' => true])

@section('content')
<div class="pengumuman-container">
    <div class="pengumuman-header">
        <div class="pengumuman-title">
            <h3>Master Pengumuman</h3>
            <p>Kelola pengumuman yang ditampilkan ke publik</p>
        </div>
        <button class="btn btn-primary" id="btnTambahPengumuman">Tambah Pengumuman</button>
    </div>

    <div class="pengumuman-stats">
        <div class="total-badge">Total Pengumuman: <span id="totalPengumuman">{{ $pengumuman->count() }}</span></div>
        <div class="search-box">
            <input type="text" id="searchPengumuman" placeholder="Cari judul atau deskripsi...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="pengumuman-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>File</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPengumumanBody">
                @forelse($pengumuman as $index => $p)
                <tr>
                    <td>{{ $loop->iteration }}</td
                    <td>{{ $p->judul }}</td
                    <td>{{ Str::limit($p->deskripsi, 80) }}</td
                    <td><span class="status-badge {{ $p->status == 'Published' ? 'status-published' : 'status-draft' }}">{{ $p->status }}</span></td
                    <td>@if($p->file_path) <a href="{{ asset('storage/'.$p->file_path) }}" target="_blank">Lihat File</a> @else - @endif</td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-edit-pengumuman" data-id="{{ $p->id }}" data-judul="{{ $p->judul }}" data-deskripsi="{{ $p->deskripsi }}" data-status="{{ $p->status }}">Ubah</button>
                            <button class="btn btn-danger btn-hapus-pengumuman" data-id="{{ $p->id }}" data-judul="{{ $p->judul }}">Hapus</button>
                        </div>
                    </td
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-row">Belum ada pengumuman</td
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">Tidak ada data yang cocok</div>
    </div>
</div>
@endsection
