@extends('index')

@section('content')

<div class="rv-container">

    {{-- ── HEADER ── --}}
    <div class="rv-page-header">
        <div>
            <p class="rv-sub-label">Sub Event</p>
            <h3 class="rv-page-title">{{ $subEvent['sub_event'] }}</h3>
        </div>
        <a href="{{ route('penilaian.tahap2.index') }}" class="btn btn-primary">
            Kembali
        </a>
    </div>

    {{-- ── TABS ── --}}
    <div class="rv-tabs-wrap">
        <ul class="nav rv-tabs" id="tabNominator" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn active"
                        id="tab-umum" data-bs-toggle="tab" data-bs-target="#panel-umum"
                        type="button" role="tab">Umum</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn"
                        id="tab-pelajar" data-bs-toggle="tab" data-bs-target="#panel-pelajar"
                        type="button" role="tab">Pelajar</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="tabNominatorContent">

        {{-- ─────────── TAB UMUM ─────────── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">
            <div class="rv-card">

                <div class="rv-card-header">
                    <h6 class="rv-card-title">Nominator Umum</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" id="btnRangkingUmum">
                            <i class="bi bi-sort-numeric-down me-1"></i>Rangking
                        </button>
                        <button class="btn btn-info" id="btnExcelUmum">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="rv-table" id="tableUmum">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:48px">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" style="width:88px">Rangking</th>
                                <th class="text-center" style="width:100px">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" style="width:80px">{{ $p['nama_singkat'] }}</th>
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
                                        <span class="rv-badge-rank">{{ $nom['rangking'] }}</span>
                                    @else
                                        <span style="color:var(--ri-text-muted)">—</span>
                                    @endif
                                </td>
                                <td class="text-center rv-nilai">
                                    {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 1) : '—' }}
                                </td>
                                @foreach($penilai as $p)
                                <td class="text-center">{{ $nom['nilai'][$p['id']] ?? '—' }}</td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 5 + count($penilai) }}" class="rv-empty">
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
            <div class="rv-card">

                <div class="rv-card-header">
                    <h6 class="rv-card-title">Nominator Pelajar</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" id="btnRangkingPelajar">
                            <i class="bi bi-sort-numeric-down me-1"></i>Rangking
                        </button>
                        <button class="btn btn-info" id="btnExcelPelajar">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="rv-table" id="tablePelajar">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:48px">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" style="width:88px">Rangking</th>
                                <th class="text-center" style="width:100px">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" style="width:80px">{{ $p['nama_singkat'] }}</th>
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
                                        <span class="rv-badge-rank">{{ $nom['rangking'] }}</span>
                                    @else
                                        <span style="color:var(--ri-text-muted)">—</span>
                                    @endif
                                </td>
                                <td class="text-center rv-nilai">
                                    {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 1) : '—' }}
                                </td>
                                @foreach($penilai as $p)
                                <td class="text-center">{{ $nom['nilai'][$p['id']] ?? '—' }}</td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 5 + count($penilai) }}" class="rv-empty">
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
/* ═══════════════════════════════════════════
   SHARED RV (Rekap Nilai) DESIGN SYSTEM
   ─ Dipakai di Tahap 1, Tahap 2, dan Rekap
═══════════════════════════════════════════ */

.rv-container {
    padding: 28px 24px;
    margin: 20px;
}

.rv-sub-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--ri-text-muted);
    margin-bottom: 4px;
}
.rv-page-title {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--ri-text-primary);
    margin: 0 0 1.5rem;
}
.rv-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 1.5rem;
}

