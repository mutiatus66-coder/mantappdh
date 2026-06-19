@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="page-container">

    {{-- ── HEADER ── --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="ec-title">Rekap Nilai Pendaftar</h3>
            <p class="ec-subtitle">{{ $subEventNama ?? '' }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ url('/inovasi/rekap-nilai') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button class="btn btn-danger" id="pdfBtn">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
            </button>
            <button class="btn btn-success" id="excelBtn">
                <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
            </button>
        </div>
    </div>

    {{-- ── FILTER & SEARCH ── --}}
    <div class="sub-event-stats mb-3">
        <div class="total-badge">
            <select id="kategoriFilter" class="form-select form-select-sm" style="min-width: 160px;">
                <option value="semua">Semua Kategori</option>
                <option value="umum">Umum</option>
                <option value="pelajar">Pelajar</option>
            </select>
        </div>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Cari judul atau instansi...">
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div style="overflow-x: auto;">
        <table class="se-table" id="tabelRekap">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Judul Inovasi</th>
                    <th>Instansi / Organisasi</th>
                    <th>Link Youtube</th>
                    <th>No Handphone</th>
                    <th style="text-align:center;">Kategori</th>
                    <th style="text-align:center;">Nilai T1</th>
                    <th style="text-align:center;">Nilai T2</th>
                    <th style="text-align:center;">Nilai Total</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($usulan as $item)
                <tr data-kategori="{{ strtolower($item['kategori'] ?? '') }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['judul'] ?? '-' }}</td>
                    <td>{{ $item['instansi'] ?? '-' }}</td>
                    <td>
                        @if(!empty($item['link_youtube']))
                            <a href="{{ $item['link_youtube'] }}" target="_blank" class="link-primary">
                                <i class="bi bi-youtube me-1"></i>Lihat
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item['no_hp'] ?? '-' }}</td>
                    <td style="text-align:center;">
                        <span class="badge-kategori">{{ $item['kategori'] ?? '-' }}</span>
                    </td>
                    <td style="text-align:center;">{{ $item['nilai_t1'] ?? '-' }}</td>
                    <td style="text-align:center;">{{ $item['nilai_t2'] ?? '-' }}</td>
                    <td style="text-align:center;">
                        <strong>{{ $item['nilai_total'] ?? '-' }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data pendaftar untuk sub event ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── INFO ── --}}
    <div class="mt-2" style="font-size: 0.825rem; color: var(--ri-text-muted);">
        <span id="paginationInfo">Menampilkan 0 dari 0 data</span>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kategoriFilter = document.getElementById('kategoriFilter');
        const searchInput    = document.getElementById('searchInput');
        const tbody          = document.getElementById('tableBody');
        const infoDiv        = document.getElementById('paginationInfo');

        function updateDisplay() {
            const kategori = kategoriFilter.value;
            const keyword  = searchInput.value.toLowerCase().trim();
            const rows     = Array.from(tbody.querySelectorAll('tr'));
            let visible    = 0;

            rows.forEach(row => {
                if (row.querySelector('.empty-row')) return;
                const rowKat   = row.getAttribute('data-kategori') ?? '';
                const teks     = row.textContent.toLowerCase();
                const matchKat = kategori === 'semua' || rowKat === kategori;
                const matchCari = teks.includes(keyword);
                const tampil   = matchKat && matchCari;
                row.style.display = tampil ? '' : 'none';
                if (tampil) visible++;
            });

            const total = rows.filter(r => !r.querySelector('.empty-row')).length;
            infoDiv.textContent = `Menampilkan ${visible} dari ${total} data`;
        }

        kategoriFilter.addEventListener('change', updateDisplay);
        searchInput.addEventListener('keyup', updateDisplay);
        updateDisplay();

        document.getElementById('pdfBtn').onclick = () => {
        window.print();
    };
        document.getElementById('excelBtn').onclick = () => {
        const table = document.getElementById('tabelRekap');
        if (!table) return;
        const csv = [...table.querySelectorAll('tr')].map(row =>
            [...row.querySelectorAll('th, td')].map(c => '"' + c.innerText.trim().replace(/"/g, '""') + '"').join(',')
        ).join('\n');
        const a = document.createElement('a');
        a.href     = URL.createObjectURL(new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' }));
        a.download = 'rekap_pendaftar.csv';
        a.click();
    };
});
</script>
@endpush