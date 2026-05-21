@extends('index', ['dummy' => true])

@section('content')
<style>
/* Gaya usulan nilai – tabel ukuran normal, bagian lain rapat */
.usulan-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 16px 20px;
    margin: 12px;
}
.usulan-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 8px;
}
.usulan-title h3 {
    font-size: 1.3rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}
.usulan-title p {
    margin: 0;
    color: var(--ri-text-muted);
    font-size: 0.8rem;
}
.btn-kembali {
    background: #6c757d;
    color: white;
    padding: 4px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.8rem;
}
.btn-download {
    background: #0d6efd;
    border: none;
    color: white;
    padding: 4px 12px;
    border-radius: 8px;
    margin-left: 6px;
    font-size: 0.75rem;
}
.filter-section {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
    padding: 4px 0;
}
.filter-label {
    font-weight: 600;
    font-size: 0.8rem;
    color: var(--ri-text-primary);
}
.custom-select-wrapper {
    position: relative;
    display: inline-block;
    width: 160px;
}
.custom-select {
    appearance: none;
    -webkit-appearance: none;
    background: var(--ri-card-bg);
    border: 1px solid var(--ri-border);
    border-radius: 40px;
    padding: 5px 28px 5px 12px;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--ri-text-primary);
    cursor: pointer;
    width: 100%;
    background-color: var(--ri-input-bg);
}
.custom-select:hover { border-color: #3b82f6; }
.custom-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
}
.custom-arrow {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 5px solid var(--ri-text-muted);
    pointer-events: none;
}
.search-box {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}
.search-box label {
    font-size: 0.8rem;
    margin: 0;
}
.search-box input {
    padding: 5px 10px;
    border: 1px solid var(--ri-border);
    border-radius: 8px;
    width: 220px;
    background: var(--ri-input-bg);
    font-size: 0.8rem;
}
/* Tabel ukuran normal */
.usulan-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid var(--ri-table-border-outer);
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
    border-bottom: 1px solid var(--ri-table-border-header);
}
.usulan-table td {
    padding: 12px;
    border-bottom: 1px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    background: var(--ri-table-row-bg);
    font-size: 0.875rem;
}
.usulan-table tr:hover td { background: var(--ri-table-row-hover); }
.pagination-info {
    margin-top: 12px;
    text-align: right;
    font-size: 0.75rem;
    color: var(--ri-text-muted);
}
.empty-row { text-align: center; padding: 20px; color: var(--ri-text-muted); }

@media (max-width: 640px) {
    .usulan-container { margin: 8px; padding: 12px; }
    .usulan-table th, .usulan-table td { padding: 8px; font-size: 0.75rem; }
    .search-box input { width: 100%; }
    .custom-select-wrapper { width: 100%; }
    .filter-section { flex-direction: column; align-items: flex-start; gap: 6px; }
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
        <span class="filter-label">Pilih Kategori :</span>
        <div class="custom-select-wrapper">
            <select id="kategoriFilter" class="custom-select">
                <option value="semua">Semua</option>
                <option value="umum">Umum</option>
                <option value="pelajar">Pelajar</option>
            </select>
            <span class="custom-arrow"></span>
        </div>
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
