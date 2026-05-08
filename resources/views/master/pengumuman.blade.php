@extends('index', ['dummy' => true])

@section('content')
<style>
    .pengumuman-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
    }
    .pengumuman-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .pengumuman-title h3 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
        color: #1f2937;
    }
    .pengumuman-title p {
        margin: 0;
        color: #6b7280;
        font-size: 0.875rem;
    }
    .btn-tambah {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-tambah:hover { opacity: 0.9; }
    .stats-search {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .total-badge {
        background: #dcfce7;
        color: #166534;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .search-box input {
        padding: 6px 12px 6px 32px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        width: 240px;
    }
    .pengumuman-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }
    .pengumuman-table th {
        background: #f9fafb;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #4b5563;
        border-bottom: 1px solid #e5e7eb;
    }
    .pengumuman-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #1f2937;
        font-size: 0.875rem;
    }
    .pengumuman-table tr:hover {
        background: #fefce8;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-published {
        background: #dcfce7;
        color: #166534;
    }
    .status-draft {
        background: #fef9c3;
        color: #854d0e;
    }
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
        padding: 4px;
    }
    .btn-edit { color: #f59e0b; }
    .btn-hapus { color: #ef4444; }
    .btn-hapus:hover, .btn-edit:hover { opacity: 0.7; }
    .empty-row {
        text-align: center;
        padding: 30px;
        color: #9ca3af;
    }
    @media (max-width: 640px) {
        .pengumuman-container { margin: 10px; padding: 12px; }
        .pengumuman-table th, .pengumuman-table td { padding: 8px; }
        .search-box input { width: 100%; }
    }
</style>

<div class="pengumuman-container">
    <div class="pengumuman-header">
        <div class="pengumuman-title">
            <h3>Master Pengumuman</h3>
            <p>Kelola pengumuman yang ditampilkan ke publik</p>
        </div>
        <button class="btn-tambah" id="btnTambahPengumuman">+ Tambah Pengumuman</button>
    </div>

    <div class="stats-search">
        <div class="total-badge">📋 Total Pengumuman: <span id="totalPengumuman">{{ count($pengumuman ?? []) }}</span></div>
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
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody id="pengumumanBody">
                @forelse(($pengumuman ?? []) as $index => $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p['judul'] }}</td>
                    <td>{{ Str::limit($p['deskripsi'], 60) }}</td>
                    <td>
                        <span class="status-badge {{ $p['status'] == 'Published' ? 'status-published' : 'status-draft' }}">
                            {{ $p['status'] }}
                        </span>
                    </td>
                    <td style="text-align: center">
                        <div class="action-buttons">
                            <button class="btn-icon btn-edit" data-id="{{ $p['id'] }}" title="Edit">✏️</button>
                            <button class="btn-icon btn-hapus" data-id="{{ $p['id'] }}" data-judul="{{ $p['judul'] }}" title="Hapus">🗑️</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-row">Belum ada pengumuman. Silakan tambah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display: none; text-align: center; padding: 20px; color: #9ca3af;">
            Tidak ada pengumuman yang cocok
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search
        const searchInput = document.getElementById('searchPengumuman');
        const rows = document.querySelectorAll('#pengumumanBody tr');
        const emptyMsg = document.getElementById('emptySearchMessage');
        const totalSpan = document.getElementById('totalPengumuman');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                let keyword = this.value.toLowerCase();
                let visibleCount = 0;
                rows.forEach(row => {
                    let judul = row.cells[1]?.innerText.toLowerCase() || '';
                    let deskripsi = row.cells[2]?.innerText.toLowerCase() || '';
                    if (judul.includes(keyword) || deskripsi.includes(keyword)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
                if (totalSpan) totalSpan.innerText = visibleCount;
            });
        }

        // Tambah (sementara alert)
        const btnTambah = document.getElementById('btnTambahPengumuman');
        if (btnTambah) {
            btnTambah.addEventListener('click', () => {
                alert('Fitur tambah pengumuman (implementasikan dengan modal form).');
            });
        }

        // Edit & Hapus (sementara alert)
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                alert(`Edit pengumuman ID ${btn.dataset.id} (implementasikan modal edit).`);
            });
        });
        document.querySelectorAll('.btn-hapus').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm(`Yakin hapus pengumuman "${btn.dataset.judul}"?`)) {
                    alert(`Hapus ID ${btn.dataset.id} – sambungkan ke route DELETE.`);
                }
            });
        });
    });
</script>
@endsection
