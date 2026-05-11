@extends('index', ['dummy' => true])

@section('content')
<style>
.rekap-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}
.rekap-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.rekap-title h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}
.rekap-title p {
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
}
.btn-download {
    background: #0d6efd;
    border: none;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    margin-left: 8px;
}
.btn-download-pdf {
    background: #dc3545;
}
.filter-section {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.filter-section select {
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid var(--ri-border);
    background: var(--ri-input-bg);
    color: var(--ri-text-primary);
}
.rekap-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid var(--ri-table-border-outer);
    border-radius: 8px;
    overflow: hidden;
}
.rekap-table th {
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
.rekap-table td {
    padding: 12px;
    border-bottom: 1.5px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    font-size: 0.875rem;
    background: var(--ri-table-row-bg);
}
.rekap-table tr:hover td {
    background: var(--ri-table-row-hover);
}
.nilai-badge {
    font-weight: 600;
}
.pagination-info {
    margin-top: 20px;
    text-align: right;
    font-size: 0.875rem;
    color: var(--ri-text-muted);
}
@media (max-width: 640px) {
    .rekap-container { margin: 10px; padding: 12px; }
    .rekap-table th, .rekap-table td { padding: 8px; }
}
</style>

<div class="rekap-container">
    <div class="rekap-header">
        <div class="rekap-title">
            <h3>Rekap Nilai Inovasi</h3>
            <p>Nilai tahap 1, tahap 2, dan total</p>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="btn-kembali">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button class="btn-download" id="downloadExcel">
                <i class="bi bi-file-earmark-excel"></i> download excel
            </button>
            <button class="btn-download btn-download-pdf" id="downloadPDF">
                <i class="bi bi-file-earmark-pdf"></i> download pdf
            </button>
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

    <div style="overflow-x: auto;">
        <table class="rekap-table">
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
            <tbody id="rekapBody">
                @forelse($rekap as $item)
                <tr data-kategori="{{ strtolower($item['kategori']) }}">
                    <td>{{ $item['inovasi'] }}</td>
                    <td>{{ $item['instansi'] }}</td>
                    <td>{{ $item['link_youtube'] }}</td>
                    <td>{{ $item['no_hp'] }}</td>
                    <td>{{ $item['kategori'] }}</td>
                    <td class="nilai-badge">{{ $item['nilai_t1'] }}</td>
                    <td class="nilai-badge">{{ $item['nilai_t2'] }}</td>
                    <td class="nilai-badge">{{ $item['nilai_total'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">Belum ada data penilaian</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-info" id="paginationInfo">
        Showing 0 to 0 of 0 entries
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('kategoriFilter');
    const tbody = document.getElementById('rekapBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const infoDiv = document.getElementById('paginationInfo');

    function updateTable() {
        const selected = filterSelect.value;
        let visibleCount = 0;
        rows.forEach(row => {
            const kategori = row.getAttribute('data-kategori');
            if (selected === 'semua' || kategori === selected) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        infoDiv.innerText = `Showing ${visibleCount} to ${visibleCount} of ${visibleCount} entries`;
    }

    filterSelect.addEventListener('change', updateTable);
    updateTable();

    document.getElementById('downloadExcel').onclick = () => alert('Generate Excel...');
    document.getElementById('downloadPDF').onclick = () => alert('Generate PDF...');
});
</script>
@endsection
