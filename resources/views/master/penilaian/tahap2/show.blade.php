@extends('index')

@section('content')

<div class="penilaian-detail-container">

    {{-- ── BREADCRUMB HEADER ── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <p class="penilaian-sub-label mb-1">Sub Event :</p>
            <h4 class="penilaian-sub-title mb-0">{{ $subEvent['sub_event'] }}</h4>
        </div>
        <a href="{{ route('penilaian.tahap2.index') }}" class="btn btn-kembali">
            </i>Kembali
        </a>
    </div>

    {{-- ── TABS — Umum | Pelajar ── --}}
    <ul class="nav penilaian-tabs mb-4" id="tabNominator" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="penilaian-tab-btn active"
                    id="tab-umum" data-bs-toggle="tab" data-bs-target="#panel-umum"
                    type="button" role="tab">Umum</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="penilaian-tab-btn"
                    id="tab-pelajar" data-bs-toggle="tab" data-bs-target="#panel-pelajar"
                    type="button" role="tab">Pelajar</button>
        </li>
    </ul>

    <div class="tab-content" id="tabNominatorContent">

        {{-- ─────────── TAB UMUM ─────────── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">

            <div class="penilaian-table-card">
                <div class="penilaian-table-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="penilaian-table-title mb-0">Nominator Umum</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-rangking" id="btnRangkingUmum">
                            </i>Rangking
                        </button>
                        <button class="btn btn-excel" id="btnExcelUmum">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table penilaian-table align-middle mb-0" id="tableUmum">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" width="90">Rangking</th>
                                <th class="text-center" width="90">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" width="80">{{ $p['nama_singkat'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nominasiUmum as $i => $nom)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $nom['inovator'] }}</td>
                                <td>{{ $nom['nama_inovasi'] }}</td>
                                <td class="text-center">
                                    @if($nom['rangking'])
                                        <span class="badge-rangking">{{ $nom['rangking'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold" style="color:#3C678E;">
                                    {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 1) : '-' }}
                                </td>
                                @foreach($penilai as $p)
                                <td class="text-center">
                                    {{ $nom['nilai'][$p['id']] ?? '-' }}
                                </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 5 + count($penilai) }}" class="text-center py-5 empty-row">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    Belum ada data nominasi umum.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- ─────────── TAB PELAJAR ─────────── --}}
        <div class="tab-pane fade" id="panel-pelajar" role="tabpanel">

            <div class="penilaian-table-card">
                <div class="penilaian-table-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="penilaian-table-title mb-0">Nominator Pelajar</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-rangking" id="btnRangkingPelajar">
                            </i>Rangking
                        </button>
                        <button class="btn btn-excel" id="btnExcelPelajar">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table penilaian-table align-middle mb-0" id="tablePelajar">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" width="90">Rangking</th>
                                <th class="text-center" width="90">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" width="80">{{ $p['nama_singkat'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nominasiPelajar as $i => $nom)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $nom['inovator'] }}</td>
                                <td>{{ $nom['nama_inovasi'] }}</td>
                                <td class="text-center">
                                    @if($nom['rangking'])
                                        <span class="badge-rangking">{{ $nom['rangking'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold" style="color:#3C678E;">
                                    {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 1) : '-' }}
                                </td>
                                @foreach($penilai as $p)
                                <td class="text-center">
                                    {{ $nom['nilai'][$p['id']] ?? '-' }}
                                </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 5 + count($penilai) }}" class="text-center py-5 empty-row">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    Belum ada data nominasi pelajar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection


@push('styles')
<style>
/* ── Container ── */
.penilaian-detail-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.10);
    padding: 28px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}

