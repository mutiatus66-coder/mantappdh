@extends('index', ['dummy' => true])

@section('content')
<div class="penilai-container">
    <div class="penilai-header">
        <div class="penilai-title">
            <h3>Master Penilai</h3>
            <p>Kelola data penilai</p>
        </div>
        <button class="btn btn-primary" id="btnTambahPenilai">Tambah Penilai</button>
    </div>

    <div class="penilai-stats">
        <div class="total-badge">Total Penilai: <span id="totalPenilai">{{ count($penilai) }}</span></div>
        <div class="search-box">
            <input type="text" id="searchPenilai" placeholder="Cari nama atau email...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="penilai-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Penilai</th>
                    <th>Email</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPenilaiBody">
                @forelse($penilai as $index => $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p['nama'] }}</td>
                    <td>{{ $p['email'] }}</td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-edit-penilai" data-id="{{ $p['id'] }}" data-nama="{{ $p['nama'] }}" data-email="{{ $p['email'] }}">Ubah</button>
                            <button class="btn btn-danger btn-hapus-penilai" data-id="{{ $p['id'] }}" data-nama="{{ $p['nama'] }}" data-url="{{ route('penilai.destroy', $p['id']) }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-row">Belum ada data penilai</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">Tidak ada data yang cocok</div>
    </div>
</div>
@endsection
