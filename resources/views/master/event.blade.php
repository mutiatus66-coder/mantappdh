@extends('index', ['dummy' => true])

@push('styles')
    <link rel="stylesheet" href="<?= asset('template.demo6/demo6/assets/css/CostumeStyle.css') ?>">

    {{--
        PENTING: Jangan pakai datatables.css lokal karena versi nya lama (1.x)
        nanti tampilan DT v2.x + ColumnControl berantakan.
        pake CDN aja yh —Regan.
    --}}
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css" rel="stylesheet" integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm" crossorigin="anonymous">

@endpush

@section('content')
<div class="page-container">

    {{-- Header --}}
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data Event</h3>
            <p>Kelola semua event yang tersedia</p>
        </div>
        <button class="btn btn-primary" id="btnTambahEvent">Tambah Event</button>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Event: <span id="totalEvent"><?= $events->count() ?></span>
        </div>
    </div>

    {{--
        Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
        nggak perlu overflow-x wrapper karna DT mengelola scroll sendiri.
    --}}
    <table id="tabelEvent" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Event</th>
                <th>Jenis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelEventBody">
            @forelse($events as $index => $item)
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= e($item->nama_event) ?></td>
                <td><?= e($item->jenis ?? '-') ?></td>
                <td>
                    <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
                        <button class="btn btn-warning btn-sm btn-edit-event"
                                data-id="<?= $item->id ?>"
                                data-nama-event="<?= e($item->nama_event) ?>"
                                data-jenis="<?= e($item->jenis) ?>"
                                data-url="<?= route('event.update', $item->id) ?>">
                            Ubah
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus-event"
                                data-id="<?= $item->id ?>"
                                data-nama="<?= e($item->nama_event) ?>"
                                data-url="<?= route('event.destroy', $item->id) ?>">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data event
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

{{-- ===== MODAL: Tambah / Ubah Event ===== --}}
<div class="modal fade" id="modalEvent" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalEventTitle">Tambah Event</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalEvent" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <div class="modal-body px-5 py-4">
                <div class="mb-4">
                    <label class="form-label fw-semibold required">Nama Event</label>
                    <input type="text" id="inputNamaEvent" class="form-control"
                           placeholder="Masukkan nama event...">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold required">Jenis</label>
                    <select id="inputJenis" class="form-select">
                        <option value="" disabled selected>-- Pilih Jenis --</option>
                        <option value="INOTEK">INOTEK</option>
                        <option value="INODA">INODA</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalEvent">Batal</button>
                <button type="button" id="btnSimpanEvent" class="btn btn-success px-4">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: Konfirmasi Hapus ===== --}}
<div class="modal fade" id="modalHapusEvent" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">
            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem;color:#dc2626;"></i>
                </div>
            </div>
            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem;line-height:1.6;">
                Data event <strong id="namaEventHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapus">Batal</button>
                <button type="button" id="btnHapusEvent" class="btn btn-danger btn-sm px-3">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{--
    JANGAN load DT core JS lokal karena kemungkinan versi lawas
--}}
<script src="<?= asset('assets/jquery/jquery-4.0.0.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js" integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ" crossorigin="anonymous"></script>

