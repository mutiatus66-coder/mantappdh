@extends('index', ['dummy' => true])

@section('content')
<div class="usulan-container">
    <div class="usulan-header">
        <div class="usulan-title">
            <h3>DATA REKAP PENDAFTAR</h3>
            <p>Sub Event: <strong>{{ $subEventNama }}</strong></p>
        </div>
        <div>
            <a href="{{ url('/inovasi/rekap-nilai') }}" class="btn-kembali">← Kembali</a>
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
                    <td colspan="8" class="empty-row">No data available in table</td
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
