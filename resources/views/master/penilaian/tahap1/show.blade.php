{{-- resources/views/master/penilaian/tahap1/show.blade.php --}}
@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
{{--
    PENTING: Jangan pakai datatables.css lokal — versi lama (1.x) akan
    membuat tampilan DT v2.x + ColumnControl berantakan. Pakai CDN. —Regan.
--}}
<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
      rel="stylesheet"
      integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
      crossorigin="anonymous">
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

    /* ═══════════════════════════════════════════════════════════════════════
     * KONSTANTA & URL
     * ═══════════════════════════════════════════════════════════════════════ */
    const MAX_LOLOS      = 10; // ganti maksimal lolos hanya butuh ganti ni nomor
    const NILAI_URL      = '{{ route("penilaian.tahap1.simpan.nilai", $subEvent["id"]) }}';
    const SIMPAN_URL     = '{{ route("penilaian.tahap1.simpan", $subEvent["id"]) }}';
    const CATATAN_BASE   = '{{ url("penilaian/catatan") }}';
    const CSRF           = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const nilaiDb        = @json($nilaiLoginPerInovator ?? []);
    const penilaiLoginId = {{ $penilaiLogin?->id ?? 'null' }};

    const cap = s => s.charAt(0).toUpperCase() + s.slice(1);

    /* ═══════════════════════════════════════════════════════════════════════
     * HELPER: resolve group dari elemen
     * ═══════════════════════════════════════════════════════════════════════ */
    function resolveGroup(el) {
        return el.dataset.group
            ?? el.closest('[data-group]')?.dataset.group
            ?? el.closest('.tab-pane')?.id?.replace('panel-', '')
            ?? 'umum';
    }

    /* ═══════════════════════════════════════════════════════════════════════
     * HELPER: ambil total nilai numerik dari baris (pakai data-sort)
     * ═══════════════════════════════════════════════════════════════════════ */
    function getTotalNilaiFromRow(chk) {
        const tr = chk.closest('tr');
        if (!tr) return -1;
        const nilaiCell = tr.querySelector('.rv-nilai');
        return nilaiCell ? (parseFloat(nilaiCell.dataset.sort) || -1) : -1;
    }

    /* ═══════════════════════════════════════════════════════════════════════
     * TOAST
     * ═══════════════════════════════════════════════════════════════════════ */
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

    /* ═══════════════════════════════════════════════════════════════════════
     * GROUP HELPER
     * Gunakan getter agar referensi ke DOM selalu fresh (DT re-render saat
     * sort/pagination sehingga NodeList lama bisa basi).
     * ═══════════════════════════════════════════════════════════════════════ */
    function buildGroup(name) {
        return {
            name,
            get rows()        { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]`)]; },
            get rowsEnabled() { return this.rows.filter(c => !c.disabled); },
            get checked()     { return this.rows.filter(c => c.checked); },
            get checkAll()    { return document.querySelector(`.chk-all[data-group="${name}"]`); },
        };
    }
    const groups = { umum: buildGroup('umum'), pelajar: buildGroup('pelajar') };

    /* ═══════════════════════════════════════════════════════════════════════
     * SYNC CHECKBOX UI
     *
     * Tanggung jawab:
     *   1. Toggle class row-lolos pada <tr> yang dipilih
     *   2. Lock/unlock checkbox yang belum dipilih jika sudah mencapai MAX_LOLOS
     *   3. Update state chk-all (checked / indeterminate / unchecked)
     *   4. Update label counter pada tombol Simpan
     *
     * Di-expose ke window agar panel.blade.php bisa memanggilnya setelah
     * DataTables selesai draw.
     * ═══════════════════════════════════════════════════════════════════════ */
    window.syncCheckboxUI = function syncCheckboxUI(groupName) {
        const g = groups[groupName];
        if (!g) return;

        const jumlahDipilih  = g.checked.length;
        const sudahMaksimal  = jumlahDipilih >= MAX_LOLOS;

        /* 1. Highlight baris terpilih */
        g.rows.forEach(chk => {
            chk.closest('tr')?.classList.toggle('row-lolos', chk.checked);
        });

        /* 2. Lock checkbox yang belum dipilih jika sudah MAX_LOLOS */
        g.rows.forEach(chk => {
            /*
             * Jangan sentuh checkbox yang disabled karena "belum semua penilai
             * menilai" (disabled permanen dari server). Bedanya: checkbox
             * permanen tidak punya title "Siap diloloskan" dan tidak pernah
             * bisa di-checked. Kita hanya kelola checkbox yang memang enabled
             * atau yang di-lock oleh logika MAX ini.
             *
             * Strategi: simpan alasan disabled di data-lock agar bisa dibedakan.
             */
            if (chk.checked) return; // yang sudah dipilih — jangan disentuh

            const permaDisabled = chk.dataset.lock === 'perm'; // di-set saat init
            if (permaDisabled) return;

            if (sudahMaksimal) {
                chk.disabled     = true;
                chk.dataset.lock = 'max';
                chk.title        = `Maksimal ${MAX_LOLOS} inovasi yang dapat diloloskan`;
            } else {
                chk.disabled     = false;
                chk.dataset.lock = '';
                chk.title        = 'Siap diloloskan';
            }
        });

        /* 3. Update chk-all
         *    — checked      : semua yang bisa dipilih sudah dipilih (maks MAX_LOLOS)
         *    — indeterminate: sebagian dipilih
         *    — unchecked    : tidak ada yang dipilih
         */
        if (g.checkAll) {
            const totalBisaDipilih = g.rows.filter(c => c.dataset.lock !== 'perm').length;
            const efektifMaks      = Math.min(MAX_LOLOS, totalBisaDipilih);
            g.checkAll.indeterminate = jumlahDipilih > 0 && jumlahDipilih < efektifMaks;
            g.checkAll.checked       = jumlahDipilih > 0 && jumlahDipilih >= efektifMaks;
        }

        /* 4. Update counter label tombol Simpan */
        const simpanBtn = document.querySelector(`.btn-rv-simpan[data-group="${groupName}"]`);
        if (simpanBtn) {
            simpanBtn.innerHTML = jumlahDipilih > 0
                ? `<i class="bi bi-save me-1"></i>Simpan (${jumlahDipilih}/${MAX_LOLOS})`
                : `<i class="bi bi-save me-1"></i>Simpan`;
        }
    };

    /* ═══════════════════════════════════════════════════════════════════════
     * INIT: tandai checkbox yang permanen disabled (belum semua penilai menilai)
     * Dilakukan sekali saat load agar syncCheckboxUI bisa membedakannya.
     * ═══════════════════════════════════════════════════════════════════════ */
    function initPermDisabled() {
        document.querySelectorAll('.chk-row').forEach(chk => {
            /*
             * Checkbox dianggap "permanen disabled" jika saat halaman dimuat
             * atribut disabled sudah ada DAN baris induknya punya
             * data-sudah-lengkap="0". Ini mencerminkan kondisi dari server.
             */
            const tr = chk.closest('tr');
            if (chk.disabled && tr?.dataset.sudahLengkap === '0') {
                chk.dataset.lock = 'perm';
            }
        });
    }
    initPermDisabled();

    /* ═══════════════════════════════════════════════════════════════════════
     * CHANGE LISTENER — checkbox
     * ═══════════════════════════════════════════════════════════════════════ */
    document.addEventListener('change', function (e) {

        /* ── chk-row (checkbox per baris) ── */
        if (e.target.matches('.chk-row')) {
            syncCheckboxUI(e.target.dataset.group);
            return;
        }

        /* ── chk-all (select all) ──
         *
         * Perilaku khusus MAX_LOLOS:
         *   - Saat di-check   → centang top-7 berdasarkan total nilai tertinggi
         *   - Saat di-uncheck → uncheck semua
         */
        if (e.target.matches('.chk-all')) {
            const g = groups[e.target.dataset.group];
            if (!g) return;

            if (e.target.checked) {
                /*
                 * Pilih top-MAX_LOLOS dari checkbox yang bisa dipilih,
                 * diurutkan berdasarkan total nilai tertinggi (data-sort pada .rv-nilai).
                 */
                const kandidat = g.rows.filter(c => c.dataset.lock !== 'perm');

                /* Urutkan descending berdasarkan total nilai */
                const sorted = [...kandidat].sort((a, b) => {
                    return getTotalNilaiFromRow(b) - getTotalNilaiFromRow(a);
                });

                /* Uncheck semua dulu */
                kandidat.forEach(c => { c.checked = false; });

                /* Centang MAX_LOLOS teratas */
                sorted.slice(0, MAX_LOLOS).forEach(c => { c.checked = true; });

                /* Paksa chk-all checked (karena browser bisa reset setelah kita
                 * manipulasi di atas dan syncCheckboxUI belum jalan) */
                e.target.checked = true;

            } else {
                /* Uncheck semua */
                g.rows.forEach(c => { c.checked = false; });
            }

            syncCheckboxUI(e.target.dataset.group);
        }
    });

    /* Init sync awal setelah DT selesai render pertama kali */
    Object.keys(groups).forEach(syncCheckboxUI);

    /* ═══════════════════════════════════════════════════════════════════════
     * UPDATE BARIS TABEL SETELAH SIMPAN NILAI
     * ═══════════════════════════════════════════════════════════════════════ */
    function updateRowAfterSave(usulanId, data) {
        const row = document.querySelector(`tr[data-id="${usulanId}"]`);
        if (!row) return;

        if (data.total_nilai !== undefined) {
            const nilaiCell = row.querySelector('.rv-nilai');
            if (nilaiCell) {
                nilaiCell.dataset.sort = data.total_nilai;
                nilaiCell.textContent  = data.total_nilai > 0
                    ? parseFloat(data.total_nilai).toFixed(2) : '—';
            }
        }

        if (data.nilai_penilai !== undefined && penilaiLoginId) {
            const penilaiCell = row.querySelector(`.rv-nilai-penilai[data-penilai-id="${penilaiLoginId}"]`);
            if (penilaiCell) {
                penilaiCell.dataset.sort = data.nilai_penilai;
                penilaiCell.textContent  = parseFloat(data.nilai_penilai).toFixed(2);
            }
        }

        if (data.sudah_lengkap !== undefined) {
            row.dataset.sudahLengkap = data.sudah_lengkap ? '1' : '0';

            const statusCell = row.querySelector(
                'td:nth-last-child(' + (penilaiLoginId ? '2' : '1') + ')'
            );
            if (statusCell) {
                statusCell.dataset.sort = data.sudah_lengkap ? 2 : 1;
                statusCell.innerHTML = data.sudah_lengkap
                    ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lengkap</span>'
                    : '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Sebagian</span>';
            }

            const chk = row.querySelector('.chk-row');
            if (chk) {
                if (data.sudah_lengkap) {
                    /* Baru bisa dipilih — hapus lock perm */
                    chk.disabled     = false;
                    chk.dataset.lock = '';
                    chk.title        = 'Siap diloloskan';
                } else {
                    /* Kunci permanen */
                    chk.disabled     = true;
                    chk.checked      = false;
                    chk.dataset.lock = 'perm';
                    chk.title        = 'Belum semua penilai menilai';
                }
            }

            const group = resolveGroup(row);
            syncCheckboxUI(group);

            /* Beritahu DT bahwa DOM baris sudah berubah */
            const tableId = row.closest('table')?.id;
            if (tableId && window['dt_' + tableId]) {
                window['dt_' + tableId].row(row).invalidate('dom').draw(false);
            }
        }
    }

    /* ═══════════════════════════════════════════════════════════════════════
     * STATE AKTIF MODAL
     * ═══════════════════════════════════════════════════════════════════════ */
    let activeInovatorId = null;
    let activeUsulanId   = null;

    /* ═══════════════════════════════════════════════════════════════════════
     * CLICK LISTENER — satu delegation ke document
     * Mencakup: btn-input-nilai, btn-catatan, btn-simpan-nilai-modal,
     *           btn-rv-simpan, btn-simpan-catatan
     * ═══════════════════════════════════════════════════════════════════════ */
    document.addEventListener('click', function (e) {

        /* ── Buka modal input nilai ── */
        const nilaiBtn = e.target.closest('.btn-input-nilai');
        if (nilaiBtn) {
            activeInovatorId = nilaiBtn.dataset.inovatorId;
            const group   = resolveGroup(nilaiBtn);
            const modalEl = document.getElementById('modalNilaiTahap1' + cap(group));
            if (!modalEl) return;

            const elNama = document.querySelector('.modal-inovator-nama-' + group);
            const elInov = document.querySelector('.modal-inovasi-nama-'  + group);
            if (elNama) elNama.textContent = nilaiBtn.dataset.inovator    ?? '';
            if (elInov) elInov.textContent = nilaiBtn.dataset.namaInovasi ?? '';

            const savedNilai = nilaiDb[activeInovatorId] ?? {};
            modalEl.querySelectorAll('.input-nilai-item').forEach(inp => {
                inp.value = savedNilai[inp.dataset.keteranganId] ?? '';
            });

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            return;
        }

        /* ── Buka modal catatan ── */
        const catatanBtn = e.target.closest('.btn-catatan');
        if (catatanBtn) {
            activeUsulanId = catatanBtn.dataset.usulanId;
            const group    = resolveGroup(catatanBtn);

            const elInovator = document.querySelector('.modal-catatan-inovator-' + group);
            const elInovasi  = document.querySelector('.modal-catatan-inovasi-'  + group);
            if (elInovator) elInovator.textContent = catatanBtn.dataset.inovator    ?? '';
            if (elInovasi)  elInovasi.textContent  = catatanBtn.dataset.namaInovasi ?? '';

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
            return;
        }

        /* ── Simpan nilai dari modal ── */
        const simpanNilaiBtn = e.target.closest('.btn-simpan-nilai-modal');
        if (simpanNilaiBtn) {
            if (!activeInovatorId) return;
            const group   = resolveGroup(simpanNilaiBtn);
            const modalEl = document.getElementById('modalNilaiTahap1' + cap(group));

            const nilai = {};
            modalEl.querySelectorAll('.input-nilai-item').forEach(inp => {
                if (inp.value !== '') nilai[inp.dataset.keteranganId] = parseInt(inp.value, 10);
            });

            if (Object.keys(nilai).length === 0) {
                toast('Isi minimal satu nilai terlebih dahulu.', 'error');
                return;
            }

            const orig = simpanNilaiBtn.innerHTML;
            simpanNilaiBtn.disabled  = true;
            simpanNilaiBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            fetch(NILAI_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ usulan_id: activeInovatorId, nilai }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toast('Nilai berhasil disimpan!');
                    nilaiDb[activeInovatorId] = Object.assign(nilaiDb[activeInovatorId] ?? {}, nilai);
                    updateRowAfterSave(activeInovatorId, data);
                    bootstrap.Modal.getInstance(modalEl)?.hide();
                } else {
                    toast(data.message ?? 'Gagal menyimpan nilai.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { simpanNilaiBtn.disabled = false; simpanNilaiBtn.innerHTML = orig; });
            return;
        }

        /* ── Simpan lolos (btn-rv-simpan) ── */
        const simpanBtn = e.target.closest('.btn-rv-simpan');
        if (simpanBtn) {
            const g   = groups[simpanBtn.dataset.group];
            if (!g) return;
            const ids = g.checked.map(c => c.dataset.id);

            if (ids.length === 0) {
                toast('Pilih minimal 1 inovasi terlebih dahulu.', 'error');
                return;
            }

            /* Guard MAX_LOLOS — seharusnya tidak pernah tercapai karena UI
             * sudah mengunci checkbox, tapi ini lapisan kedua. */
            if (ids.length > MAX_LOLOS) {
                toast(`Maksimal ${MAX_LOLOS} inovasi yang dapat diloloskan untuk kategori ${g.name}.`, 'error');
                return;
            }

            const orig    = simpanBtn.innerHTML;
            simpanBtn.disabled  = true;
            simpanBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

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
                    toast(data.message ?? 'Gagal menyimpan data.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { simpanBtn.disabled = false; simpanBtn.innerHTML = orig; });
            return;
        }

        /* ── Simpan catatan ── */
        const simpanCatatanBtn = e.target.closest('.btn-simpan-catatan');
        if (simpanCatatanBtn) {
            if (!activeUsulanId) return;
            const group   = resolveGroup(simpanCatatanBtn);
            const catatan = document.querySelector('.textarea-catatan-' + group)?.value.trim();
            if (!catatan) { toast('Catatan tidak boleh kosong.', 'error'); return; }

            const orig    = simpanCatatanBtn.innerHTML;
            simpanCatatanBtn.disabled  = true;
            simpanCatatanBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            fetch(`${CATATAN_BASE}/${activeUsulanId}`, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ catatan }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toast('Catatan berhasil disimpan!');
                    bootstrap.Modal.getInstance(
                        document.getElementById('modalCatatan' + cap(group))
                    )?.hide();
                } else {
                    toast(data.message ?? 'Gagal menyimpan catatan.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { simpanCatatanBtn.disabled = false; simpanCatatanBtn.innerHTML = orig; });
        }

    }); // end document click delegation

});
</script>
@endpush