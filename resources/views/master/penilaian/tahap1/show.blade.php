@extends('index')

@section('content')

{{-- Flash Message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.2); color:#1e40af; margin: 0 24px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="rv-container">

    {{-- ── HEADER ── --}}
    <div class="rv-page-header">
        <div>
            <p class="rv-sub-label">Sub Event</p>
            <h3 class="rv-page-title">{{ $subEvent['sub_event'] }}</h3>
        </div>
        <a href="{{ route('penilaian.tahap1.index') }}" class="btn-rv-back">
            <i class="bi bi-arrow-left"></i> Kembali
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
                        <button class="btn-rv-action btn-rv-rank" data-table="tableUmum">
                            <i class="bi bi-sort-numeric-down me-1"></i>Rangking
                        </button>
                        <button class="btn-rv-action btn-rv-excel" data-table="tableUmum" data-filename="verifikasi-umum">
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
                        <button class="btn-rv-action btn-rv-rank" data-table="tablePelajar">
                            <i class="bi bi-sort-numeric-down me-1"></i>Rangking
                        </button>
                        <button class="btn-rv-action btn-rv-excel" data-table="tablePelajar" data-filename="verifikasi-pelajar">
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


@push('styles')
<style>
/* ═══════════════════════════════════════════
   SHARED RV (Rekap Nilai) DESIGN SYSTEM
   ─ Dipakai di Tahap 1, Tahap 2, dan Rekap
═══════════════════════════════════════════ */

/* ── Container ── */
.rv-container {
    padding: 28px 24px;
    margin: 20px;
}

/* ── Page header ── */
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

/* ── Btn Kembali ── */
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

/* ── Tabs ── */
.rv-tabs-wrap {
    margin-bottom: 20px;
}
.rv-tabs {
    border-bottom: 2px solid var(--ri-border);
    gap: 4px;
}
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

/* ── Card ── */
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

/* ── Action buttons ── */
.btn-rv-action {
    display: inline-flex;
    align-items: center;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 60px;
    padding: 7px 16px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-rv-rank {
    background: linear-gradient(105deg, #2563eb, #1e40af);
    color: #fff;
    box-shadow: 0 2px 6px rgba(37,99,235,0.2);
}
.btn-rv-rank:hover {
    background: linear-gradient(105deg, #1d4ed8, #1e3a8a);
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(37,99,235,0.25);
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

/* ── Simpan bar ── */
.rv-simpan-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: rgba(37,99,235,0.06);
    border: 1px solid rgba(37,99,235,0.18);
    border-radius: 16px;
    padding: 12px 18px;
    margin-bottom: 16px;
}
[data-bs-theme="dark"] .rv-simpan-bar {
    background: rgba(96,165,250,0.06);
    border-color: rgba(96,165,250,0.18);
}
.rv-simpan-info {
    font-size: 0.84rem;
    font-weight: 600;
    color: #1e40af;
}
[data-bs-theme="dark"] .rv-simpan-info { color: #93c5fd; }

.btn-rv-simpan {
    background: linear-gradient(105deg, #2563eb, #1e40af);
    color: #fff;
    border: none;
    font-weight: 600;
    font-size: 0.82rem;
    border-radius: 60px;
    padding: 8px 18px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(37,99,235,0.2);
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-rv-simpan:hover {
    background: linear-gradient(105deg, #1d4ed8, #1e3a8a);
    transform: scale(1.02);
    box-shadow: 0 4px 10px rgba(37,99,235,0.25);
}

/* ── Checkbox ── */
.rv-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #2563eb;
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
.rv-table tbody tr:last-child td {
    border-bottom: none;
}
.rv-table tbody tr:hover td {
    background: rgba(37,99,235,0.03) !important;
}
[data-bs-theme="dark"] .rv-table tbody tr:hover td {
    background: rgba(96,165,250,0.05) !important;
}

/* ── Row lolos highlight ── */
.row-lolos td {
    background: rgba(37,99,235,0.04) !important;
}
[data-bs-theme="dark"] .row-lolos td {
    background: rgba(96,165,250,0.06) !important;
}

/* ── Total Nilai cell ── */
.rv-nilai {
    font-weight: 700;
    color: #2563eb !important;
}
[data-bs-theme="dark"] .rv-nilai { color: #60a5fa !important; }

/* ── Empty state ── */
.rv-empty {
    text-align: center;
    padding: 48px 20px;
    color: var(--ri-text-muted);
}

/* ── Toast ── */
.rv-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    padding: 12px 20px;
    border-radius: 60px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    opacity: 0;
    transform: translateY(12px);
    transition: opacity 0.25s, transform 0.25s;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    pointer-events: none;
}
.rv-toast.show   { opacity: 1; transform: translateY(0); }
.rv-toast.success { background: linear-gradient(105deg, #2563eb, #1e40af); color: #fff; }
.rv-toast.error   { background: linear-gradient(105deg, #dc2626, #991b1b); color: #fff; }
</style>
@endpush


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