.btn-rv-back {
    background: linear-gradient(105deg, #2563eb, #1e40af);
    color: #fff !important;
    border: none;
    font-weight: 600;
    font-size: 0.85rem;
    border-radius: 60px;
    padding: 9px 20px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    box-shadow: 0 2px 6px rgba(37,99,235,0.2);
    transition: all 0.2s;
}
.btn-rv-back:hover {
    background: linear-gradient(105deg, #1d4ed8, #1e3a8a);
    transform: scale(1.02);
    box-shadow: 0 6px 12px rgba(37,99,235,0.25);
    color: #fff !important;
}

.rv-tabs-wrap { margin-bottom: 20px; }
.rv-tabs { border-bottom: 2px solid var(--ri-border); gap: 4px; }
.rv-tab-btn {
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
.rv-tab-btn.active,
.rv-tab-btn:focus {
    color: #2563eb;
    border-bottom-color: #2563eb;
    outline: none;
    background: transparent;
}
[data-bs-theme="dark"] .rv-tab-btn.active {
    color: #60a5fa;
    border-bottom-color: #60a5fa;
}

.rv-card {
    background: var(--ri-card-bg);
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 32px;
    padding: 28px 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    transition: all 0.25s ease;
}
[data-bs-theme="dark"] .rv-card {
    border-color: rgba(255,255,255,0.06);
}

.rv-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px;
}
.rv-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--ri-text-primary);
    margin: 0;
}
.btn-rv-excel {
    background: var(--ri-table-head-bg);
    color: var(--ri-text-primary);
    border: 1px solid var(--ri-border) !important;
}
.btn-rv-excel:hover {
    background: #65A605 !important;
    color: #fff !important;
    border-color: #65A605 !important;
}

/* ── Rangking badge ── */
.rv-badge-rank {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(105deg, #2563eb, #1e40af);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(37,99,235,0.25);
}

/* ── Table ── */
.rv-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}
.rv-table thead tr {
    border-bottom: 2px solid var(--ri-border);
}
.rv-table th {
    padding: 12px 14px;
    font-size: 0.74rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #374151;
    background: transparent;
    white-space: nowrap;
}
[data-bs-theme="dark"] .rv-table th {
    color: #d1d5db;
}
.rv-table td {
    padding: 14px 14px;
    border-bottom: 1px solid var(--ri-border);
    color: var(--ri-text-primary);
    background: transparent;
    vertical-align: middle;
    transition: background 0.15s;
    font-weight: 500;
}
.rv-table tbody tr:last-child td { border-bottom: none; }
.rv-table tbody tr:hover td {
    background: rgba(37,99,235,0.03) !important;
}
[data-bs-theme="dark"] .rv-table tbody tr:hover td {
    background: rgba(96,165,250,0.05) !important;
}

.rv-nilai {
    font-weight: 700;
    color: #2563eb !important;
}
[data-bs-theme="dark"] .rv-nilai { color: #60a5fa !important; }

.rv-empty {
    text-align: center;
    padding: 48px 20px;
    color: var(--ri-text-muted);
}
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function sortByTotal(tableId) {
        const tbody = document.querySelector('#' + tableId + ' tbody');
        if (!tbody) return;
        [...tbody.querySelectorAll('tr')]
            .sort((a, b) => (parseFloat(b.cells[4]?.textContent) || 0) - (parseFloat(a.cells[4]?.textContent) || 0))
            .forEach((row, i) => { row.cells[0].textContent = i + 1; tbody.appendChild(row); });
    }

    function exportCSV(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const csv = [...table.querySelectorAll('tr')].map(row =>
            [...row.querySelectorAll('th, td')]
                .map(c => `"${c.innerText.trim().replace(/"/g, '""')}"`)
                .join(',')
        ).join('\n');
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
        a.download = filename + '.csv';
        a.click();
    }

    document.getElementById('btnRangkingUmum')?.addEventListener('click',    () => sortByTotal('tableUmum'));
    document.getElementById('btnRangkingPelajar')?.addEventListener('click', () => sortByTotal('tablePelajar'));
    document.getElementById('btnExcelUmum')?.addEventListener('click',       () => exportCSV('tableUmum',    'nominasi-umum'));
    document.getElementById('btnExcelPelajar')?.addEventListener('click',    () => exportCSV('tablePelajar', 'nominasi-pelajar'));

});
</script>
@endpush