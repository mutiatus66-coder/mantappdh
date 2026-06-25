@extends('index', ['dummy' => true])

@push('styles')
    <link rel="stylesheet" href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}">

    {{--
        PENTING: Jangan pakai datatables.css lokal karena versi nya lama (1.x),
        nanti tampilan DT v2.x + ColumnControl berantakan.
        Pakai CDN saja —Regan.
    --}}
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
          rel="stylesheet"
          integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
          crossorigin="anonymous">
@endpush

@section('content')
<div class="page-container">

    {{-- Header --}}
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Master Pengumuman</h3>
            <p>Kelola pengumuman yang ditampilkan ke publik</p>
        </div>
        <button class="btn btn-primary" id="btnTambahPengumuman">Tambah Pengumuman</button>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Pengumuman: <span id="totalPengumuman">{{ $pengumuman->count() }}</span>
        </div>
    </div>

    {{--
        Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
        Tidak perlu overflow-x wrapper karena DT mengelola scroll sendiri.
    --}}
    <table id="tabelPengumuman" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>File</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelPengumumanBody">
            @forelse($pengumuman as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->judul }}</td>
                <td>{{ Str::limit($p->deskripsi, 80) }}</td>
                <td>
                    <span class="badge-kategori {{ $p->status === 'Published' ? 'status-published' : 'status-draft' }}">
                        {{ $p->status }}
                    </span>
                </td>
                <td>
                    @if($p->file_path)
                        <a href="{{ asset('storage/' . $p->file_path) }}" target="_blank">Lihat File</a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
                        <button class="btn btn-warning btn-sm btn-edit-pengumuman"
                                data-id="{{ $p->id }}"
                                data-judul="{{ $p->judul }}"
                                data-deskripsi="{{ $p->deskripsi }}"
                                data-status="{{ $p->status }}"
                                data-url="{{ route('pengumuman.update', $p->id) }}">
                            Ubah
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus-pengumuman"
                                data-id="{{ $p->id }}"
                                data-nama="{{ $p->judul }}"
                                data-url="{{ route('pengumuman.destroy', $p->id) }}">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data pengumuman
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>


