@extends('index')

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="all-container">

    {{-- ── HEADER ── --}}
    <div class="rv-page-header">
        <div>
            <p class="rv-sub-label">Sub Event</p>
            <h3 class="rv-page-title">{{ $subEvent['sub_event'] }}</h3>
        </div>
        <a href="{{ route('penilaian.tahap2.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i>Kembali
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

        {{-- ── TAB UMUM ── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">
            @include('master.penilaian.tahap2.panel', [
                'group'    => 'umum',
                'title'    => 'Nominator Umum',
                'tableId'  => 'tableUmum',
                'filename' => 'nominasi-umum',
                'nominasi' => $nominasiUmum,
                'penilai'  => $penilai,
            ])
        </div>

        {{-- ── TAB PELAJAR ── --}}
        <div class="tab-pane fade" id="panel-pelajar" role="tabpanel">
            @include('master.penilaian.tahap2.panel', [
                'group'    => 'pelajar',
                'title'    => 'Nominator Pelajar',
                'tableId'  => 'tablePelajar',
                'filename' => 'nominasi-pelajar',
                'nominasi' => $nominasiPelajar,
                'penilai'  => $penilai,
            ])
        </div>

    </div>
</div>

@endsection

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
        a.href     = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
        a.download = filename + '.csv';
        a.click();
    }

    document.querySelectorAll('.btn-rv-rank').forEach(btn => {
        btn.addEventListener('click', function () { sortByTotal(this.dataset.table); });
    });

    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () { exportCSV(this.dataset.table, this.dataset.filename); });
    });

});
</script>
@endpush