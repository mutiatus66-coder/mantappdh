@extends('index', ['dummy' => true])

@push('styles')
    <link rel="stylesheet" href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}">

    {{--
        PENTING: Jangan pakai datatables.css lokal karena versi nya lama (1.x)
        nanti tampilan DT v2.x + ColumnControl berantakan.
        pake CDN aja yh —Regan.
    --}}
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
          rel="stylesheet"
          integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
          crossorigin="anonymous">
@endpush

@section('content')

{{-- ══ FLASH MESSAGES ══ --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(0,172,193,0.10); border:1px solid rgba(0,172,193,0.3); color:#006064; margin: 0 20px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(163,45,45,0.1); border:1px solid rgba(163,45,45,0.3); color:#A32D2D; margin: 0 20px;">
    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══ MAIN CONTAINER ══ --}}
<div class="bidang-container">

    <div class="bidang-header">
        <div class="bidang-title">
            <h3>Master Bidang</h3>
            <p>Kelola bidang untuk setiap sub event</p>
        </div>
    </div>

    {{-- ══ LOOP PER EVENT (PARENT) ══ --}}
    @forelse($events as $event)
    <div class="mb-4">

        {{-- Event Label --}}
        <div class="px-1 mb-2 d-flex align-items-center gap-2">
            <span class="badge bg-secondary text-uppercase" style="font-size:.7rem; letter-spacing:.05em;">
                {{ $event->jenis }}
            </span>
            <h6 class="mb-0 fw-bold" style="color:var(--ri-text-primary); font-size:.95rem;">
                {{ $event->nama_event }}
            </h6>
        </div>

        {{-- Accordion Sub Events --}}
        <div class="accordion" id="accordion-event-{{ $event->id }}">

            @forelse($event->subEvents as $se)
            @php
                $aktif    = $se->bidangs->where('status', 'aktif')->count();
                $nonaktif = $se->bidangs->where('status', 'tidak_aktif')->count();
            @endphp

            <div class="accordion-item bidang-accordion-item mb-2">

                {{-- Accordion Header --}}
                <h2 class="accordion-header" id="heading-{{ $se->id }}">
                    <button class="accordion-button bidang-accordion-btn fw-semibold collapsed px-4"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $se->id }}"
                            aria-expanded="false"
                            aria-controls="collapse-{{ $se->id }}"
                            data-se-id="{{ $se->id }}">
                        <span class="me-2 small text-muted">{{ $se->tahun }}</span>
                        <span class="fw-bold">{{ $se->sub_event }}</span>
                    </button>
                </h2>

                {{-- Accordion Body --}}
                <div id="collapse-{{ $se->id }}"
                     class="accordion-collapse collapse"
                     aria-labelledby="heading-{{ $se->id }}">

                    <div class="accordion-body p-4">

                        {{-- Stats + Tambah --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div class="d-flex gap-2">
                                <span class="badge-aktif rounded-pill px-3 py-2" id="badge-aktif-{{ $se->id }}">
                                    Aktif <strong>{{ $aktif }}</strong>
                                </span>
                                <span class="badge-nonaktif rounded-pill px-3 py-2" id="badge-nonaktif-{{ $se->id }}">
                                    Tidak Aktif <strong>{{ $nonaktif }}</strong>
                                </span>
                            </div>

                            <button class="btn btn-primary btn-tambah-bidang"
                                    data-sub-event-id="{{ $se->id }}"
                                    data-sub-event-nama="{{ $se->sub_event }}">
                                Tambah Bidang
                            </button>
                        </div>

                        {{--
                            Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
                            nggak perlu overflow-x wrapper karna DT mengelola scroll sendiri.
                        --}}
                        <table id="tabelBidang-{{ $se->id }}" class="display nowrap compact" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bidang</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-se-{{ $se->id }}">
                                @forelse($se->bidangs as $bidang)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucfirst($bidang->nama) }}</td>
                                    <td>{{ $bidang->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                    <td>
                                        {{-- HTML aksi disimpan di sini tapi dirender via DT --}}
                                        <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
                                            <button class="btn btn-warning btn-sm btn-ubah-bidang"
                                                    data-id="{{ $bidang->id }}"
                                                    data-nama="{{ $bidang->nama }}"
                                                    data-status="{{ $bidang->status }}"
                                                    data-sub-event-id="{{ $se->id }}"
                                                    data-sub-event-nama="{{ $se->sub_event }}"
                                                    data-url="{{ route('bidang.update', $bidang->id) }}">
                                                Ubah
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-hapus-bidang"
                                                    data-id="{{ $bidang->id }}"
                                                    data-nama="{{ $bidang->nama }}"
                                                    data-url="{{ route('bidang.destroy', $bidang->id) }}">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            @empty
            <div class="text-center py-4 empty-row">
                <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
                Belum ada sub event untuk event ini.
            </div>
            @endforelse

        </div>
    </div>

    @empty
    <div class="text-center py-5 empty-row">
        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
        Belum ada data event.
    </div>
    @endforelse

</div>


{{-- ══ MODAL — Tambah / Ubah Bidang ══ --}}
<div class="modal fade" id="modalBidang" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalBidangTitle">Tambah Bidang</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalBidang" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <div class="modal-body px-5 py-4">

                <p class="mb-3" style="font-size:0.85rem; color:var(--ri-text-muted);">
                    Sub Event: <strong id="bidangSubEventNama" style="color:var(--ri-text-primary);"></strong>
                </p>

                <div class="mb-4">
                    <label class="form-label fw-semibold required">Nama Bidang</label>
                    <input type="text" id="bidangNama"
                           class="form-control" placeholder="Masukkan nama bidang...">
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="d-flex gap-4 mt-1">
                        <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                            <input type="radio" name="statusBidang" id="statusAktifBidang" value="aktif" checked> Aktif
                        </label>
                        <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                            <input type="radio" name="statusBidang" id="statusNonaktifBidang" value="tidak_aktif"> Tidak Aktif
                        </label>
                    </div>
                </div>

            </div>

            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalBidang">Batal</button>
                <button type="button" id="btnSimpanBidang" class="btn btn-success px-4">Simpan</button>
            </div>

        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Bidang ══ --}}
