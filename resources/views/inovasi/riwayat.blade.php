@extends('index', ['dummy' => true])

@section('content')
<style>
/* Menggunakan ulang style dari penilai, hanya ubah nama kelas */
.btn-warning {
    background: #65A605 !important;
    border-color: #65A605 !important;
    color: #fff !important;
}
.btn-warning:hover {
    background: #538a04 !important;
    border-color: #538a04 !important;
}
.riwayat-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}
.riwayat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.riwayat-title h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}
.riwayat-title p {
    margin: 0;
    color: var(--ri-text-muted);
    font-size: 0.875rem;
}
.btn-kembali {
    background: #6c757d;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
}
.btn-download-excel {
    background: #1f7e4e;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: 500;
}
.riwayat-stats {
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
}
.riwayat-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid var(--ri-table-border-outer);
    border-radius: 8px;
    overflow: hidden;
}
.riwayat-table th {
    background: var(--ri-table-head-bg);
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ri-text-muted);
    border-bottom: 2px solid var(--ri-table-border-header);
}
.riwayat-table td {
    padding: 12px;
    border-bottom: 1.5px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    font-size: 0.875rem;
    background: var(--ri-table-row-bg);
}
.riwayat-table tr:hover td {
    background: var(--ri-table-row-hover);
}
.riwayat-table tr:last-child td {
    border-bottom: none;
}
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #fef3c7;
    color: #92400e;
}
.status-selesai {
    background: #d1fae5;
    color: #065f46;
}
.status-proses {
    background: #dbeafe;
    color: #1e40af;
}
.empty-row {
    text-align: center;
    padding: 30px;
    color: var(--ri-text-muted);
    background: var(--ri-table-row-bg);
}
@media (max-width: 640px) {
    .riwayat-container { margin: 10px; padding: 12px; }
    .riwayat-table th, .riwayat-table td { padding: 8px; }
    .search-box input { width: 100%; }
}
</style>

<div class="riwayat-container">
    <div class="riwayat-header">
        <div class="riwayat-title">
            <h3>Riwayat Inovasi</h3>
            <p>Daftar usulan inovasi yang telah diajukan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn-kembali">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button class="btn-download-excel" id="downloadExcelRiwayat">
                <i class="bi bi-file-earmark-excel"></i> Download Excel
            </button>
        </div>
    </div>

    <div class="riwayat-stats">
        <div class="total-badge">Total Usulan: <span id="totalRiwayat">{{ count($inovasi) }}</span></div>
        <div class="search-box">
            <input type="text" id="searchRiwayat" placeholder="Cari nama atau inovasi...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="riwayat-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Status</th>
                    <th>Inovator</th>
                    <th>Nama Inovasi</th>
                    <th>Nama Tim</th>
                    <th>Nama Ketua</th>
                    <th>Email Ketua</th>
                    <th>No WA Ketua</th>
                </tr>
            </thead>
            <tbody id="riwayatBody">
                @forelse($inovasi as $index => $i)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <span class="status-badge
                            @if($i['status'] == 'Selesai') status-selesai
                            @elseif($i['status'] == 'Proses') status-proses
                            @endif">
                            {{ $i['status'] }}
                        </span>
                    </td>
                    <td>{{ $i['inovator'] }}</td>
                    <td>{{ $i['nama_inovasi'] }}</td>
                    <td>{{ $i['nama_tim'] }}</td>
                    <td>{{ $i['ketua_nama'] }}</td>
                    <td>{{ $i['ketua_email'] }}</td>
                    <td>{{ $i['ketua_wa'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data riwayat
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">
            Tidak ada data yang cocok
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Search
    const searchInput = document.getElementById('searchRiwayat');
    const rows = document.querySelectorAll('#riwayatBody tr');
    const emptyMsg = document.getElementById('emptySearchMessage');
    const totalSpan = document.getElementById('totalRiwayat');

    searchInput.addEventListener('keyup', function () {
        let kw = this.value.toLowerCase();
        let n = 0;
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const show = text.includes(kw);
            row.style.display = show ? '' : 'none';
            if (show) n++;
        });
        emptyMsg.style.display = n === 0 ? 'block' : 'none';
        totalSpan.innerText = n;
    });

    // Download Excel (simulasi)
    document.getElementById('downloadExcelRiwayat').addEventListener('click', function () {
        alert('Fitur download Excel akan diimplementasikan.');
    });
});
</script>
@endsection