<script>
(function () {
    'use strict';

    /* ══════════════════════════════════════════
       KONSTANTA & ELEMEN
    ══════════════════════════════════════════ */
    const STORE_URL = "<?= route('event.store') ?>";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody     = document.getElementById('tabelEventBody');
    const totalSpan = document.getElementById('totalEvent');

    const modalEventEl = document.getElementById('modalEvent');
    const modalHapusEl = document.getElementById('modalHapusEvent');
    const modalEvent   = new bootstrap.Modal(modalEventEl);
    const modalHapus   = new bootstrap.Modal(modalHapusEl);

    /* ══════════════════════════════════════════
       STATE
    ══════════════════════════════════════════ */
    let activeMode      = 'store';
    let activeUpdateId  = null, activeUpdateUrl = null;
    let activeHapusId   = null, activeHapusUrl  = null, activeHapusNama = null;
    let isSaving        = false, isDeleting = false;
    let dt              = null;

    /* ══════════════════════════════════════════
    Layout Datatables
    ══════════════════════════════════════════ */
    $(document).ready(function () {
        dt = $('#tabelEvent').DataTable({
            responsive: true,

            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : {
                    first: '«',
                    last: '»',
                    next: '›',
                    previous: '‹'
                }
            },

            layout: {
                topStart: ['pageLength',
                { buttons: ['colvis'] }],
                topEnd: 'search'
            },

            pageLength: 10,
            lengthMenu: [10,25,50,100],
            order: [[0,'asc']],

            columnDefs: [
                {
                    targets: [0],
                    searchable: false,
                    width: '50px',
                    className: 'dt-center'
                },
                {
                    targets: [3],
                    orderable: false,
                    searchable: false,
                    width: '160px',
                    className: 'dt-center'
                }
            ]
        });

        dt.on('draw', updateTotal);
        updateTotal();
    });

    function updateTotal() {
        if (!dt || !totalSpan) return;
        totalSpan.textContent = dt.rows({ search: 'applied' }).count();
    }

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
       HELPER: MANIPULASI BARIS DT
    ══════════════════════════════════════════ */
    function makeAksiHtml(id, namaEvent, jenis, updateUrl = '', destroyUrl = '') {
        return `
        <div class="btn-aksi-wrap d-flex gap-2 justify-content-center">
            <button
                class="btn btn-warning btn-sm btn-edit-event"
                data-id="${id}"
                data-nama-event="${namaEvent}"
                data-jenis="${jenis}"
                data-url="${updateUrl}">
                Ubah
            </button>

            <button
                class="btn btn-danger btn-sm btn-hapus-event"
                data-id="${id}"
                data-nama="${namaEvent}"
                data-url="${destroyUrl}">
                Hapus
            </button>
        </div>`;
    }

    function updateRow(id, namaEvent, jenis) {
        const editBtn = tbody.querySelector(`.btn-edit-event[data-id="${id}"]`);
        if (!editBtn) return;
        const tr = editBtn.closest('tr');
        tr.cells[1].textContent = namaEvent;
        tr.cells[2].textContent = jenis;
        editBtn.dataset.namaEvent = namaEvent;
        editBtn.dataset.jenis = jenis;
        const hapusBtn = tr.querySelector('.btn-hapus-event');
        if (hapusBtn) hapusBtn.dataset.nama = namaEvent;
        if (dt) dt.row(tr).invalidate('dom').draw(false);
    }

    function appendRow(event) {
        if (!dt) return;
        const newNo = dt.rows().count() + 1;
        dt.row.add([
            newNo,
            event.nama_event,
            event.jenis ?? '-',
            makeAksiHtml(
                event.id,
                event.nama_event,
                event.jenis ?? '',
                event.update_url,
                event.destroy_url
            ),
        ]).draw(false);
        updateTotal();
    }

    /* ══════════════════════════════════════════
       HELPER: LOADING STATE
    ══════════════════════════════════════════ */
    function setSimpanLoading(on) {
        const btn   = document.getElementById('btnSimpanEvent');
        const batal = document.getElementById('btnBatalEvent');
        const tutup = document.getElementById('btnTutupModalEvent');
        btn.disabled    = on;
        btn.textContent = on ? 'Menyimpan...' : 'Simpan';
        batal.disabled  = on;
        tutup.disabled  = on;
    }

    function setHapusLoading(on) {
        const btn   = document.getElementById('btnHapusEvent');
        const batal = document.getElementById('btnBatalHapus');
        btn.disabled    = on;
        btn.textContent = on ? 'Menghapus...' : 'Hapus';
        batal.disabled  = on;
    }

    /* ══════════════════════════════════════════
       MODAL: TAMBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnTambahEvent').addEventListener('click', () => {
        activeMode = 'store';
        activeUpdateId = activeUpdateUrl = null;
        document.getElementById('modalEventTitle').textContent = 'Tambah Event';
        document.getElementById('inputNamaEvent').value = '';
        document.getElementById('inputJenis').value     = '';
        setSimpanLoading(false);
        modalEvent.show();
    });

    /* ══════════════════════════════════════════
       MODAL: UBAH / HAPUS (event delegation)
    ══════════════════════════════════════════ */
    tbody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-event');
        if (editBtn) {
            activeMode      = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;
            document.getElementById('modalEventTitle').textContent = 'Ubah Event';
            document.getElementById('inputNamaEvent').value = editBtn.dataset.namaEvent;
            document.getElementById('inputJenis').value     = editBtn.dataset.jenis;
            setSimpanLoading(false);
            modalEvent.show();
            return;
        }

        const hapusBtn = e.target.closest('.btn-hapus-event');
        if (hapusBtn) {
            activeHapusId   = hapusBtn.dataset.id;
            activeHapusUrl  = hapusBtn.dataset.url;
            activeHapusNama = hapusBtn.dataset.nama;
            document.getElementById('namaEventHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapus.show();
        }
    });

    /* ══════════════════════════════════════════
       MODAL: TUTUP MANUAL
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupModalEvent').addEventListener('click', () => { if (!isSaving)   modalEvent.hide(); });
    document.getElementById('btnBatalEvent').addEventListener('click',      () => { if (!isSaving)   modalEvent.hide(); });
    document.getElementById('btnBatalHapus').addEventListener('click',      () => { if (!isDeleting) modalHapus.hide(); });

    /* ══════════════════════════════════════════
       SUBMIT: TAMBAH / UBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnSimpanEvent').addEventListener('click', async () => {
        if (isSaving) return;

        const namaEvent = document.getElementById('inputNamaEvent').value.trim();
        const jenis     = document.getElementById('inputJenis').value;

        if (!namaEvent) { document.getElementById('inputNamaEvent').focus(); return; }
        if (!jenis)     { document.getElementById('inputJenis').focus();     return; }

        isSaving = true;
        setSimpanLoading(true);

        try {
            const isUpdate = activeMode === 'update';
            const res = await sendRequest(isUpdate ? activeUpdateUrl : STORE_URL, {
                nama_event: namaEvent,
                jenis,
                _method: isUpdate ? 'PUT' : 'POST',
            });
            if (res.success) {
                modalEvent.hide();
                toast(isUpdate ? 'Event berhasil diubah!' : 'Event berhasil ditambahkan!');
                isUpdate ? updateRow(activeUpdateId, namaEvent, jenis) : appendRow(res.event);
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
    document.getElementById('btnHapusEvent').addEventListener('click', async () => {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapus.hide();
                toast(`Event "${activeHapusNama}" berhasil dihapus!`);
                const hapusBtn = tbody.querySelector(`.btn-hapus-event[data-id="${activeHapusId}"]`);
                if (hapusBtn && dt) dt.row(hapusBtn.closest('tr')).remove().draw(false);
                updateTotal();
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