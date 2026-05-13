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
                        <button class="btn btn-t1-rangking" id="btnRangkingUmum">
                            Rangking
                        </button>
                        <button class="btn btn-t1-excel" id="btnExcelUmum">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                {{-- Simpan bar --}}
                <div class="t1-simpan-bar mb-3" id="simpanBarUmum" style="display:none !important;">
                    <span class="t1-simpan-info">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span id="simpanCountUmum">0</span> inovasi dipilih untuk lolos ke Tahap 2
                    </span>
                    <button class="btn btn-t1-simpan" id="btnSimpanUmum">
                        Simpan
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table t1-table align-middle mb-0" id="tableUmum">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">
                                    <input type="checkbox" class="t1-checkbox" id="checkAllUmum">
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
                            <tr data-id="{{ $nom['id'] }}" class="{{ ($nom['lolos'] ?? false) ? 'row-lolos' : '' }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="t1-checkbox chk-umum"
                                        data-id="{{ $nom['id'] }}"
                                        {{ ($nom['lolos'] ?? false) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">{{ $i + 1 }}</td>
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
                        <button class="btn btn-t1-rangking" id="btnRangkingPelajar">
                            Rangking
                        </button>
                        <button class="btn btn-t1-excel" id="btnExcelPelajar">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                        </button>
                    </div>
                </div>

                {{-- Simpan bar --}}
                <div class="t1-simpan-bar mb-3" id="simpanBarPelajar" style="display:none !important;">
                    <span class="t1-simpan-info">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span id="simpanCountPelajar">0</span> inovasi dipilih untuk lolos ke Tahap 2
                    </span>
                    <button class="btn btn-t1-simpan" id="btnSimpanPelajar">
                        Simpan
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table t1-table align-middle mb-0" id="tablePelajar">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">
                                    <input type="checkbox" class="t1-checkbox" id="checkAllPelajar">
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
                            <tr data-id="{{ $nom['id'] }}" class="{{ ($nom['lolos'] ?? false) ? 'row-lolos' : '' }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                           class="t1-checkbox chk-pelajar"
                                           data-id="{{ $nom['id'] }}"
                                           {{ ($nom['lolos'] ?? false) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">{{ $i + 1 }}</td>
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

/* ── Btn Kembali — navy, sinkron Sub Event btn-gold ── */
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
    display: flex !important;
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

/* ── Btn Simpan — teal, sinkron Sub Event ── */
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
.t1-table td:not(.t1-nilai) {
    color: var(--ri-text-primary) !important;
}
.t1-table td a,
.t1-table td a:hover {
    color: var(--ri-text-primary) !important;
    text-decoration: none;
}
.t1-table tr:hover td { background: var(--ri-table-row-hover) !important; }

/* ── Row lolos highlight ── */
.row-lolos td {
    background: rgba(12,76,138,0.05) !important;
}
[data-bs-theme="dark"] .row-lolos td {
    background: rgba(55,138,221,0.05) !important;
}

/* ── Nilai — hanya td, bukan th ── */
.t1-table td.t1-nilai { color: #0C4C8A !important; font-weight: 700; }
[data-bs-theme="dark"] .t1-table td.t1-nilai { color: #378ADD !important; }

/* ── Btn Rangking — navy, sinkron Sub Event btn-gold ── */
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

/* ── Btn Excel — sinkron Sub Event ── */
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

.t1-empty-row {
    color: var(--ri-text-muted) !important;
    background: var(--ri-table-row-bg) !important;
}
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Checkbox logic ──────────────────────────────────────────
    function setupCheckbox(groupClass, checkAllId, simpanBarId, countId) {
        const checkAll  = document.getElementById(checkAllId);
        const bar       = document.getElementById(simpanBarId);
        const countEl   = document.getElementById(countId);

        function updateBar() {
            const checked = document.querySelectorAll('.' + groupClass + ':checked');
            const n = checked.length;
            countEl.textContent = n;
            if (n > 0) {
                bar.style.removeProperty('display');
                bar.style.display = 'flex';
            } else {
                bar.style.display = 'none';
            }
            const all = document.querySelectorAll('.' + groupClass);
            checkAll.indeterminate = n > 0 && n < all.length;
            checkAll.checked = n > 0 && n === all.length;
        }

        document.querySelectorAll('.' + groupClass).forEach(chk => {
            chk.addEventListener('change', function () {
                this.closest('tr').classList.toggle('row-lolos', this.checked);
                updateBar();
            });
        });

        checkAll?.addEventListener('change', function () {
            document.querySelectorAll('.' + groupClass).forEach(chk => {
                chk.checked = this.checked;
                chk.closest('tr').classList.toggle('row-lolos', this.checked);
            });
            updateBar();
        });

        updateBar();
    }

    setupCheckbox('chk-umum',    'checkAllUmum',    'simpanBarUmum',    'simpanCountUmum');
    setupCheckbox('chk-pelajar', 'checkAllPelajar', 'simpanBarPelajar', 'simpanCountPelajar');

    // ── Simpan ──────────────────────────────────────────────────
    function setupSimpan(btnId, groupClass, kategori) {
        document.getElementById(btnId)?.addEventListener('click', function () {
            const ids = Array.from(document.querySelectorAll('.' + groupClass + ':checked'))
                            .map(c => c.dataset.id);

            fetch('{{ route("penilaian.tahap1.simpan", $subEvent["id"]) }}', {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                },
                body: JSON.stringify({ kategori, ids })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('Data berhasil disimpan!', 'success');
                } else {
                    showToast('Gagal menyimpan data.', 'error');
                }
            })
            .catch(() => showToast('Terjadi kesalahan.', 'error'));
        });
    }

    setupSimpan('btnSimpanUmum',    'chk-umum',    'umum');
    setupSimpan('btnSimpanPelajar', 'chk-pelajar', 'pelajar');

    // ── Toast ───────────────────────────────────────────────────
    function showToast(msg, type) {
        const t = document.createElement('div');
        t.className = 't1-toast t1-toast-' + type;
        t.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'x-circle-fill') + ' me-2"></i>' + msg;
        document.body.appendChild(t);
        setTimeout(() => t.classList.add('t1-toast-show'), 10);
        setTimeout(() => { t.classList.remove('t1-toast-show'); setTimeout(() => t.remove(), 300); }, 2800);
    }

    // ── Sort by Total Nilai ─────────────────────────────────────
    function sortTableByTotal(tableId) {
        const tbody = document.querySelector('#' + tableId + ' tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            const va = parseFloat(a.cells[4]?.textContent.trim()) || 0;
            const vb = parseFloat(b.cells[4]?.textContent.trim()) || 0;
            return vb - va;
        });
        rows.forEach((r, i) => { r.cells[1].textContent = i + 1; tbody.appendChild(r); });
    }

    document.getElementById('btnRangkingUmum')?.addEventListener('click',    () => sortTableByTotal('tableUmum'));
    document.getElementById('btnRangkingPelajar')?.addEventListener('click', () => sortTableByTotal('tablePelajar'));

    // ── Export CSV ──────────────────────────────────────────────
    function exportTableToCSV(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        let csv = [];
        table.querySelectorAll('tr').forEach(row => {
            const cols = Array.from(row.querySelectorAll('th, td'))
                .slice(1)
                .map(c => '"' + c.innerText.trim().replace(/"/g, '""') + '"');
            csv.push(cols.join(','));
        });
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename + '.csv';
        a.click();
    }

    document.getElementById('btnExcelUmum')?.addEventListener('click',    () => exportTableToCSV('tableUmum',    'verifikasi-umum'));
    document.getElementById('btnExcelPelajar')?.addEventListener('click', () => exportTableToCSV('tablePelajar', 'verifikasi-pelajar'));

});
</script>

<style>
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
}
.t1-toast-show { opacity: 1; transform: translateY(0); }
.t1-toast-success { background: #00838F; color: #fff; }
.t1-toast-error   { background: #A32D2D; color: #fff; }
</style>
@endpush