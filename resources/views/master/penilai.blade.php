@extends('index', ['dummy' => true])

@section('content')
<style>
    /* Style lokal untuk tabel penilai - tidak tergantung Tailwind */
    .penilai-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
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
        color: #1f2937;
    }
    .penilai-title p {
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
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        width: 240px;
    }
    .penilai-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }
    .penilai-table th {
        background: #f9fafb;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #4b5563;
        border-bottom: 1px solid #e5e7eb;
    }
    .penilai-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #1f2937;
        font-size: 0.875rem;
    }
    .penilai-table tr:hover {
        background: #fefce8;
    }
    .btn-hapus {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.875rem;
    }
    .btn-hapus:hover {
        color: #b91c1c;
        text-decoration: underline;
    }
    .empty-row {
        text-align: center;
        padding: 30px;
        color: #9ca3af;
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
        <div class="total-badge">📋 Total Penilai: <span id="totalPenilai">{{ count($penilai) }}</span></div>
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
                            🗑️ Hapus
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display: none; text-align: center; padding: 20px; color: #9ca3af;">
            Tidak ada data yang cocok
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fitur search
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

        // Modal tambah (sederhana, pakai confirm dulu)
        const btnTambah = document.getElementById('btnTambahPenilai');
        btnTambah.addEventListener('click', function() {
            alert('Fitur tambah belum dihubungkan ke backend.\nSilakan implementasikan form modal.');
        });

        // Hapus (konfirmasi sederhana)
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
