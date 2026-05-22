@extends('index')
<link href="{{ asset('template.demo6/demo6/assets/css/setel.css') }}" rel="stylesheet">
@section('content')

{{-- Flash Message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.2); color:#1e40af; margin: 0 24px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="all-container">

    {{-- ── HEADER ── --}}
    <div class="rv-page-header">
        <div>
            <p class="rv-sub-label">Sub Event</p>
            <h3 class="rv-page-title">{{ $subEvent['sub_event'] }}</h3>
        </div>
        <a href="{{ route('penilaian.tahap1.index') }}" class="btn btn-primary">
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
                    <h6 class="rv-card-title">Verifikasi Umum</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-table="tableUmum">
                            <i class="bi bi-sort-numeric-down me-1"></i>Rangking
                        </button>
                        <button class="btn btn-info" data-table="tableUmum" data-filename="verifikasi-umum">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                {{-- Simpan bar --}}
                <div class="rv-simpan-bar" id="simpanBarUmum" style="display:none;">
                    <span class="rv-simpan-info">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span class="simpan-count">0</span> inovasi dipilih untuk lolos ke Tahap 2
                    </span>
                    <button class="btn-rv-simpan" data-group="umum">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="rv-table" id="tableUmum">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:48px">
                                    <input type="checkbox" class="rv-checkbox chk-all" data-group="umum">
                                </th>
                                <th class="text-center" style="width:48px">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" style="width:100px">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" style="width:84px">{{ $p['nama_singkat'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nominasiUmum as $i => $nom)
                            <tr data-id="{{ $nom['id'] }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="rv-checkbox chk-row"
                                        data-group="umum"
                                        data-id="{{ $nom['id'] }}">
                                </td>
                                <td class="text-center row-no">{{ $i + 1 }}</td>
                                <td>{{ $nom['inovator'] }}</td>
                                <td>{{ $nom['nama_inovasi'] }}</td>
                                <td class="text-center rv-nilai">
                                    {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 2) : '0.00' }}
                                </td>
                                @foreach($penilai as $p)
                                <td class="text-center">
                                    {{ isset($nom['nilai'][$p['id']]) ? number_format($nom['nilai'][$p['id']], 2) : '' }}
                                </td>
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
                    <h6 class="rv-card-title">Verifikasi Pelajar</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-table="tablePelajar">
                            <i class="bi bi-sort-numeric-down me-1"></i>Rangking
                        </button>
                        <button class="btn btn-info" data-table="tablePelajar" data-filename="verifikasi-pelajar">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                {{-- Simpan bar --}}
                <div class="rv-simpan-bar" id="simpanBarPelajar" style="display:none;">
                    <span class="rv-simpan-info">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span class="simpan-count">0</span> inovasi dipilih untuk lolos ke Tahap 2
                    </span>
                    <button class="btn-rv-simpan" data-group="pelajar">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="rv-table" id="tablePelajar">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:48px">
                                    <input type="checkbox" class="rv-checkbox chk-all" data-group="pelajar">
                                </th>
                                <th class="text-center" style="width:48px">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" style="width:100px">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" style="width:84px">{{ $p['nama_singkat'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nominasiPelajar as $i => $nom)
                            <tr data-id="{{ $nom['id'] }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="rv-checkbox chk-row"
                                        data-group="pelajar"
                                        data-id="{{ $nom['id'] }}">
                                </td>
                                <td class="text-center row-no">{{ $i + 1 }}</td>
                                <td>{{ $nom['inovator'] }}</td>
                                <td>{{ $nom['nama_inovasi'] }}</td>
                                <td class="text-center rv-nilai">
                                    {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 2) : '0.00' }}
                                </td>
                                @foreach($penilai as $p)
                                <td class="text-center">
                                    {{ isset($nom['nilai'][$p['id']]) ? number_format($nom['nilai'][$p['id']], 2) : '' }}
                                </td>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const SIMPAN_URL = '{{ route("penilaian.tahap1.simpan", $subEvent["id"]) }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function buildGroup(name) {
        const cap = s => s.charAt(0).toUpperCase() + s.slice(1);
        return {
            name,
            get rows()    { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]`)] },
            get checked() { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]:checked`)] },
            get checkAll(){ return document.querySelector(`.chk-all[data-group="${name}"]`) },
            get bar()     { return document.getElementById(`simpanBar${cap(name)}`) },
            get count()   { return document.querySelector(`#simpanBar${cap(name)} .simpan-count`) },
        };
    }

    const groups = { umum: buildGroup('umum'), pelajar: buildGroup('pelajar') };

    function syncUI(g, fromInit = false) {
        const total = g.rows.length;
        const n     = g.checked.length;

        g.rows.forEach(chk => {
            chk.closest('tr').classList.toggle('row-lolos', chk.checked);
        });

        if (g.checkAll) {
            g.checkAll.indeterminate = n > 0 && n < total;
            g.checkAll.checked       = total > 0 && n === total;
        }

        if (!fromInit) g.bar.style.display = n > 0 ? 'flex' : 'none';
        g.count.textContent = n;
    }

    Object.values(groups).forEach(g => syncUI(g, true));

    document.querySelectorAll('.chk-row').forEach(chk => {
        chk.addEventListener('change', function () { syncUI(groups[this.dataset.group]); });
    });

    document.querySelectorAll('.chk-all').forEach(chkAll => {
        chkAll.addEventListener('change', function () {
            const g = groups[this.dataset.group];
            g.rows.forEach(chk => chk.checked = this.checked);
            syncUI(g);
        });
    });

    document.querySelectorAll('.btn-rv-simpan').forEach(btn => {
        btn.addEventListener('click', function () {
            const g   = groups[this.dataset.group];
            const ids = g.checked.map(c => c.dataset.id);

            fetch(SIMPAN_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ kategori: g.name, ids }),
            })
            .then(r => r.json())
            .then(data => toast(data.success ? 'Data berhasil disimpan!' : 'Gagal menyimpan data.', data.success ? 'success' : 'error'))
            .catch(()  => toast('Terjadi kesalahan.', 'error'));
        });
    });

    // Rangking — sort by Total Nilai (desc)
    document.querySelectorAll('.btn-rv-rank').forEach(btn => {
        btn.addEventListener('click', function () {
            const tbody = document.querySelector(`#${this.dataset.table} tbody`);
            if (!tbody) return;
            [...tbody.querySelectorAll('tr')]
                .sort((a, b) => (parseFloat(b.cells[4]?.textContent) || 0) - (parseFloat(a.cells[4]?.textContent) || 0))
                .forEach((row, i) => {
                    const no = row.querySelector('.row-no');
                    if (no) no.textContent = i + 1;
                    tbody.appendChild(row);
                });
        });
    });

    // Export CSV (skip checkbox col)
    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const table = document.getElementById(this.dataset.table);
            if (!table) return;
            const csv = [...table.querySelectorAll('tr')].map(row =>
                [...row.querySelectorAll('th, td')].slice(1)
                    .map(c => `"${c.innerText.trim().replace(/"/g, '""')}"`)
                    .join(',')
            ).join('\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = `${this.dataset.filename}.csv`;
            a.click();
        });
    });

    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `rv-toast ${type}`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'}"></i>${msg}`;
        document.body.appendChild(el);
        requestAnimationFrame(() => el.classList.add('show'));
        setTimeout(() => {
            el.classList.remove('show');
            el.addEventListener('transitionend', () => el.remove(), { once: true });
        }, 2800);
    }

});
</script>
@endpush