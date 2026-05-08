@extends('index', ['dummy' => true])

@section('content')
<style>
    .penilai-container {
        background: var(--ri-card-bg);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
        transition: background 0.2s, color 0.2s;
    }
    .penilai-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .penilai-title h3 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
        color: var(--ri-text-primary);
    }
    .penilai-title p {
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
    .btn-tambah:hover {
        opacity: 0.9;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .penilai-stats {
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
    .penilai-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid var(--ri-table-border-outer);
        border-radius: 8px;
        overflow: hidden;
    }
    .penilai-table th {
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
    .penilai-table td {
        padding: 12px;
        border-bottom: 1.5px solid var(--ri-table-border-row);
        color: var(--ri-text-primary);
        font-size: 0.875rem;
        background: var(--ri-table-row-bg);
        transition: background 0.2s, color 0.2s;
    }
    .penilai-table tr:hover td {
        background: var(--ri-table-row-hover);
    }
    .penilai-table tr:last-child td {
        border-bottom: none;
    }
    .btn-hapus {
        background: #A32D2D;
        color: #ffffff !important;
        border: none;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
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
        .penilai-container { margin: 10px; padding: 12px; }
        .penilai-table th, .penilai-table td { padding: 8px; }
        .search-box input { width: 100%; }
    }
</style>

<div class="penilai-container">
    <div class="penilai-header">
        <div class="penilai-title">
            <h3>Master Penilai</h3>
            <p>Kelola data penilai</p>
        </div>
        <button class="btn-tambah" id="btnTambahPenilai">+ Tambah Penilai</button>
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
                    <th style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPenilaiBody">
                @foreach($penilai as $index => $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p['nama'] }}</td>
                    <td>{{ $p['email'] }}</td>
                    <td style="text-align: center">
                        <button class="btn-hapus" data-id="{{ $p['id'] }}" data-nama="{{ $p['nama'] }}">
                            Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display: none; text-align: center; padding: 20px; color: var(--ri-text-muted);">
            Tidak ada data yang cocok
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchPenilai');
        const rows = document.querySelectorAll('#tabelPenilaiBody tr');
        const emptyMsg = document.getElementById('emptySearchMessage');
        const totalSpan = document.getElementById('totalPenilai');

        searchInput.addEventListener('keyup', function() {
            let keyword = this.value.toLowerCase();
            let visibleCount = 0;
            rows.forEach(row => {
                let nama = row.cells[1]?.innerText.toLowerCase() || '';
                let email = row.cells[2]?.innerText.toLowerCase() || '';
                if (nama.includes(keyword) || email.includes(keyword)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            totalSpan.innerText = visibleCount;
        });

        const btnTambah = document.getElementById('btnTambahPenilai');
        btnTambah.addEventListener('click', function() {
            alert('Fitur tambah belum dihubungkan ke backend.\nSilakan implementasikan form modal.');
        });

        const hapusBtns = document.querySelectorAll('.btn-hapus');
        hapusBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                let nama = this.dataset.nama;
                if (confirm(`Yakin ingin menghapus penilai "${nama}"?`)) {
                    alert(`Hapus ${nama} - implementasikan dengan form DELETE.`);
                }
            });
        });
    });
</script>
@endsection