{{-- ===== MODAL: Tambah / Ubah Pengumuman ===== --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalPengumumanTitle">Tambah Pengumuman</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalPengumuman" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <div class="modal-body px-5 py-4">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Judul</label>
                        <input type="text" id="pJudul" class="form-control"
                               placeholder="Masukkan judul pengumuman">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea id="pDeskripsi" class="form-control" rows="4"
                                  placeholder="Masukkan deskripsi pengumuman"></textarea>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold required">Status</label>
                        <select id="pStatus" class="form-select">
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">File (PDF/DOC/DOCX/JPG/PNG, maks 2MB)</label>
                        <input type="file" id="pFile" class="form-control"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp,.svg">
                    </div>
                    <div class="col-md-12 mb-2" id="previewWrap" style="display:none;">
                        <img id="previewGambar" src="" alt="Preview"
                             style="max-height:180px;border-radius:8px;border:1px solid #e5e7eb;object-fit:cover;">
                    </div>
                </div>
            </div>

            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalPengumuman">Batal</button>
                <button type="button" id="btnSimpanPengumuman" class="btn btn-success px-4">Simpan</button>
            </div>

        </div>
    </div>
</div>


{{-- ===== MODAL: Konfirmasi Hapus Pengumuman ===== --}}
<div class="modal fade" id="modalHapusPengumuman" tabindex="-1"
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
                Tindakan ini tidak dapat dibatalkan. Pengumuman
                <strong id="namaPengumumanHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapusPengumuman">Batal</button>
                <button type="button" id="btnHapusPengumuman" class="btn btn-danger btn-sm px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')
{{-- JANGAN load DT core JS lokal karena kemungkinan versi lawas --}}
<script src="{{ asset('assets/jquery/jquery-4.0.0.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"
        integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"
        integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js"
        integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ"
        crossorigin="anonymous"></script>

<script>
(function () {
    'use strict';

    /* ══════════════════════════════════════════
       KONSTANTA & ELEMEN
    ══════════════════════════════════════════ */
    const STORE_URL = "{{ route('pengumuman.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody     = document.getElementById('tabelPengumumanBody');
    const totalSpan = document.getElementById('totalPengumuman');

    const modalPengumumanEl = document.getElementById('modalPengumuman');
    const modalHapusEl      = document.getElementById('modalHapusPengumuman');
    const modalPengumuman   = new bootstrap.Modal(modalPengumumanEl);
    const modalHapus        = new bootstrap.Modal(modalHapusEl);

    /* ══════════════════════════════════════════
       STATE
    ══════════════════════════════════════════ */
    let activeMode      = 'store';
    let activeUpdateId  = null, activeUpdateUrl = null;
    let activeHapusId   = null, activeHapusUrl  = null, activeHapusNama = null;
    let isSaving        = false, isDeleting = false;
    let dt              = null;

    /* ══════════════════════════════════════════
       INIT DATATABLES
    ══════════════════════════════════════════ */
    $(document).ready(function () {
        dt = $('#tabelPengumuman').DataTable({
            responsive: true,

            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : { first: '«', last: '»', next: '›', previous: '‹' },
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
                    // Kolom No: tidak perlu di-search, lebar tetap, center
                    targets    : [0],
                    searchable : false,
                    width      : '50px',
                    className  : 'dt-center',
                },
                {
                    // Kolom Status: center
                    targets  : [3],
                    className: 'dt-center',
                },
                {
                    // Kolom File: center, tidak sortable
                    targets   : [4],
                    orderable : false,
                    className : 'dt-center',
                },
                {
                    // Kolom Aksi: tidak sortable, tidak bisa dicari, center
                    targets   : [5],
                    orderable : false,
                    searchable: false,
                    width     : '180px',
                    className : 'dt-center',
                },
            ],
        });

        // Update counter setiap kali DT menggambar ulang (search/filter/paginate)
        dt.on('draw', updateTotal);
        updateTotal();
    });

    /* ══════════════════════════════════════════
       HELPER: UPDATE TOTAL COUNTER
       Menghitung baris yang sedang ditampilkan
       (setelah filter/search diterapkan)
    ══════════════════════════════════════════ */
    function updateTotal() {
        if (!dt || !totalSpan) return;
        totalSpan.textContent = dt.rows({ search: 'applied' }).count();
    }

    /* ══════════════════════════════════════════
       HELPER: AJAX — kirim FormData (multipart)
       Digunakan untuk store/update karena ada
       file upload (tidak bisa pakai JSON biasa)
    ══════════════════════════════════════════ */
    async function sendFormData(url, formData) {
        const res = await fetch(url, {
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body   : formData,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

    /* ══════════════════════════════════════════
       HELPER: AJAX — kirim JSON biasa
       Digunakan untuk DELETE (tidak ada file)
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
       HELPER: TOAST NOTIFIKASI
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
       HELPER: BUILD HTML BADGE STATUS
    ══════════════════════════════════════════ */
    function makeBadgeStatus(status) {
        const cls = status === 'Published' ? 'status-published' : 'status-draft';
        return `<span class="badge-kategori ${cls}">${status}</span>`;
    }

    /* ══════════════════════════════════════════
       HELPER: BUILD HTML KOLOM FILE
    ══════════════════════════════════════════ */
    function makeFileHtml(fileUrl) {
        return fileUrl ? `<a href="${fileUrl}" target="_blank">Lihat File</a>` : '-';
    }

    /* ══════════════════════════════════════════
       HELPER: BUILD HTML KOLOM AKSI
    ══════════════════════════════════════════ */
    function makeAksiHtml(id, judul, deskripsi, status, updateUrl, destroyUrl) {
        return `
        <div class="btn-aksi-wrap d-flex gap-2 justify-content-center">
            <button class="btn btn-warning btn-sm btn-edit-pengumuman"
                    data-id="${id}"
                    data-judul="${judul}"
                    data-deskripsi="${deskripsi ?? ''}"
                    data-status="${status}"
                    data-url="${updateUrl}">
                Ubah
            </button>
            <button class="btn btn-danger btn-sm btn-hapus-pengumuman"
                    data-id="${id}"
                    data-nama="${judul}"
                    data-url="${destroyUrl}">
                Hapus
            </button>
        </div>`;
    }

    /* ══════════════════════════════════════════
       HELPER: UPDATE BARIS DT (setelah ubah)
       Invalidate DOM supaya DT sinkron dengan
       data yang baru tanpa full reload
    ══════════════════════════════════════════ */
    function updateRow(id, judul, deskripsi, status, fileUrl) {
        const editBtn = tbody.querySelector(`.btn-edit-pengumuman[data-id="${id}"]`);
        if (!editBtn) return;

        const tr = editBtn.closest('tr');

        // Update teks sel
        tr.cells[1].textContent = judul;
        tr.cells[2].textContent = deskripsi.length > 80
            ? deskripsi.substring(0, 80) + '...'
            : deskripsi;
        tr.cells[3].innerHTML = makeBadgeStatus(status);
        tr.cells[4].innerHTML = makeFileHtml(fileUrl);

        // Update data-* pada tombol agar modal ubah berikutnya terisi benar
        editBtn.dataset.judul     = judul;
        editBtn.dataset.deskripsi = deskripsi;
        editBtn.dataset.status    = status;

        const hapusBtn = tr.querySelector('.btn-hapus-pengumuman');
        if (hapusBtn) hapusBtn.dataset.nama = judul;

        // Beritahu DT bahwa DOM baris ini berubah
        if (dt) dt.row(tr).invalidate('dom').draw(false);
    }

    /* ══════════════════════════════════════════
       HELPER: TAMBAH BARIS BARU KE DT
       (setelah store berhasil)
    ══════════════════════════════════════════ */
    function appendRow(p) {
        if (!dt) return;

        const newNo = dt.rows().count() + 1;

        dt.row.add([
            newNo,
            p.judul,
            p.deskripsi
                ? (p.deskripsi.length > 80 ? p.deskripsi.substring(0, 80) + '...' : p.deskripsi)
                : '',
            makeBadgeStatus(p.status),
            makeFileHtml(p.file_url),
            makeAksiHtml(
                p.id,
                p.judul,
                p.deskripsi ?? '',
                p.status,
                p.update_url,
                p.destroy_url
            ),
        ]).draw(false);

        updateTotal();
    }

    /* ══════════════════════════════════════════
       HELPER: LOADING STATE — TOMBOL SIMPAN
    ══════════════════════════════════════════ */
    function setSimpanLoading(on) {
        const btn   = document.getElementById('btnSimpanPengumuman');
        const batal = document.getElementById('btnBatalPengumuman');
        const tutup = document.getElementById('btnTutupModalPengumuman');
        btn.disabled    = on;
        btn.textContent = on ? 'Menyimpan...' : 'Simpan';
        batal.disabled  = on;
        tutup.disabled  = on;
    }

    /* ══════════════════════════════════════════
       HELPER: LOADING STATE — TOMBOL HAPUS
    ══════════════════════════════════════════ */
    function setHapusLoading(on) {
        const btn   = document.getElementById('btnHapusPengumuman');
        const batal = document.getElementById('btnBatalHapusPengumuman');
        btn.disabled    = on;
        btn.textContent = on ? 'Menghapus...' : 'Hapus';
        batal.disabled  = on;
    }

    /* ══════════════════════════════════════════
       HELPER: RESET FORM MODAL PENGUMUMAN
    ══════════════════════════════════════════ */
    function resetFormModal() {
        document.getElementById('pJudul').value     = '';
        document.getElementById('pDeskripsi').value = '';
        document.getElementById('pStatus').value    = '';
        document.getElementById('pFile').value      = '';
        document.getElementById('previewWrap').style.display = 'none';
        document.getElementById('previewGambar').src         = '';
    }

    /* ══════════════════════════════════════════
       PREVIEW FILE GAMBAR
       Tampilkan preview jika file yang dipilih
       adalah gambar, sembunyikan jika bukan
    ══════════════════════════════════════════ */
    document.getElementById('pFile').addEventListener('change', function () {
        const file = this.files[0];
        const wrap = document.getElementById('previewWrap');
        const img  = document.getElementById('previewGambar');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
            reader.readAsDataURL(file);
        } else {
            wrap.style.display = 'none';
            img.src = '';
        }
    });

    /* ══════════════════════════════════════════
       MODAL: TAMBAH — buka modal dalam mode store
    ══════════════════════════════════════════ */
    document.getElementById('btnTambahPengumuman').addEventListener('click', () => {
        activeMode = 'store';
        activeUpdateId = activeUpdateUrl = null;
        document.getElementById('modalPengumumanTitle').textContent = 'Tambah Pengumuman';
        resetFormModal();
        setSimpanLoading(false);
        modalPengumuman.show();
    });

    /* ══════════════════════════════════════════
       MODAL: UBAH / HAPUS — event delegation
       Tangkap klik pada tombol di dalam tbody,
       termasuk baris yang ditambah secara dinamis
    ══════════════════════════════════════════ */
    tbody.addEventListener('click', function (e) {

        // ── Tombol Ubah ──
        const editBtn = e.target.closest('.btn-edit-pengumuman');
        if (editBtn) {
            activeMode      = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;

            document.getElementById('modalPengumumanTitle').textContent = 'Ubah Pengumuman';
            document.getElementById('pJudul').value                     = editBtn.dataset.judul;
            document.getElementById('pDeskripsi').value                 = editBtn.dataset.deskripsi;
            document.getElementById('pStatus').value                    = editBtn.dataset.status;

            // Reset file input & preview saat ubah
            document.getElementById('pFile').value              = '';
            document.getElementById('previewWrap').style.display = 'none';
            document.getElementById('previewGambar').src         = '';

            setSimpanLoading(false);
            modalPengumuman.show();
            return;
        }

        // ── Tombol Hapus ──
        const hapusBtn = e.target.closest('.btn-hapus-pengumuman');
        if (hapusBtn) {
            activeHapusId   = hapusBtn.dataset.id;
            activeHapusUrl  = hapusBtn.dataset.url;
            activeHapusNama = hapusBtn.dataset.nama;

            document.getElementById('namaPengumumanHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapus.show();
        }
    });

    /* ══════════════════════════════════════════
       MODAL: TUTUP MANUAL
       Block dismiss saat proses sedang berjalan
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupModalPengumuman').addEventListener('click', () => {
        if (!isSaving) modalPengumuman.hide();
    });
    document.getElementById('btnBatalPengumuman').addEventListener('click', () => {
        if (!isSaving) modalPengumuman.hide();
    });
    document.getElementById('btnBatalHapusPengumuman').addEventListener('click', () => {
        if (!isDeleting) modalHapus.hide();
    });

    /* ══════════════════════════════════════════
       SUBMIT: TAMBAH / UBAH PENGUMUMAN
       Menggunakan FormData karena ada file upload
    ══════════════════════════════════════════ */
    document.getElementById('btnSimpanPengumuman').addEventListener('click', async () => {
        if (isSaving) return;

        const judul     = document.getElementById('pJudul').value.trim();
        const deskripsi = document.getElementById('pDeskripsi').value.trim();
        const status    = document.getElementById('pStatus').value;
        const fileInput = document.getElementById('pFile');

        // Validasi sisi klien
        if (!judul)  { document.getElementById('pJudul').focus();  return; }
        if (!status) { document.getElementById('pStatus').focus(); return; }

        isSaving = true;
        setSimpanLoading(true);

        const isUpdate = activeMode === 'update';

        // Simpan referensi sebelum async (state bisa berubah)
        const updateId  = activeUpdateId;
        const updateUrl = activeUpdateUrl;

        try {
            const fd = new FormData();
            fd.append('_method',   isUpdate ? 'PUT' : 'POST');
            fd.append('judul',     judul);
            fd.append('deskripsi', deskripsi);
            fd.append('status',    status);
            if (fileInput.files[0]) fd.append('file', fileInput.files[0]);

            const url = isUpdate ? updateUrl : STORE_URL;
            const res = await sendFormData(url, fd);

            if (res.success) {
                modalPengumuman.hide();
                toast(isUpdate ? 'Pengumuman berhasil diubah!' : 'Pengumuman berhasil ditambahkan!');
                isUpdate
                    ? updateRow(updateId, judul, deskripsi, status, res.file_url)
                    : appendRow(res.pengumuman);
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
       SUBMIT: HAPUS PENGUMUMAN
    ══════════════════════════════════════════ */
    document.getElementById('btnHapusPengumuman').addEventListener('click', async () => {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });

            if (res.success) {
                modalHapus.hide();
                toast(`Pengumuman "${activeHapusNama}" berhasil dihapus!`);

                // Cari dan hapus baris dari DT
                const hapusBtn = tbody.querySelector(`.btn-hapus-pengumuman[data-id="${activeHapusId}"]`);
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