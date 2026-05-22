@extends('index')

@section('content')
<link href="{{ asset('template.demo6/demo6/assets/css/setel.css') }}" rel="stylesheet">
<div class="all-container">

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