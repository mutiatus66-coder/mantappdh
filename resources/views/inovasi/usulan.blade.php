@extends('index', ['dummy' => true])

@section('content')
<style>
/* Gaya sama persis dengan penilai, disesuaikan */
.usulan-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}
.usulan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.usulan-title h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}
.usulan-title p {
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
    margin-left: 8px;
}
.usulan-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.search-box {
    display: flex;
    align-items: center;
    gap: 8px;
}
.search-box label {
    margin: 0;
    color: var(--ri-text-muted);
    font-size: 0.875rem;
}
.search-box input {
    padding: 6px 12px;
    border: 1px solid var(--ri-border);
    border-radius: 8px;
    font-size: 0.875rem;
    width: 200px;
    background: var(--ri-input-bg);
    color: var(--ri-text-primary);
}
.usulan-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid var(--ri-table-border-outer);
    border-radius: 8px;
    overflow: hidden;
}
.usulan-table th {
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
.usulan-table td {
    padding: 12px;
    border-bottom: 1.5px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    font-size: 0.875rem;
    background: var(--ri-table-row-bg);
}
.usulan-table tr:hover td {
    background: var(--ri-table-row-hover);
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
.empty-row {
    text-align: center;
    padding: 30px;
    color: var(--ri-text-muted);
    background: var(--ri-table-row-bg);
}
@media (max-width: 640px) {
    .usulan-container { margin: 10px; padding: 12px; }
    .usulan-table th, .usulan-table td { padding: 8px; }
    .search-box input { width: 100%; }
}
</style>

<div class="usulan-container">
    <div class="usulan-header">
        <div class="usulan-title">
            <h3>DATA RIWAYAT</h3>
            <p>Sub Event: <strong>{{ $subEventNama }}</strong></p>
        </div>
        <div>
            <a href="/inovasi/riwayat" class="btn-kembali">
               <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
            </a>
            <button class="btn-download-excel" id="downloadExcelUsulan">
                <i class="bi bi-file-earmark-excel"></i> Download Excel
            </button>
        </div>
    </div>

    <div class="usulan-stats">
        <div class="total-badge">Total Usulan: <span id="totalUsulan">{{ count($usulan) }}</span></div>
        <div class="search-box">
            <label>Cari:</label>
            <input type="text" id="searchUsulan" placeholder="Nama inovasi atau inovator...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="usulan-table">
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
            <tbody id="usulanBody">
                @forelse($usulan as $index => $u)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="status-badge">{{ $u['status'] }}</span></td>
                    <td>{{ $u['inovator'] }}</td>
                    <td>{{ $u['nama_inovasi'] }}</td>
                    <td>{{ $u['nama_tim'] }}</td>
                    <td>{{ $u['ketua_nama'] }}</td>
                    <td>{{ $u['ketua_email'] }}</td>
                    <td>{{ $u['ketua_wa'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada usulan untuk sub event ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">
            Tidak ada usulan yang cocok
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchUsulan');
    const rows = document.querySelectorAll('#usulanBody tr');
    const emptyMsg = document.getElementById('emptySearchMessage');
    const totalSpan = document.getElementById('totalUsulan');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            let kw = this.value.toLowerCase();
            let n = 0;
            rows.forEach(row => {
                // kolom inovator (index 2) dan nama inovasi (index 3)
                const inovator = row.cells[2]?.innerText.toLowerCase() || '';
                const inovasi = row.cells[3]?.innerText.toLowerCase() || '';
                const show = inovator.includes(kw) || inovasi.includes(kw);
                row.style.display = show ? '' : 'none';
                if (show) n++;
            });
            emptyMsg.style.display = n === 0 ? 'block' : 'none';
            totalSpan.innerText = n;
        });
    }

    document.getElementById('downloadExcelUsulan')?.addEventListener('click', () => {
        alert('Fitur download Excel akan diimplementasikan.');
    });
});
</script>
@endsection
