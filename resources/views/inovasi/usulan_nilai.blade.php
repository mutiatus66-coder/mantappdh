@extends('index', ['dummy' => true])

@section('content')
<style>
.usulan-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
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
}
.btn-download {
    background: #0d6efd;
    border: none;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    margin-left: 8px;
}
.btn-download-pdf { background: #dc3545; }
.filter-section {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}
.filter-section select {
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid var(--ri-border);
    background: var(--ri-input-bg);
    color: var(--ri-text-primary);
}
.search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
}
.search-box input {
    padding: 6px 12px;
    border: 1px solid var(--ri-border);
    border-radius: 8px;
    width: 250px;
    background: var(--ri-input-bg);
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
    color: var(--ri-text-muted);
    border-bottom: 2px solid var(--ri-table-border-header);
}
.usulan-table td {
    padding: 12px;
    border-bottom: 1px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    background: var(--ri-table-row-bg);
}
.usulan-table tr:hover td { background: var(--ri-table-row-hover); }
.pagination-info {
    margin-top: 20px;
    text-align: right;
    font-size: 0.875rem;
    color: var(--ri-text-muted);
}
.empty-row { text-align: center; padding: 30px; color: var(--ri-text-muted); }
@media (max-width: 640px) {
    .usulan-container { margin: 10px; padding: 12px; }
    .usulan-table th, .usulan-table td { padding: 8px; }
    .search-box input { width: 100%; }
}
</style>

<div class="usulan-container">
    <div class="usulan-header">
        <div class="usulan-title">
            <h3>DATA REKAP PENDAFTAR</h3>
            <p>Sub Event: <strong>{{ $subEventNama }}</strong></p>
        </div>
        <div>
            <a href="/inovasi/rekap-nilai" class="btn-kembali">← Kembali</a>
            <button class="btn-download btn-download-pdf" id="pdfBtn">DOWNLOAD PDF</button>
            <button class="btn-download" id="excelBtn">DOWNLOAD EXCEL</button>
        </div>
    </div>

    <div class="filter-section">
        <span>Pilih Kategori :</span>
        <select id="kategoriFilter">
            <option value="semua">Semua</option>
            <option value="umum">Umum</option>
            <option value="pelajar">Pelajar</option>
        </select>
    </div>

    <div class="search-box">
        <label>Search:</label>
        <input type="text" id="searchInput" placeholder="Cari judul atau instansi...">
    </div>

    <div style="overflow-x: auto;">
        <table class="usulan-table">
            <thead>
                <tr>
                    <th>Judul Inovasi</th>
                    <th>Nama Instansi/Organisasi</th>
                    <th>Link Youtube</th>
                    <th>No Handphone</th>
                    <th>Kategori</th>
                    <th>Nilai Tahap 1</th>
                    <th>Nilai Tahap 2</th>
                    <th>Nilai Total</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($usulan as $item)
                <tr data-kategori="{{ strtolower($item['kategori']) }}">
                    <td>{{ $item['judul'] }}</td>
                    <td>{{ $item['instansi'] }}</td>
                    <td>{{ $item['link_youtube'] }}</td>
                    <td>{{ $item['no_hp'] }}</td>
                    <td>{{ $item['kategori'] }}</td>
                    <td>{{ $item['nilai_t1'] }}</td>
                    <td>{{ $item['nilai_t2'] }}</td>
                    <td>{{ $item['nilai_total'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">No data available in table</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-info" id="paginationInfo">Showing 0 to 0 of 0 entries</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kategoriFilter = document.getElementById('kategoriFilter');
        const searchInput = document.getElementById('searchInput');
        const tbody = document.getElementById('tableBody');
        const infoDiv = document.getElementById('paginationInfo');

        function updateDisplay() {
            const kategori = kategoriFilter.value;
            const keyword = searchInput.value.toLowerCase();
            const rows = Array.from(tbody.querySelectorAll('tr'));
            let visibleCount = 0;

            rows.forEach(row => {
                const rowKategori = row.getAttribute('data-kategori');
                const judul = row.cells[0]?.innerText.toLowerCase() || '';
                const instansi = row.cells[1]?.innerText.toLowerCase() || '';
                const matchKategori = (kategori === 'semua' || rowKategori === kategori);
                const matchSearch = judul.includes(keyword) || instansi.includes(keyword);
                if (matchKategori && matchSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const start = visibleCount === 0 ? 0 : 1;
            const end = visibleCount;
            infoDiv.innerText = `Showing ${start} to ${end} of ${visibleCount} entries`;
        }

        kategoriFilter.addEventListener('change', updateDisplay);
        searchInput.addEventListener('keyup', updateDisplay);
        updateDisplay();

        document.getElementById('excelBtn').onclick = () => alert('Download Excel');
        document.getElementById('pdfBtn').onclick = () => alert('Download PDF');
    });
</script>
@endsection
