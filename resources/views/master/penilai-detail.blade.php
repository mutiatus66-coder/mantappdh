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
<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Penilai — {{ $subEvent->sub_event }}</h3>
            <p>{{ $subEvent->event->nama_event ?? '-' }} · {{ $subEvent->tahun }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('penilai.index') }}" class="btn btn-dark">← Kembali</a>
            <button class="btn btn-primary" id="btnTambahPenilai">Tambah Penilai</button>
        </div>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Penilai: <span id="totalPenilai">{{ $penilai->count() }}</span>
        </div>
    </div>

    {{--
        Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
        nggak perlu overflow-x wrapper karna DT mengelola scroll sendiri.
    --}}
    <table id="tabelPenilai" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Penilai</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelPenilaiBody">
            @forelse($penilai as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->email }}</td>
                <td>
                    <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
                        <button class="btn btn-warning btn-sm btn-edit-penilai"
                                data-id="{{ $p->id }}"
                                data-user-id="{{ $p->user_id }}"
                                data-nama="{{ $p->nama }}"
                                data-email="{{ $p->email }}"
                                data-url="{{ route('penilai.update', $p->id) }}">
                            Ubah
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus-penilai"
                                data-id="{{ $p->id }}"
                                data-nama="{{ $p->nama }}"
                                data-url="{{ route('penilai.destroy', $p->id) }}">
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

{{-- ===== MODAL: Tambah / Ganti Penilai ===== --}}
<div class="modal fade" id="modalPenilai" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalPenilaiTitle">Tambah Penilai</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalPenilai" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <div class="modal-body px-5 py-4">

                {{-- PILIH USER --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold required">Pilih User Penilai</label>
                    <select id="penilaiUserId" class="form-select">
                        <option value="" disabled selected>Pilih User Penilai...</option>
                        @foreach($usersPenilai as $u)
                        <option value="{{ $u->id }}"
                                data-nama="{{ $u->nama }}"
                                data-email="{{ $u->email }}">
                            {{ $u->nama }} ({{ $u->email }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PREVIEW NAMA & EMAIL (otomatis terisi saat user dipilih) --}}
                <div id="previewPenilai" class="p-2 bg-light rounded d-none">
                    <small class="text-muted">
                        Nama: <span id="previewNama" class="fw-semibold"></span>
                        &nbsp;|&nbsp;
                        Email: <span id="previewEmail" class="fw-semibold"></span>
                    </small>
                </div>

            </div>
            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalPenilai">Batal</button>
                <button type="button" id="btnSimpanPenilai" class="btn btn-success px-4">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: Konfirmasi Hapus ===== --}}
