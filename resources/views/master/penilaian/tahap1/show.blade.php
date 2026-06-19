@extends('index')

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

{{-- ── Flash message ── --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.2); color:#1e40af; margin:0 24px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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

    {{-- ── Tabs ── --}}
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

        {{-- ── Tab Umum ── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">
            @include('master.penilaian.tahap1.panel', [
                'group'    => 'umum',
                'title'    => 'Verifikasi Umum',
                'tableId'  => 'tableUmum',
                'filename' => 'verifikasi-umum',
                'nominasi' => $nominasiUmum,
                'penilai'  => $penilai,
                'indikators'            => $indikators,
                'penilaiLogin'          => $penilaiLogin,
                'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
            ])
        </div>

        {{-- ── Tab Pelajar ── --}}
        <div class="tab-pane fade" id="panel-pelajar" role="tabpanel">
            @include('master.penilaian.tahap1.panel', [
                'group'    => 'pelajar',
                'title'    => 'Verifikasi Pelajar',
                'tableId'  => 'tablePelajar',
                'filename' => 'verifikasi-pelajar',
                'nominasi' => $nominasiPelajar,
                'penilai'  => $penilai,
                'indikators'            => $indikators,
                'penilaiLogin'          => $penilaiLogin,
                'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
            ])
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const SIMPAN_URL = '{{ route("penilaian.tahap1.simpan", $subEvent["id"]) }}';
    const NILAI_URL  = '{{ route("penilaian.tahap1.simpan.nilai", $subEvent["id"]) }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // Nilai per inovator milik penilai yang sedang login (untuk pre-fill)
    const nilaiDb = @json($nilaiLoginPerInovator ?? []);

    // ── Modal Input Nilai ─────────────────────────────────────────────────
    let activeInovatorId = null;

    document.querySelectorAll('.btn-input-nilai').forEach(btn => {
        btn.addEventListener('click', function () {
            activeInovatorId = this.dataset.inovatorId;

            // Isi header modal
            document.querySelectorAll('.modal-inovator-nama').forEach(el => el.textContent = this.dataset.inovator);
            document.querySelectorAll('.modal-inovasi-nama').forEach(el => el.textContent = this.dataset.namaInovasi);

            // Pre-fill dari nilaiDb
            const savedNilai = nilaiDb[activeInovatorId] ?? {};
            document.querySelectorAll('.input-nilai-item').forEach(inp => {
                const kid = inp.dataset.keteranganId;
                inp.value = savedNilai[kid] !== undefined ? savedNilai[kid] : '';
            });

            // Tentukan group berdasarkan tombol yang diklik
            const group   = this.closest('.rv-card')
                            ?.querySelector('.btn-simpan-nilai-modal')?.dataset.group ?? 'umum';
            const modalId = 'modalNilaiTahap1' + cap(group);
            bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId)).show();
        });
    });

    document.querySelectorAll('.btn-simpan-nilai-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!activeInovatorId) return;

            const group = this.dataset.group;
            const nilai = {};
            document.querySelectorAll('.input-nilai-item[data-group="' + group + '"]').forEach(inp => {
                if (inp.value !== '') nilai[inp.dataset.keteranganId] = parseInt(inp.value, 10);
            });

            if (Object.keys(nilai).length === 0) {
                toast('Isi minimal satu nilai terlebih dahulu.', 'error');
                return;
            }

            const orig    = this.innerHTML;
            this.disabled = true;
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
                    nilaiDb[activeInovatorId] = Object.assign(nilaiDb[activeInovatorId] ?? {}, nilai);
                    const modalId = 'modalNilaiTahap1' + cap(group);
                    bootstrap.Modal.getInstance(document.getElementById(modalId))?.hide();
                } else {
                    toast(data.message ?? 'Gagal menyimpan nilai.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { this.disabled = false; this.innerHTML = orig; });
        });
    });

    // ── Helper: capitalize first letter ──
    const cap = s => s.charAt(0).toUpperCase() + s.slice(1);

    // ── Build group helper ──
    function buildGroup(name) {
        return {
            name,
            get rows()    { return [...document.querySelectorAll('.chk-row[data-group="' + name + '"]')] },
            get checked() { return [...document.querySelectorAll('.chk-row[data-group="' + name + '"]:checked')] },
            get checkAll(){ return document.querySelector('.chk-all[data-group="' + name + '"]') },
            get bar()     { return document.getElementById('simpanBar' + cap(name)) },
            get count()   { return document.querySelector('#simpanBar' + cap(name) + ' .simpan-count') },
        };
    }

    const groups = {
        umum    : buildGroup('umum'),
        pelajar : buildGroup('pelajar'),
    };

    // ── Sync UI state ──
    function syncUI(g) {
        const total = g.rows.length;
        const n     = g.checked.length;

        g.rows.forEach(chk => {
            chk.closest('tr').classList.toggle('row-lolos', chk.checked);
        });

        if (g.checkAll) {
            g.checkAll.indeterminate = n > 0 && n < total;
            g.checkAll.checked       = total > 0 && n === total;
        }

        if (g.bar) g.bar.style.display = n > 0 ? 'flex' : 'none';
        if (g.count) g.count.textContent = n;
    }

    Object.values(groups).forEach(g => syncUI(g));

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

    // ── Simpan lolos (session) ──
    document.querySelectorAll('.btn-rv-simpan').forEach(btn => {
        btn.addEventListener('click', function () {
            const g   = groups[this.dataset.group];
            const ids = g.checked.map(c => c.dataset.id);

            if (ids.length === 0) {
                toast('Pilih minimal 1 inovasi terlebih dahulu.', 'error');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            fetch(SIMPAN_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ kategori: g.name, ids }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) toast('Data berhasil disimpan!', 'success');
                else toast(data.message ?? 'Gagal menyimpan data.', 'error');
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-save me-1"></i>Simpan'; });
        });
    });
    // ── Catatan Penilai ──
