@extends('index', ['dummy' => true])

@section('content')
<style>
.rekap-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
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
    border: none;
}
.filter-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.filter-label {
    font-weight: 500;
    color: var(--ri-text-primary);
}
.filter-select {
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
    color: var(--ri-text-muted);
    border-bottom: 2px solid var(--ri-table-border-header);
}
.rekap-table td {
    padding: 12px;
    border-bottom: 1px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
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
.empty-row {
    text-align: center;
    padding: 30px;
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
        <a href="{{ url()->previous() }}" class="btn-kembali">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="filter-wrapper">
        <span class="filter-label">Pilih Kategori :</span>
        <select id="kategoriFilter" class="filter-select">
            <option value="semua">Semua</option>
            <option value="umum">Umum</option>
            <option value="pelajar">Pelajar</option>
        </select>
    </div>

    <div style="overflow-x: auto;">
        <table class="rekap-table" id="rekapTable">
            <thead>
                <tr>
                    <th>Inovasi</th>
                    <th>Instansi/Organisasi</th>
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
                    <td colspan="8" class="empty-row">No data available in table</td>
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
    const filter = document.getElementById('kategoriFilter');
    const tbody = document.getElementById('rekapBody');
    const infoDiv = document.getElementById('paginationInfo');
    let allRows = Array.from(tbody.querySelectorAll('tr'));

    function updateDisplay() {
        const selected = filter.value;
        let visibleCount = 0;
        allRows.forEach(row => {
            const kategori = row.getAttribute('data-kategori');
            if (selected === 'semua' || kategori === selected) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        infoDiv.innerText = `Showing ${visibleCount} to ${visibleCount} of ${visibleCount} entries`;
        if (visibleCount === 0 && allRows.length === 0) {
            infoDiv.innerText = 'Showing 0 to 0 of 0 entries';
        }
    }

    filter.addEventListener('change', updateDisplay);
    updateDisplay();
});
</script>
@endsection