<div class="modal fade" id="modalHapusPenilai" tabindex="-1"
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
                Penilai <strong id="namaPenilaiHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapusPenilai">Batal</button>
                <button type="button" id="btnHapusPenilai" class="btn btn-danger btn-sm px-3">Hapus</button>
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
    const SUB_EVENT_ID = {{ $subEvent->id }};
    const STORE_URL    = "{{ route('penilai.store') }}";
    const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody        = document.getElementById('tabelPenilaiBody');
    const totalSpan    = document.getElementById('totalPenilai');

    const modalPenilaiEl = document.getElementById('modalPenilai');
    const modalHapusEl   = document.getElementById('modalHapusPenilai');
    const modalPenilai   = new bootstrap.Modal(modalPenilaiEl);
    const modalHapus     = new bootstrap.Modal(modalHapusEl);

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
        dt = $('#tabelPenilai').DataTable({
            responsive: true,

            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : { first: '«', last: '»', next: '›', previous: '‹' },
                emptyTable  : 'Belum ada penilai untuk sub event ini.',
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
                    targets   : [0],
                    searchable: false,
                    width     : '50px',
                    className : 'dt-center',
                },
                {
                    targets   : [3],
                    orderable : false,
                    searchable: false,
                    width     : '180px',
                    className : 'dt-center',
                },
            ],
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
        Object.entries(data).forEach(([k, v]) => {
            if (v !== null && v !== undefined && v !== '') form.append(k, v);
        });
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
    function makeAksiHtml(id, nama, email, userId, updateUrl, destroyUrl) {
        return `
        <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
            <button class="btn btn-warning btn-sm btn-edit-penilai"
                    data-id="${id}"
                    data-user-id="${userId}"
                    data-nama="${nama}"
                    data-email="${email}"
                    data-url="${updateUrl}">
                Ubah
            </button>
            <button class="btn btn-danger btn-sm btn-hapus-penilai"
                    data-id="${id}"
                    data-nama="${nama}"
                    data-url="${destroyUrl}">
                Hapus
            </button>
        </div>`;
    }

    function updateRow(id, nama, email, userId) {
        const editBtn = tbody.querySelector(`.btn-edit-penilai[data-id="${id}"]`);
        if (!editBtn) return;
        const tr = editBtn.closest('tr');
        tr.cells[1].textContent = nama;
        tr.cells[2].textContent = email;
        editBtn.dataset.nama   = nama;
        editBtn.dataset.email  = email;
        editBtn.dataset.userId = userId;
        const hapusBtn = tr.querySelector('.btn-hapus-penilai');
        if (hapusBtn) hapusBtn.dataset.nama = nama;
        if (dt) dt.row(tr).invalidate('dom').draw(false);
    }

    function appendRow(p) {
        if (!dt) return;
        const newNo = dt.rows().count() + 1;
        dt.row.add([
            newNo,
            p.nama,
            p.email,
            makeAksiHtml(p.id, p.nama, p.email, p.user_id, p.update_url, p.destroy_url),
        ]).draw(false);
        updateTotal();
    }

    /* ══════════════════════════════════════════
       HELPER: LOADING STATE
    ══════════════════════════════════════════ */
    function setSimpanLoading(on) {
        const btn   = document.getElementById('btnSimpanPenilai');
        const batal = document.getElementById('btnBatalPenilai');
        const tutup = document.getElementById('btnTutupModalPenilai');
        btn.disabled    = on;
        btn.textContent = on ? 'Menyimpan...' : 'Simpan';
        batal.disabled  = on;
        tutup.disabled  = on;
    }

    function setHapusLoading(on) {
        const btn   = document.getElementById('btnHapusPenilai');
        const batal = document.getElementById('btnBatalHapusPenilai');
        btn.disabled    = on;
        btn.textContent = on ? 'Menghapus...' : 'Hapus';
        batal.disabled  = on;
    }

    /* ══════════════════════════════════════════
       PREVIEW USER saat dropdown berubah
    ══════════════════════════════════════════ */
    document.getElementById('penilaiUserId').addEventListener('change', function () {
        const selected  = this.options[this.selectedIndex];
        const previewEl = document.getElementById('previewPenilai');
        if (selected.value) {
            previewEl.classList.remove('d-none');
            document.getElementById('previewNama').textContent  = selected.dataset.nama;
            document.getElementById('previewEmail').textContent = selected.dataset.email;
        } else {
            previewEl.classList.add('d-none');
        }
    });

    /* ══════════════════════════════════════════
       MODAL: TAMBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnTambahPenilai').addEventListener('click', () => {
        activeMode = 'store';
        activeUpdateId = activeUpdateUrl = null;
        document.getElementById('modalPenilaiTitle').textContent = 'Tambah Penilai';
        document.getElementById('penilaiUserId').value = '';
        document.getElementById('previewPenilai').classList.add('d-none');
        setSimpanLoading(false);
        modalPenilai.show();
    });

    /* ══════════════════════════════════════════
       MODAL: UBAH / HAPUS (event delegation)
    ══════════════════════════════════════════ */
    tbody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-penilai');
        if (editBtn) {
            activeMode      = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;
            document.getElementById('modalPenilaiTitle').textContent = 'Ganti Penilai';
            document.getElementById('penilaiUserId').value = editBtn.dataset.userId;
            document.getElementById('penilaiUserId').dispatchEvent(new Event('change'));
            setSimpanLoading(false);
            modalPenilai.show();
            return;
        }

        const hapusBtn = e.target.closest('.btn-hapus-penilai');
        if (hapusBtn) {
            activeHapusId   = hapusBtn.dataset.id;
            activeHapusUrl  = hapusBtn.dataset.url;
            activeHapusNama = hapusBtn.dataset.nama;
            document.getElementById('namaPenilaiHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapus.show();
        }
    });

    /* ══════════════════════════════════════════
       MODAL: TUTUP MANUAL
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupModalPenilai').addEventListener('click', () => { if (!isSaving)   modalPenilai.hide(); });
    document.getElementById('btnBatalPenilai').addEventListener('click',      () => { if (!isSaving)   modalPenilai.hide(); });
    document.getElementById('btnBatalHapusPenilai').addEventListener('click', () => { if (!isDeleting) modalHapus.hide(); });

    /* ══════════════════════════════════════════
       SUBMIT: TAMBAH / UBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnSimpanPenilai').addEventListener('click', async () => {
        if (isSaving) return;

        const userId = document.getElementById('penilaiUserId').value;
        if (!userId) { toast('Harap pilih user penilai.', 'error'); return; }

        isSaving = true;
        setSimpanLoading(true);

        try {
            const isUpdate = activeMode === 'update';
            const res = await sendRequest(isUpdate ? activeUpdateUrl : STORE_URL, {
                _method     : isUpdate ? 'PUT' : 'POST',
                sub_event_id: SUB_EVENT_ID,
                user_id     : userId,
            });

            if (res.success) {
                modalPenilai.hide();
                toast(isUpdate ? 'Penilai berhasil diganti!' : 'Penilai berhasil ditambahkan!');
                isUpdate
                    ? updateRow(activeUpdateId, res.penilai.nama, res.penilai.email, res.penilai.user_id)
                    : appendRow(res.penilai);
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
    document.getElementById('btnHapusPenilai').addEventListener('click', async () => {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapus.hide();
                toast(`Penilai "${activeHapusNama}" berhasil dihapus!`);
                const hapusBtn = tbody.querySelector(`.btn-hapus-penilai[data-id="${activeHapusId}"]`);
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