const CATATAN_BASE = '{{ url("penilaian/catatan") }}';
let activeUsulanId = null;

document.querySelectorAll('.btn-catatan').forEach(btn => {
    btn.addEventListener('click', function () {
        activeUsulanId = this.dataset.usulanId;
        const group    = this.dataset.group;

        document.querySelector('.modal-catatan-inovator-' + group).textContent = this.dataset.inovator;
        document.querySelector('.modal-catatan-inovasi-'  + group).textContent = this.dataset.namaInovasi;

        // Load catatan existing
        fetch(CATATAN_BASE + '/' + activeUsulanId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(data => {
            document.querySelector('.textarea-catatan-' + group).value = data.catatan ?? '';
        })
        .catch(() => {});

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('modalCatatan' + cap(group))
        ).show();
    });
});

    document.querySelectorAll('.btn-simpan-catatan').forEach(btn => {
    btn.addEventListener('click', function () {
        if (!activeUsulanId) return;
        const group   = this.dataset.group;
        const catatan = document.querySelector('.textarea-catatan-' + group).value.trim();

        if (!catatan) {
            toast('Catatan tidak boleh kosong.', 'error');
            return;
        }

        const orig    = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

        fetch(CATATAN_BASE + '/' + activeUsulanId, {
            method : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ catatan }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toast('Catatan berhasil disimpan!', 'success');
                bootstrap.Modal.getInstance(
                    document.getElementById('modalCatatan' + cap(group))
                )?.hide();
            } else {
                toast(data.message ?? 'Gagal menyimpan catatan.', 'error');
            }
        })
        .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
        .finally(() => { this.disabled = false; this.innerHTML = orig; });
    });
    });
    // ── Rangking ──
    document.querySelectorAll('.btn-rv-rank').forEach(btn => {
        btn.addEventListener('click', function () {
            const tbody = document.querySelector('#' + this.dataset.table + ' tbody');
            if (!tbody) return;

            const rows = [...tbody.querySelectorAll('tr')].filter(row => row.querySelector('.rv-nilai'));
            rows.sort((a, b) => {
                const nilaiA = parseFloat(a.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                const nilaiB = parseFloat(b.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                return nilaiB - nilaiA;
            });
            rows.forEach((row, i) => {
                const no = row.querySelector('.row-no');
                if (no) no.textContent = i + 1;
                tbody.appendChild(row);
            });
        });
    });

    // ── Export CSV ──
    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const table = document.getElementById(this.dataset.table);
            if (!table) return;

            const csv = [...table.querySelectorAll('tr')].map(row =>
                [...row.querySelectorAll('th, td')]
                    .slice(1)
                    .map(c => '"' + c.innerText.trim().replace(/"/g, '""') + '"')
                    .join(',')
            ).join('\n');

            const a = document.createElement('a');
            a.href     = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = this.dataset.filename + '.csv';
            a.click();
        });
    });

    // ── Toast ──
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = 'rv-toast ' + type;
        el.innerHTML = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'x-circle-fill') + '"></i>' + msg;
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