@extends('index', ['dummy' => true])

@section('content')
<style>
    .pengumuman-container {
        background: var(--ri-card-bg);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
        transition: background 0.2s, color 0.2s;
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
        color: var(--ri-text-primary);
    }
    .pengumuman-title p {
        margin: 0;
        color: var(--ri-text-muted);
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
        border: 1px solid var(--ri-border);
        border-radius: 8px;
        font-size: 0.875rem;
        width: 240px;
        background: var(--ri-input-bg);
        color: var(--ri-text-primary);
        transition: background 0.2s, color 0.2s, border-color 0.2s;
    }
    .pengumuman-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid var(--ri-table-border-outer);
        border-radius: 8px;
        overflow: hidden;
    }
    .pengumuman-table th {
        background: var(--ri-table-head-bg);
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--ri-text-muted);
        border-bottom: 2px solid var(--ri-table-border-header);
        transition: background 0.2s, color 0.2s;
    }
    .pengumuman-table td {
        padding: 12px;
        border-bottom: 1.5px solid var(--ri-table-border-row);
        color: var(--ri-text-primary);
        font-size: 0.875rem;
        background: var(--ri-table-row-bg);
        transition: background 0.2s, color 0.2s;
    }
    .pengumuman-table tr:hover td {
        background: var(--ri-table-row-hover);
    }
    .pengumuman-table tr:last-child td {
        border-bottom: none;
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
    .btn-edit-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white !important;
        border: none;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
    }
    .btn-edit-icon:hover { opacity: 0.88; }
    .btn-hapus {
        background: #A32D2D;
        color: #ffffff !important;
        border: none;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        transition: background 0.15s;
    }
    .btn-hapus:hover {
        background: #8b2424;
        color: #ffffff !important;
    }
    .empty-row {
        text-align: center;
        padding: 30px;
        color: var(--ri-text-muted);
        background: var(--ri-table-row-bg);
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
                            <button class="btn-edit-icon" data-id="{{ $p['id'] }}" title="Edit">Ubah</button>
                            <button class="btn-hapus" data-id="{{ $p['id'] }}" data-judul="{{ $p['judul'] }}" title="Hapus">Hapus</button>
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
        <div id="emptySearchMessage" style="display: none; text-align: center; padding: 20px; color: var(--ri-text-muted);">
            Tidak ada pengumuman yang cocok
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        const btnTambah = document.getElementById('btnTambahPengumuman');
        if (btnTambah) {
            btnTambah.addEventListener('click', () => {
                alert('Fitur tambah pengumuman (implementasikan dengan modal form).');
            });
        }

        document.querySelectorAll('.btn-edit-icon').forEach(btn => {
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