@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="all-container">
    {{-- ── Header ── --}}
    <div class="rv-page-header">
        <div>
            <p class="rv-sub-label">Sub Event</p>
            <h3 class="rv-page-title">{{ $subEvent['sub_event'] }}</h3>
        </div>
        <a href="{{ route('penilaian.tahap1.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    {{-- ── Keterangan Status ── --}}
    <div class="alert alert-info d-flex gap-3 flex-wrap align-items-center mb-3" style="font-size:.875rem">
        <span><span class="badge bg-secondary me-1"><i class="bi bi-dash-circle"></i></span> Belum ada penilaian</span>
        <span><span class="badge bg-warning text-dark me-1"><i class="bi bi-hourglass-split"></i></span> Sebagian penilai sudah menilai</span>
        <span><span class="badge bg-success me-1"><i class="bi bi-check-circle"></i></span> Semua penilai sudah menilai — siap diloloskan</span>
    </div>

    {{-- ── Tabs ── --}}
    <div class="rv-tabs-wrap">
        <ul class="nav rv-tabs" id="tabNominator" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn active" id="tab-umum"
                        data-bs-toggle="tab" data-bs-target="#panel-umum"
                        type="button" role="tab">Umum</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn" id="tab-pelajar"
                        data-bs-toggle="tab" data-bs-target="#panel-pelajar"
                        type="button" role="tab">Pelajar</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="tabNominatorContent">
        {{-- ── Tab Umum ── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">
            @include('master.penilaian.tahap1.panel', [
                'group'                 => 'umum',
                'title'                 => 'Nominator Umum',
                'tableId'               => 'tableUmum',
                'filename'              => 'nominasi-umum',
                'nominasi'              => $nominasiUmum,
                'penilai'               => $penilai,
                'indikators'            => $indikators,
                'penilaiLogin'          => $penilaiLogin,
                'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
            ])
        </div>

        {{-- ── Tab Pelajar ── --}}
        <div class="tab-pane fade" id="panel-pelajar" role="tabpanel">
            @include('master.penilaian.tahap1.panel', [
                'group'                 => 'pelajar',
                'title'                 => 'Nominator Pelajar',
                'tableId'               => 'tablePelajar',
                'filename'              => 'nominasi-pelajar',
                'nominasi'              => $nominasiPelajar,
                'penilai'               => $penilai,
                'indikators'            => $indikators,
                'penilaiLogin'          => $penilaiLogin,
                'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
            ])
        </div>
    </div>
</div>

{{-- ── Semua modal dirender di luar tab (anti pane tersembunyi) ── --}}
@stack('penilaian-modals')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const NILAI_URL    = '{{ route("penilaian.tahap1.simpan.nilai", $subEvent["id"]) }}';
    const SIMPAN_URL   = '{{ route("penilaian.tahap1.simpan", $subEvent["id"]) }}';
    const CATATAN_BASE = '{{ url("penilaian/catatan") }}';
    const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const nilaiDb      = @json($nilaiLoginPerInovator ?? []);
    const penilaiLoginId = {{ $penilaiLogin?->id ?? 'null' }};

    const cap = s => s.charAt(0).toUpperCase() + s.slice(1);

    // ── Helper: tentukan group secara berlapis ──────────────────────────
    function resolveGroup(el) {
        return el.dataset.group
            ?? el.closest('[data-group]')?.dataset.group
            ?? el.closest('.tab-pane')?.id?.replace('panel-', '')
            ?? 'umum';
    }

    // ── Toast ───────────────────────────────────────────────────────────
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `ri-toast ri-toast-${type === 'success' ? 'success' : 'error'}`;
        el.innerHTML = `
            <span class="ri-toast-icon">
              <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'}"></i>
            </span>
            <span class="ri-toast-msg">${msg}</span>
            <button class="ri-toast-close" onclick="this.parentElement.remove()">
              <i class="bi bi-x-lg"></i>
            </button>`;
        document.body.appendChild(el);
        requestAnimationFrame(() => el.classList.add('ri-toast-show'));
        setTimeout(() => {
            el.classList.remove('ri-toast-show');
            setTimeout(() => el.remove(), 300);
        }, 3500);
    }

    // ── Update baris tabel setelah simpan nilai ─────────────────────────
    function updateRowAfterSave(usulanId, data) {
        const row = document.querySelector(`tr[data-id="${usulanId}"]`);
        if (!row) return;

        // Update total nilai
        if (data.total_nilai !== undefined) {
            const nilaiCell = row.querySelector('.rv-nilai');
            if (nilaiCell) {
                nilaiCell.dataset.nilai = data.total_nilai;
                nilaiCell.textContent   = data.total_nilai > 0
                    ? parseFloat(data.total_nilai).toFixed(2)
                    : '—';
            }
        }

        // Update nilai kolom penilai login
        if (data.nilai_penilai !== undefined && penilaiLoginId) {
            const penilaiCell = row.querySelector(`.rv-nilai-penilai[data-penilai-id="${penilaiLoginId}"]`);
            if (penilaiCell) {
                penilaiCell.textContent = parseFloat(data.nilai_penilai).toFixed(2);
            }
        }

        // Update status badge dan checkbox
        if (data.sudah_lengkap !== undefined) {
            row.dataset.sudahLengkap = data.sudah_lengkap ? '1' : '0';

            const statusCell = row.querySelector('td:nth-last-child(' + (penilaiLoginId ? '2' : '1') + ')');
            if (statusCell) {
                if (data.sudah_lengkap) {
                    statusCell.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lengkap</span>';
                } else {
                    statusCell.innerHTML = '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Sebagian</span>';
                }
            }

            const chk = row.querySelector('.chk-row');
            if (chk) {
                chk.disabled = !data.sudah_lengkap;
                chk.title    = data.sudah_lengkap ? 'Siap diloloskan' : 'Belum semua penilai menilai';
                if (!data.sudah_lengkap) chk.checked = false;
            }

            // Sync UI group setelah perubahan checkbox
            const group = resolveGroup(row);
            if (groups[group]) syncUI(groups[group]);
        }
    }

    // ── Modal Input Nilai Tahap 1 ───────────────────────────────────────
    let activeInovatorId = null;

    document.querySelectorAll('.btn-input-nilai').forEach(btn => {
        btn.addEventListener('click', function () {
            activeInovatorId = this.dataset.inovatorId;
            const group   = resolveGroup(this);
            const modalEl = document.getElementById('modalNilaiTahap1' + cap(group));
            if (!modalEl) return;

            const elNama = document.querySelector('.modal-inovator-nama-' + group);
            const elInov = document.querySelector('.modal-inovasi-nama-'  + group);
            if (elNama) elNama.textContent = this.dataset.inovator    ?? '';
            if (elInov) elInov.textContent = this.dataset.namaInovasi ?? '';

            const savedNilai = nilaiDb[activeInovatorId] ?? {};
            modalEl.querySelectorAll('.input-nilai-item').forEach(inp => {
                inp.value = savedNilai[inp.dataset.keteranganId] ?? '';
            });

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
    });

    document.querySelectorAll('.btn-simpan-nilai-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!activeInovatorId) return;
            const group   = resolveGroup(this);
            const modalEl = document.getElementById('modalNilaiTahap1' + cap(group));

            const nilai = {};
            let allFilled = true;
            modalEl.querySelectorAll('.input-nilai-item').forEach(inp => {
                if (inp.value !== '') {
                    nilai[inp.dataset.keteranganId] = parseInt(inp.value, 10);
                } else {
                    allFilled = false;
                }
            });

            if (Object.keys(nilai).length === 0) {
                toast('Isi minimal satu nilai terlebih dahulu.', 'error');
                return;
            }

            const orig = this.innerHTML;
            this.disabled  = true;
            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            fetch(NILAI_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ usulan_id: activeInovatorId, nilai }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toast('Nilai berhasil disimpan!', 'success');

                    // Update cache nilai lokal
                    nilaiDb[activeInovatorId] = Object.assign(nilaiDb[activeInovatorId] ?? {}, nilai);

                    // Update tampilan tabel
                    updateRowAfterSave(activeInovatorId, data);

                    bootstrap.Modal.getInstance(modalEl)?.hide();
                } else {
                    toast(data.message ?? 'Gagal menyimpan nilai.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { this.disabled = false; this.innerHTML = orig; });
        });
    });

    // ── Checkbox lolos ──────────────────────────────────────────────────
    function buildGroup(name) {
        return {
            name,
            get rows()     { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]`)]; },
            // Hanya baris yang TIDAK disabled yang ikut hitungan
            get rowsEnabled() { return this.rows.filter(c => !c.disabled); },
            get checked()  { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]:checked`)]; },
            get checkAll() { return document.querySelector(`.chk-all[data-group="${name}"]`); },
            get bar()      { return document.getElementById('simpanBar' + cap(name)); },
            get count()    { return document.querySelector('#simpanBar' + cap(name) + ' .simpan-count'); },
        };
    }
    const groups = { umum: buildGroup('umum'), pelajar: buildGroup('pelajar') };

    function syncUI(g) {
        const total   = g.rowsEnabled.length;
        const n       = g.checked.length;
        g.rows.forEach(chk => chk.closest('tr').classList.toggle('row-lolos', chk.checked));
        if (g.checkAll) {
            g.checkAll.indeterminate = n > 0 && n < total;
            g.checkAll.checked       = total > 0 && n === total;
        }
        if (g.bar)   g.bar.style.display = n > 0 ? 'flex' : 'none';
        if (g.count) g.count.textContent = n;
    }
    Object.values(groups).forEach(g => syncUI(g));

    document.querySelectorAll('.chk-row').forEach(chk =>
        chk.addEventListener('change', function () { syncUI(groups[this.dataset.group]); })
    );
    document.querySelectorAll('.chk-all').forEach(chkAll =>
        chkAll.addEventListener('change', function () {
            const g = groups[this.dataset.group];
            // Hanya toggle yang tidak disabled
            g.rowsEnabled.forEach(chk => chk.checked = this.checked);
            syncUI(g);
        })
    );

    // ── Simpan lolos ────────────────────────────────────────────────────
    document.querySelectorAll('.btn-rv-simpan').forEach(btn => {
        btn.addEventListener('click', function () {
            const g   = groups[this.dataset.group];
            const ids = g.checked.map(c => c.dataset.id);
            if (ids.length === 0) { toast('Pilih minimal 1 inovasi terlebih dahulu.', 'error'); return; }

            const orig = this.innerHTML;
            btn.disabled  = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            fetch(SIMPAN_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ kategori: g.name, ids }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toast('Data berhasil disimpan! Halaman akan direfresh...', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    // Tampilkan pesan error (termasuk "belum lengkap")
                    toast(data.message ?? 'Gagal menyimpan data.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { btn.disabled = false; btn.innerHTML = orig; });
        });
    });

    // ── Catatan Penilai ─────────────────────────────────────────────────
    let activeUsulanId = null;

    document.querySelectorAll('.btn-catatan').forEach(btn => {
        btn.addEventListener('click', function () {
            activeUsulanId = this.dataset.usulanId;
            const group    = resolveGroup(this);

            const elInovator = document.querySelector('.modal-catatan-inovator-' + group);
            const elInovasi  = document.querySelector('.modal-catatan-inovasi-'  + group);
            if (elInovator) elInovator.textContent = this.dataset.inovator    ?? '';
            if (elInovasi)  elInovasi.textContent  = this.dataset.namaInovasi ?? '';

            const textarea = document.querySelector('.textarea-catatan-' + group);
            if (textarea) textarea.value = '';

            fetch(`${CATATAN_BASE}/${activeUsulanId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            })
            .then(r => r.json())
            .then(data => { if (textarea) textarea.value = data.catatan ?? ''; })
            .catch(() => {});

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('modalCatatan' + cap(group))
            ).show();
        });
    });

    document.querySelectorAll('.btn-simpan-catatan').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!activeUsulanId) return;
            const group   = resolveGroup(this);
            const catatan = document.querySelector('.textarea-catatan-' + group)?.value.trim();
            if (!catatan) { toast('Catatan tidak boleh kosong.', 'error'); return; }

            const orig = this.innerHTML;
            this.disabled  = true;
            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            fetch(`${CATATAN_BASE}/${activeUsulanId}`, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ catatan }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toast('Catatan berhasil disimpan!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalCatatan' + cap(group)))?.hide();
                } else {
                    toast(data.message ?? 'Gagal menyimpan catatan.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { this.disabled = false; this.innerHTML = orig; });
        });
    });

    // ── Rangking ─────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-rv-rank').forEach(btn => {
        btn.addEventListener('click', function () {
            const tbody = document.querySelector('#' + this.dataset.table + ' tbody');
            if (!tbody) return;
            const rows = [...tbody.querySelectorAll('tr')].filter(row => row.querySelector('.rv-nilai'));
            rows.sort((a, b) => {
                const vA = parseFloat(a.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                const vB = parseFloat(b.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                return vB - vA;
            });
            rows.forEach((row, i) => {
                const no = row.querySelector('.row-no');
                if (no) no.textContent = i + 1;
                tbody.appendChild(row);
            });
        });
    });

    // ── Export CSV ────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const table = document.getElementById(this.dataset.table);
            if (!table) return;
            const csv = [...table.querySelectorAll('tr')].map(row =>
                [...row.querySelectorAll('th, td')]
                    .map(c => '"' + c.innerText.trim().replace(/"/g, '""') + '"')
                    .join(',')
            ).join('\n');
            const a = document.createElement('a');
            a.href     = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = this.dataset.filename + '.csv';
            a.click();
        });
    });
});
</script>
@endpush