/* ── Breadcrumb header ── */
.penilaian-sub-label {
    font-size: 0.80rem;
    color: var(--ri-text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}
.penilaian-sub-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #3C678E;
}
[data-bs-theme="dark"] .penilaian-sub-title { color: #6DADD8; }

.btn-kembali {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    border-radius: 8px;
    padding: 8px 18px;
    font-size: 0.85rem;
    transition: opacity 0.15s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.btn-kembali:hover { opacity: 0.88; color: #fff !important; }

/* ── Tabs ── */
.penilaian-tabs {
    border-bottom: 2px solid var(--ri-border);
    gap: 4px;
}
.penilaian-tab-btn {
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    padding: 10px 22px;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ri-text-muted);
    cursor: pointer;
    transition: color 0.15s, border-color 0.15s;
    border-radius: 0;
}
.penilaian-tab-btn.active,
.penilaian-tab-btn:focus {
    color: #3C678E;
    border-bottom-color: #3C678E;
    outline: none;
    background: transparent;
}
[data-bs-theme="dark"] .penilaian-tab-btn.active {
    color: #6DADD8;
    border-bottom-color: #6DADD8;
}

/* ── Table card ── */
.penilaian-table-card {
    background: var(--ri-card-bg);
    border: 1px solid var(--ri-border);
    border-radius: 10px;
    padding: 20px 22px;
    transition: background 0.2s;
}
.penilaian-table-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #3C678E;
}
[data-bs-theme="dark"] .penilaian-table-title { color: #6DADD8; }

/* ── Table ── */
.penilaian-table {
    border: 2px solid var(--ri-table-border-outer) !important;
    border-radius: 8px;
    overflow: hidden;
}
.penilaian-table th {
    background: var(--ri-table-head-bg) !important;
    padding: 13px 12px;
    border-bottom: 2px solid var(--ri-table-border-header) !important;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ri-text-muted) !important;
}
.penilaian-table td {
    padding: 13px 12px;
    border-bottom: 1px solid var(--ri-table-border-row) !important;
    color: var(--ri-text-primary) !important;
    background: var(--ri-table-row-bg) !important;
    font-size: 0.875rem;
    transition: background 0.2s;
}
.penilaian-table tr:hover td { background: var(--ri-table-row-hover) !important; }

/* ── Rangking badge ── */
.badge-rangking {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #3C678E, #6DADD8);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    width: 28px; height: 28px;
    border-radius: 50%;
}

/* ── Buttons ── */
.btn-rangking {
    background: linear-gradient(135deg, #0C4C8A, #142D54) !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    font-size: 0.82rem;
    border-radius: 7px;
    padding: 7px 14px;
    transition: opacity 0.15s;
}
.btn-rangking:hover { opacity: 0.85; }

.btn-excel {
    background: var(--ri-table-head-bg) !important;
    color: var(--ri-text-primary) !important;
    border: 1px solid var(--ri-border) !important;
    font-weight: 600;
    font-size: 0.82rem;
    border-radius: 7px;
    padding: 7px 14px;
    transition: background 0.15s, color 0.15s;
}
.btn-excel:hover {
    background: #65A605 !important;
    color: #fff !important;
    border-color: #65A605 !important;
}

.empty-row {
    color: var(--ri-text-muted) !important;
    background: var(--ri-table-row-bg) !important;
}
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Sort by Rangking ──
    function sortTableByTotal(tableId) {
        const tbody = document.querySelector('#' + tableId + ' tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            const va = parseFloat(a.cells[4]?.textContent.trim()) || 0;
            const vb = parseFloat(b.cells[4]?.textContent.trim()) || 0;
            return vb - va;
        });
        rows.forEach((r, i) => {
            r.cells[0].textContent = i + 1;
            tbody.appendChild(r);
        });
    }

    document.getElementById('btnRangkingUmum')?.addEventListener('click',    () => sortTableByTotal('tableUmum'));
    document.getElementById('btnRangkingPelajar')?.addEventListener('click', () => sortTableByTotal('tablePelajar'));

    // ── Export Excel (simple CSV download) ──
    function exportTableToCSV(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        let csv = [];
        table.querySelectorAll('tr').forEach(row => {
            const cols = Array.from(row.querySelectorAll('th, td')).map(c => '"' + c.innerText.replace(/"/g, '""') + '"');
            csv.push(cols.join(','));
        });
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
        a.download = filename + '.csv'; a.click();
    }

    document.getElementById('btnExcelUmum')?.addEventListener('click',    () => exportTableToCSV('tableUmum',    'nominasi-umum'));
    document.getElementById('btnExcelPelajar')?.addEventListener('click', () => exportTableToCSV('tablePelajar', 'nominasi-pelajar'));

});
</script>
@endpush