<div class="modal fade" id="modalHapusBidang" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:#dc2626;"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4" style="font-size:.875rem; line-height:1.6; color:var(--ri-text-muted);">
                Tindakan ini tidak dapat dibatalkan. Bidang
                <strong id="namaBidangHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapusBidang">Batal</button>
                <button type="button" id="btnHapusBidang" class="btn btn-danger btn-sm px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/jquery/jquery-4.0.0.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js" integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ" crossorigin="anonymous"></script>

<script>
(function () {
    'use strict';

    /* ══════════════════════════════════════════
       KONSTANTA & ELEMEN
    ══════════════════════════════════════════ */
    const STORE_URL = "{{ route('bidang.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const modalBidangEl = document.getElementById('modalBidang');
    const modalHapusEl  = document.getElementById('modalHapusBidang');
    const modalBidang   = new bootstrap.Modal(modalBidangEl);
    const modalHapus    = new bootstrap.Modal(modalHapusEl);

    /* ══════════════════════════════════════════
       STATE
    ══════════════════════════════════════════ */
    let activeMode         = 'store';
    let activeUpdateId     = null, activeUpdateUrl    = null;
    let activeSubEventId   = null, activeSubEventNama = null;
    let activeHapusId      = null, activeHapusUrl     = null, activeHapusNama = null;
    let isSaving           = false, isDeleting        = false;

    /* ══════════════════════════════════════════
       DATATABLES — registry & inisialisasi
       Setiap sub event punya instance DT sendiri,
       disimpan di dtMap agar bisa di-update.
    ══════════════════════════════════════════ */
    const dtMap = {};   // { seId: DataTable instance }

    function initDT(seId) {
        if (dtMap[seId]) return;   // sudah diinit, skip
        dtMap[seId] = $(`#tabelBidang-${seId}`).DataTable({
            responsive: true,

            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : { first: '«', last: '»', next: '›', previous: '‹' },
                emptyTable  : 'Belum ada bidang untuk sub event ini.',
            },

            layout: {
                topStart: ['pageLength', { buttons: ['colvis'] }],
                topEnd  : 'search',
            },

            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order     : [[0, 'asc']],

            columnDefs: [
                {
                    targets  : [0],
                    searchable: false,
                    width    : '50px',
                    className: 'dt-center',
                },
                {
                    targets  : [2],
                    width    : '140px',
                    className: 'dt-center',
                },
                {
                    targets   : [3],
                    orderable : false,
                    searchable: false,
                    width     : '200px',
                    className : 'dt-center',
                },
            ],
        });
    }

    /* ── Inisialisasi DT saat accordion dibuka (lazy) ── */
    document.querySelectorAll('.accordion-collapse').forEach(function (collapseEl) {
        collapseEl.addEventListener('shown.bs.collapse', function () {
            const seId = this.id.replace('collapse-', '');
            initDT(seId);
            // Penting: DT butuh columns.adjust() saat container baru terlihat
            if (dtMap[seId]) dtMap[seId].columns.adjust().responsive.recalc();
        });
    });

    /* ══════════════════════════════════════════
       HELPER: AJAX
    ══════════════════════════════════════════ */
    async function sendRequest(url, data) {
        const form = new FormData();
        Object.entries(data).forEach(([k, v]) => form.append(k, v));
        const res = await fetch(url, {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body   : form,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

    /* ══════════════════════════════════════════
       HELPER: TOAST
    ══════════════════════════════════════════ */
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

    /* ══════════════════════════════════════════
       HELPER: BADGE COUNTER
       Update badge Aktif / Tidak Aktif di header accordion
    ══════════════════════════════════════════ */
    function updateBadges(seId) {
        const dt = dtMap[seId];
        if (!dt) return;

        let aktif = 0, nonaktif = 0;
        dt.rows().every(function () {
            const statusText = this.data()[2];    // kolom ke-2 = Status (plain text)
            if (typeof statusText === 'string') {
                if (statusText.trim() === 'Aktif') aktif++;
                else nonaktif++;
            }
        });

        const badgeAktif    = document.getElementById(`badge-aktif-${seId}`);
        const badgeNonaktif = document.getElementById(`badge-nonaktif-${seId}`);
        if (badgeAktif)    badgeAktif.innerHTML    = `Aktif <strong>${aktif}</strong>`;
        if (badgeNonaktif) badgeNonaktif.innerHTML = `Tidak Aktif <strong>${nonaktif}</strong>`;
    }

    /* ══════════════════════════════════════════
       HELPER: MANIPULASI BARIS DT
    ══════════════════════════════════════════ */
    function makeAksiHtml(id, nama, status, seId, seNama, updateUrl, destroyUrl) {
        return `
        <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
            <button class="btn btn-warning btn-sm btn-ubah-bidang"
                    data-id="${id}"
                    data-nama="${nama}"
                    data-status="${status}"
                    data-sub-event-id="${seId}"
                    data-sub-event-nama="${seNama}"
                    data-url="${updateUrl}">
                Ubah
            </button>
            <button class="btn btn-danger btn-sm btn-hapus-bidang"
                    data-id="${id}"
                    data-nama="${nama}"
                    data-url="${destroyUrl}">
                Hapus
            </button>
        </div>`;
    }

    function updateRow(id, nama, status) {
        const seId = activeSubEventId;
        const dt   = dtMap[seId];
        if (!dt) return;

        // Cari baris berdasarkan data-id di kolom aksi
        dt.rows().every(function () {
            const node = this.node();
            const btn  = node?.querySelector(`.btn-ubah-bidang[data-id="${id}"]`);
            if (!btn) return;

            const statusText = status === 'aktif' ? 'Aktif' : 'Tidak Aktif';
            const statusHtml = status === 'aktif'
                ? `<span class="badge-aktif px-3 py-2">Aktif</span>`
                : `<span class="badge-nonaktif px-3 py-2">Tidak Aktif</span>`;

            // Update data DT (kolom 1 & 2) lalu invalidate
            this.data()[1] = nama.charAt(0).toUpperCase() + nama.slice(1);
            this.data()[2] = statusText;
            dt.cell(this.index(), 1).data(nama.charAt(0).toUpperCase() + nama.slice(1));
            dt.cell(this.index(), 2).data(statusText);

            // Render ulang kolom status sebagai HTML badge
            $(node).find('td').eq(2).html(statusHtml);

            // Perbarui data-attribute tombol
            btn.dataset.nama   = nama;
            btn.dataset.status = status;
            const hapusBtn = node.querySelector('.btn-hapus-bidang');
            if (hapusBtn) hapusBtn.dataset.nama = nama;
        });

        updateBadges(seId);
        dt.draw(false);
    }

    function appendRow(bidang, seId) {
        const dt = dtMap[seId];
        if (!dt) return;

        const statusText = bidang.status === 'aktif' ? 'Aktif' : 'Tidak Aktif';
        const newNo      = dt.rows().count() + 1;

        dt.row.add([
            newNo,
            bidang.nama.charAt(0).toUpperCase() + bidang.nama.slice(1),
            statusText,
            makeAksiHtml(
                bidang.id,
                bidang.nama,
                bidang.status,
                seId,
                bidang.sub_event_nama ?? '',
                bidang.update_url,
                bidang.destroy_url
            ),
        ]).draw(false);

        // Render kolom status sebagai badge setelah row ditambah
        const lastRow = dt.row(':last', { search: 'none' }).node();
        if (lastRow) {
            const statusHtml = bidang.status === 'aktif'
                ? `<span class="badge-aktif px-3 py-2">Aktif</span>`
                : `<span class="badge-nonaktif px-3 py-2">Tidak Aktif</span>`;
            $(lastRow).find('td').eq(2).html(statusHtml);
        }

        updateBadges(seId);
    }

    /* ══════════════════════════════════════════
       HELPER: LOADING STATE
    ══════════════════════════════════════════ */
    function setSimpanLoading(on) {
        const btn   = document.getElementById('btnSimpanBidang');
        const batal = document.getElementById('btnBatalBidang');
        const tutup = document.getElementById('btnTutupModalBidang');
        btn.disabled    = on;
        btn.textContent = on ? 'Menyimpan...' : 'Simpan';
        batal.disabled  = on;
        tutup.disabled  = on;
    }

    function setHapusLoading(on) {
        const btn   = document.getElementById('btnHapusBidang');
        const batal = document.getElementById('btnBatalHapusBidang');
        btn.disabled    = on;
        btn.textContent = on ? 'Menghapus...' : 'Hapus';
        batal.disabled  = on;
    }

    /* ══════════════════════════════════════════
       EVENT DELEGATION — Tambah / Ubah / Hapus
       Satu listener di body menangkap semua tombol
       termasuk baris yang baru di-append ke DT
    ══════════════════════════════════════════ */
    document.body.addEventListener('click', function (e) {

        // ── Tambah Bidang ──
        const tambahBtn = e.target.closest('.btn-tambah-bidang');
        if (tambahBtn) {
            activeMode         = 'store';
            activeUpdateId     = null;
            activeUpdateUrl    = null;
            activeSubEventId   = tambahBtn.dataset.subEventId;
            activeSubEventNama = tambahBtn.dataset.subEventNama;
            document.getElementById('modalBidangTitle').textContent   = 'Tambah Bidang';
            document.getElementById('bidangSubEventNama').textContent = activeSubEventNama;
            document.getElementById('bidangNama').value               = '';
            document.getElementById('statusAktifBidang').checked      = true;
            setSimpanLoading(false);
            modalBidang.show();
            return;
        }

        // ── Ubah Bidang ──
        const ubahBtn = e.target.closest('.btn-ubah-bidang');
        if (ubahBtn) {
            activeMode         = 'update';
            activeUpdateId     = ubahBtn.dataset.id;
            activeUpdateUrl    = ubahBtn.dataset.url;
            activeSubEventId   = ubahBtn.dataset.subEventId;
            activeSubEventNama = ubahBtn.dataset.subEventNama;
            document.getElementById('modalBidangTitle').textContent   = 'Ubah Bidang';
            document.getElementById('bidangSubEventNama').textContent = activeSubEventNama;
            document.getElementById('bidangNama').value               = ubahBtn.dataset.nama;
            const radioId = ubahBtn.dataset.status === 'tidak_aktif'
                ? 'statusNonaktifBidang' : 'statusAktifBidang';
            document.getElementById(radioId).checked = true;
            setSimpanLoading(false);
            modalBidang.show();
            return;
        }

        // ── Hapus Bidang ──
        const hapusBtn = e.target.closest('.btn-hapus-bidang');
        if (hapusBtn) {
            activeHapusId   = hapusBtn.dataset.id;
            activeHapusUrl  = hapusBtn.dataset.url;
            activeHapusNama = hapusBtn.dataset.nama;
            // Ambil seId dari tombol ubah di baris yang sama
            const tr = hapusBtn.closest('tr');
            const ubahSibling = tr?.querySelector('.btn-ubah-bidang');
            activeSubEventId = ubahSibling?.dataset.subEventId ?? activeSubEventId;
            document.getElementById('namaBidangHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapus.show();
        }
    });

    /* ══════════════════════════════════════════
       MODAL: TUTUP MANUAL
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupModalBidang').addEventListener('click', () => { if (!isSaving)   modalBidang.hide(); });
    document.getElementById('btnBatalBidang').addEventListener('click',      () => { if (!isSaving)   modalBidang.hide(); });
    document.getElementById('btnBatalHapusBidang').addEventListener('click', () => { if (!isDeleting) modalHapus.hide(); });

    /* ══════════════════════════════════════════
       SUBMIT: TAMBAH / UBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnSimpanBidang').addEventListener('click', async function () {
        if (isSaving) return;

        const nama   = document.getElementById('bidangNama').value.trim();
        const status = document.querySelector('input[name="statusBidang"]:checked').value;

        if (!nama) { document.getElementById('bidangNama').focus(); return; }

        isSaving = true;
        setSimpanLoading(true);

        try {
            const isUpdate = activeMode === 'update';
            const res = await sendRequest(isUpdate ? activeUpdateUrl : STORE_URL, {
                _method     : isUpdate ? 'PUT' : 'POST',
                nama,
                status,
                sub_event_id: activeSubEventId,
            });

            if (res.success) {
                modalBidang.hide();
                toast(isUpdate ? 'Bidang berhasil diubah!' : 'Bidang berhasil ditambahkan!');
                isUpdate
                    ? updateRow(activeUpdateId, nama, status)
                    : appendRow(res.bidang, activeSubEventId);
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
            }
        } catch (err) {
            console.error(err);
            toast('Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isSaving = false;
            setSimpanLoading(false);
        }
    });

    /* ══════════════════════════════════════════
       SUBMIT: HAPUS
    ══════════════════════════════════════════ */
    document.getElementById('btnHapusBidang').addEventListener('click', async function () {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapus.hide();
                toast(`Bidang "${activeHapusNama}" berhasil dihapus!`);

                const dt = dtMap[activeSubEventId];
                if (dt) {
                    dt.rows().every(function () {
                        const node = this.node();
                        if (node?.querySelector(`.btn-hapus-bidang[data-id="${activeHapusId}"]`)) {
                            dt.row(this.index()).remove().draw(false);
                        }
                    });
                    updateBadges(activeSubEventId);
                }
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch (err) {
            console.error(err);
            toast('Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isDeleting = false;
            setHapusLoading(false);
        }
    });

})();
</script>
@endpush