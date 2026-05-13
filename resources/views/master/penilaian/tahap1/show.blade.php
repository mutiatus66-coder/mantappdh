@extends('index')

@section('content')

{{-- Flash Message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(245,158,11,0.10); border:1px solid rgba(245,158,11,0.3); color:#92400e; margin: 0 20px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="t1-detail-container">

    {{-- ── BREADCRUMB HEADER ── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <p class="t1-sub-label mb-1">Sub Event :</p>
            <h4 class="t1-sub-title mb-0">{{ $subEvent['sub_event'] }}</h4>
        </div>
        <a href="{{ route('penilaian.tahap1.index') }}" class="btn btn-kembali">
            Kembali
        </a>
    </div>

    {{-- ── TABS ── --}}
    <ul class="nav t1-tabs mb-4" id="tabNominator" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="t1-tab-btn active"
                    id="tab-umum" data-bs-toggle="tab" data-bs-target="#panel-umum"
                    type="button" role="tab">Umum</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="t1-tab-btn"
                    id="tab-pelajar" data-bs-toggle="tab" data-bs-target="#panel-pelajar"
                    type="button" role="tab">Pelajar</button>
        </li>
    </ul>

    <div class="tab-content" id="tabNominatorContent">

        {{-- ─────────── TAB UMUM ─────────── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">
            <div class="t1-table-card">

                <div class="t1-table-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="t1-table-title mb-0">Verifikasi Umum</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-t1-rangking" data-table="tableUmum">Rangking</button>
                        <button class="btn btn-t1-excel" data-table="tableUmum" data-filename="verifikasi-umum">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                {{-- Simpan bar --}}
                <div class="t1-simpan-bar mb-3" id="simpanBarUmum" style="display:none;">
                    <span class="t1-simpan-info">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span class="simpan-count">0</span> inovasi dipilih untuk lolos ke Tahap 2
                    </span>
                    <button class="btn btn-t1-simpan" data-group="umum">Simpan</button>
                </div>

                <div class="table-responsive">
                    <table class="table t1-table align-middle mb-0" id="tableUmum">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">
                                    <input type="checkbox" class="t1-checkbox chk-all" data-group="umum">
                                </th>
                                <th class="text-center" width="50">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" width="100">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" width="85">{{ $p['nama_singkat'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nominasiUmum as $i => $nom)
                            <tr data-id="{{ $nom['id'] }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="t1-checkbox chk-row"
                                        data-group="umum"
                                        data-id="{{ $nom['id'] }}">
                                </td>
                                <td class="text-center row-no">{{ $i + 1 }}</td>
                                <td>{{ $nom['inovator'] }}</td>
                                <td>{{ $nom['nama_inovasi'] }}</td>
                                <td class="text-center fw-bold t1-nilai">
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
                                <td colspan="{{ 5 + count($penilai) }}" class="text-center py-5 t1-empty-row">
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
            <div class="t1-table-card">

                <div class="t1-table-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="t1-table-title mb-0">Verifikasi Pelajar</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-t1-rangking" data-table="tablePelajar">Rangking</button>
                        <button class="btn btn-t1-excel" data-table="tablePelajar" data-filename="verifikasi-pelajar">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                {{-- Simpan bar --}}
                <div class="t1-simpan-bar mb-3" id="simpanBarPelajar" style="display:none;">
                    <span class="t1-simpan-info">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span class="simpan-count">0</span> inovasi dipilih untuk lolos ke Tahap 2
                    </span>
                    <button class="btn btn-t1-simpan" data-group="pelajar">Simpan</button>
                </div>

                <div class="table-responsive">
                    <table class="table t1-table align-middle mb-0" id="tablePelajar">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">
                                    <input type="checkbox" class="t1-checkbox chk-all" data-group="pelajar">
                                </th>
                                <th class="text-center" width="50">No</th>
                                <th>Inovator</th>
                                <th>Nama Inovasi</th>
                                <th class="text-center" width="100">Total Nilai</th>
                                @foreach($penilai as $p)
                                <th class="text-center" width="85">{{ $p['nama_singkat'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nominasiPelajar as $i => $nom)
                            <tr data-id="{{ $nom['id'] }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="t1-checkbox chk-row"
                                        data-group="pelajar"
                                        data-id="{{ $nom['id'] }}">
                                </td>
                                <td class="text-center row-no">{{ $i + 1 }}</td>
                                <td>{{ $nom['inovator'] }}</td>
                                <td>{{ $nom['nama_inovasi'] }}</td>
                                <td class="text-center fw-bold t1-nilai">
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
                                <td colspan="{{ 5 + count($penilai) }}" class="text-center py-5 t1-empty-row">
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
.t1-detail-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.10);
    padding: 28px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}

/* ── Header ── */
.t1-sub-label {
    font-size: 0.80rem;
    color: var(--ri-text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}
.t1-sub-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--ri-text-primary);
}

/* ── Btn Kembali ── */
.btn-kembali {
    background: linear-gradient(135deg, #0C4C8A, #142D54) !important;
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
    gap: 6px;
}
.btn-kembali:hover { opacity: 0.88; color: #fff !important; }

/* ── Tabs ── */
.t1-tabs {
    border-bottom: 2px solid var(--ri-border);
    gap: 4px;
}
.t1-tab-btn {
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
.t1-tab-btn.active,
.t1-tab-btn:focus {
    color: #0C4C8A;
    border-bottom-color: #0C4C8A;
    outline: none;
    background: transparent;
}
[data-bs-theme="dark"] .t1-tab-btn.active {
    color: #378ADD;
    border-bottom-color: #378ADD;
}

/* ── Table card ── */
.t1-table-card {
    background: var(--ri-card-bg);
    border: 1px solid var(--ri-border);
    border-radius: 10px;
    padding: 20px 22px;
    transition: background 0.2s;
}
.t1-table-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ri-text-primary);
}

/* ── Simpan bar ── */
.t1-simpan-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(12,76,138,0.06);
    border: 1px solid rgba(12,76,138,0.20);
    border-radius: 8px;
    padding: 10px 16px;
    gap: 12px;
    flex-wrap: wrap;
}
[data-bs-theme="dark"] .t1-simpan-bar {
    background: rgba(55,138,221,0.06);
    border-color: rgba(55,138,221,0.18);
}
.t1-simpan-info {
    font-size: 0.84rem;
    font-weight: 600;
    color: #92400e;
}
[data-bs-theme="dark"] .t1-simpan-info { color: #fbbf24; }

/* ── Btn Simpan ── */
.btn-t1-simpan {
    background: #00838F !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    font-size: 0.82rem;
    border-radius: 7px;
    padding: 7px 16px;
    transition: background 0.15s;
}
.btn-t1-simpan:hover { background: #006064 !important; }

/* ── Checkbox ── */
.t1-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #0C4C8A;
}

/* ── Table ── */
.t1-table {
    border: 2px solid var(--ri-table-border-outer) !important;
    border-radius: 8px;
    overflow: hidden;
}
.t1-table th {
    background: var(--ri-table-head-bg) !important;
    padding: 13px 12px;
    border-bottom: 2px solid var(--ri-table-border-header) !important;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ri-text-muted) !important;
}
.t1-table td {
    padding: 12px 12px;
    border-bottom: 1px solid var(--ri-table-border-row) !important;
    color: var(--ri-text-primary) !important;
    background: var(--ri-table-row-bg) !important;
    font-size: 0.875rem;
    transition: background 0.2s;
}
.t1-table tr:hover td { background: var(--ri-table-row-hover) !important; }

/* ── Row lolos highlight ── */
.row-lolos td {
    background: rgba(12,76,138,0.05) !important;
}
[data-bs-theme="dark"] .row-lolos td {
    background: rgba(55,138,221,0.05) !important;
}

/* ── Total Nilai ── */
.t1-table td.t1-nilai { color: #0C4C8A !important; font-weight: 700; }
[data-bs-theme="dark"] .t1-table td.t1-nilai { color: #378ADD !important; }

/* ── Btn Rangking ── */
.btn-t1-rangking {
    background: linear-gradient(135deg, #0C4C8A, #142D54) !important;
    color: #fff !important;
    border: none;
    font-weight: 600;
    font-size: 0.82rem;
    border-radius: 7px;
    padding: 7px 14px;
    transition: opacity 0.15s;
}
.btn-t1-rangking:hover { opacity: 0.85; }

/* ── Btn Excel ── */
.btn-t1-excel {
    background: var(--ri-table-head-bg) !important;
    color: var(--ri-text-primary) !important;
    border: 1px solid var(--ri-border) !important;
    font-weight: 600;
    font-size: 0.82rem;
    border-radius: 7px;
    padding: 7px 14px;
    transition: background 0.15s, color 0.15s;
}
.btn-t1-excel:hover {
    background: #65A605 !important;
    color: #fff !important;
    border-color: #65A605 !important;
}

/* ── Empty row ── */
.t1-empty-row {
    color: var(--ri-text-muted) !important;
    background: var(--ri-table-row-bg) !important;
}

/* ── Toast ── */
.t1-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    padding: 12px 20px;
    border-radius: 9px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    opacity: 0;
    transform: translateY(12px);
    transition: opacity 0.25s, transform 0.25s;
    box-shadow: 0 4px 20px rgba(0,0,0,0.18);
    pointer-events: none;
}
.t1-toast.show   { opacity: 1; transform: translateY(0); }
.t1-toast.success { background: #00838F; color: #fff; }
.t1-toast.error   { background: #A32D2D; color: #fff; }
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const SIMPAN_URL = '{{ route("penilaian.tahap1.simpan", $subEvent["id"]) }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ─────────────────────────────────────────────────────────────────────
    // 1. GROUP STATE
    //    Satu objek per grup (umum / pelajar) yang menyimpan referensi DOM
    // ─────────────────────────────────────────────────────────────────────
    function buildGroup(name) {
        return {
            name,
            get rows()    { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]`)] },
            get checked() { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]:checked`)] },
            get checkAll(){ return document.querySelector(`.chk-all[data-group="${name}"]`) },
            get bar()     { return document.getElementById(`simpanBar${cap(name)}`) },
            get count()   { return document.querySelector(`#simpanBar${cap(name)} .simpan-count`) },
        };
    }

    function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    const groups = { umum: buildGroup('umum'), pelajar: buildGroup('pelajar') };

    // ─────────────────────────────────────────────────────────────────────
    // 2. SYNC UI  —  row highlight + simpan bar + checkAll state
    //    Dipanggil setiap kali ada perubahan. Parameter `fromInit` = true
    //    supaya simpan bar tidak muncul saat halaman pertama kali dibuka.
    // ─────────────────────────────────────────────────────────────────────
    function syncUI(g, fromInit = false) {
        const total   = g.rows.length;
        const n       = g.checked.length;

        // Row highlight
        g.rows.forEach(chk => {
            chk.closest('tr').classList.toggle('row-lolos', chk.checked);
        });

        // checkAll indicator
        if (g.checkAll) {
            g.checkAll.indeterminate = n > 0 && n < total;
            g.checkAll.checked       = total > 0 && n === total;
        }

        // Simpan bar — jangan tampil saat init
        if (!fromInit) {
            g.bar.style.display = n > 0 ? 'flex' : 'none';
        }
        g.count.textContent = n;
    }

    // ─────────────────────────────────────────────────────────────────────
    // 3. INIT  —  sinkronkan state dari server tanpa tampilkan simpan bar
    // ─────────────────────────────────────────────────────────────────────
    Object.values(groups).forEach(g => syncUI(g, true));

    // ─────────────────────────────────────────────────────────────────────
    // 4. EVENT: checkbox per-baris
    // ─────────────────────────────────────────────────────────────────────
    document.querySelectorAll('.chk-row').forEach(chk => {
        chk.addEventListener('change', function () {
            syncUI(groups[this.dataset.group]);
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // 5. EVENT: checkAll
    // ─────────────────────────────────────────────────────────────────────
    document.querySelectorAll('.chk-all').forEach(chkAll => {
        chkAll.addEventListener('change', function () {
            const g = groups[this.dataset.group];
            g.rows.forEach(chk => chk.checked = this.checked);
            syncUI(g);
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // 6. EVENT: tombol Simpan
    // ─────────────────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-t1-simpan').forEach(btn => {
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

    // ─────────────────────────────────────────────────────────────────────
    // 7. EVENT: tombol Rangking  —  sort tbody by Total Nilai (desc)
    // ─────────────────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-t1-rangking').forEach(btn => {
        btn.addEventListener('click', function () {
            const tbody = document.querySelector(`#${this.dataset.table} tbody`);
            if (!tbody) return;

            [...tbody.querySelectorAll('tr')]
                .sort((a, b) => {
                    const va = parseFloat(a.cells[4]?.textContent.trim()) || 0;
                    const vb = parseFloat(b.cells[4]?.textContent.trim()) || 0;
                    return vb - va;
                })
                .forEach((row, i) => {
                    const noCell = row.querySelector('.row-no');
                    if (noCell) noCell.textContent = i + 1;
                    tbody.appendChild(row);
                });
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // 8. EVENT: tombol Excel  —  export CSV (skip kolom checkbox)
    // ─────────────────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-t1-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const table = document.getElementById(this.dataset.table);
            if (!table) return;

            const csv = [...table.querySelectorAll('tr')].map(row =>
                [...row.querySelectorAll('th, td')]
                    .slice(1)   // skip kolom checkbox
                    .map(c => `"${c.innerText.trim().replace(/"/g, '""')}"`)
                    .join(',')
            ).join('\n');

            const a    = document.createElement('a');
            a.href     = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = `${this.dataset.filename}.csv`;
            a.click();
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // 9. TOAST helper
    // ─────────────────────────────────────────────────────────────────────
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `t1-toast ${type}`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'} me-2"></i>${msg}`;
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