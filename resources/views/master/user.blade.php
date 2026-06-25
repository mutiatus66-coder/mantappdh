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
@php
$hakAksesLabel = [
    'admin_bapperida' => 'Admin Bapperida',
    'peserta'         => 'Peserta',
    'penilai'         => 'Penilai',
];
@endphp

<div class="page-container">

    {{-- Header --}}
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data User</h3>
            <p>Kelola semua user yang terdaftar</p>
        </div>
        <button class="btn btn-primary" id="btnTambahUser">Tambah User</button>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total User: <span id="totalUser">{{ $users->count() }}</span>
        </div>
    </div>

    {{--
        Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
        nggak perlu overflow-x wrapper karna DT mengelola scroll sendiri.
    --}}
    <table id="tabelUser" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Hak Akses</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelUserBody">
            @forelse($users ?? [] as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $hakAksesLabel[$item->hak_akses] ?? $item->hak_akses }}</td>
                <td>{{ $item->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</td>
                <td>
                    @if(Auth::id() !== $item->id)
                    <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
                        <button class="btn btn-warning btn-sm btn-edit-user"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama }}"
                                data-email="{{ $item->email }}"
                                data-hak-akses="{{ $item->hak_akses }}"
                                data-status="{{ $item->status }}"
                                data-url="{{ route('user.update', $item->id) }}">
                            Ubah
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus-user"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama }}"
                                data-url="{{ route('user.destroy', $item->id) }}">
                            Hapus
                        </button>
                        <a href="{{ route('user.login-as', $item->id) }}"
                           class="btn btn-success btn-sm">
                            Login As
                        </a>
                    </div>
                    @else
                    <span class="text-muted small fst-italic">Akun Anda</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data user
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

{{-- ===== MODAL: Tambah / Ubah User ===== --}}
<div class="modal fade" id="modalUser" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalUserTitle">Tambah User</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalUser" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <div class="modal-body px-5 py-4">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Nama</label>
                        <input type="text" id="inputNama" class="form-control"
                               placeholder="Masukkan nama...">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Email</label>
                        <input type="email" id="inputEmail" class="form-control"
                               placeholder="Masukkan email...">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Hak Akses</label>
                        <select id="inputHakAkses" class="form-select">
                            <option value="" disabled selected>Pilih Hak Akses</option>
                            <option value="admin_bapperida">Admin Bapperida</option>
                            <option value="peserta">Peserta</option>
                            <option value="penilai">Penilai</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <div class="d-flex gap-4 mt-1">
                            <label class="d-flex align-items-center gap-2"
                                   style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="statusUser" id="statusAktif" value="aktif" checked> Aktif
                            </label>
                            <label class="d-flex align-items-center gap-2"
                                   style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="statusUser" id="statusNonaktif" value="nonaktif"> Nonaktif
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label fw-semibold" id="labelPassword">Password</label>
                        <input type="password" id="inputPassword" class="form-control"
                               placeholder="Masukkan password...">
                        <div id="passwordHint"
                             style="display:none; font-size:0.78rem; color:var(--ri-text-muted); margin-top:4px;">
                            Kosongkan jika tidak ingin mengubah password
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalUser">Batal</button>
                <button type="button" id="btnSimpanUser" class="btn btn-success px-4">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: Konfirmasi Hapus ===== --}}
<div class="modal fade" id="modalHapusUser" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">
            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem;color:#dc2626;"></i>
                </div>
            </div>
            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus User Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem;line-height:1.6;">
                Tindakan ini tidak dapat dibatalkan. User
                <strong id="namaUserHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapusUser">Batal</button>
                <button type="button" id="btnHapusUser" class="btn btn-danger btn-sm px-3">Hapus</button>
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
    const STORE_URL = "{{ route('user.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody     = document.getElementById('tabelUserBody');
    const totalSpan = document.getElementById('totalUser');

    const HAK_AKSES_LABEL = {
        'admin_bapperida': 'Admin Bapperida',
        'peserta':         'Peserta',
        'penilai':         'Penilai',
    };
    function labelHakAkses(val) {
        return HAK_AKSES_LABEL[val] ?? val;
    }

    const modalUserEl  = document.getElementById('modalUser');
    const modalHapusEl = document.getElementById('modalHapusUser');
    const modalUser    = new bootstrap.Modal(modalUserEl);
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
        dt = $('#tabelUser').DataTable({
            responsive: true,

            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : {
                    first: '«', last: '»', next: '›', previous: '‹'
                },
                emptyTable  : 'Belum ada data user.',
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
                    targets   : [3, 4],
                    width     : '130px',
                    className : 'dt-center',
                },
                {
                    targets   : [5],
                    orderable : false,
                    searchable: false,
                    width     : '240px',
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
       HELPER: RENDER BADGE
    ══════════════════════════════════════════ */
    function badgeStatus(status) {
        return status === 'aktif'
            ? `<span class="badge-aktif px-3 py-2">Aktif</span>`
            : `<span class="badge-nonaktif px-3 py-2">Nonaktif</span>`;
    }

    function badgeHakAkses(val) {
        return `<span class="badge-kategori">${labelHakAkses(val)}</span>`;
    }

    /* ══════════════════════════════════════════
       HELPER: MANIPULASI BARIS DT
    ══════════════════════════════════════════ */
    function makeAksiHtml(id, nama, email, hakAkses, status, updateUrl, destroyUrl, loginUrl) {
        return `
        <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
            <button class="btn btn-warning btn-sm btn-edit-user"
                    data-id="${id}"
                    data-nama="${nama}"
                    data-email="${email}"
                    data-hak-akses="${hakAkses}"
                    data-status="${status}"
                    data-url="${updateUrl}">
                Ubah
            </button>
            <button class="btn btn-danger btn-sm btn-hapus-user"
                    data-id="${id}"
                    data-nama="${nama}"
                    data-url="${destroyUrl}">
                Hapus
            </button>
            <a href="${loginUrl}" class="btn btn-success btn-sm">Login As</a>
        </div>`;
    }

    function updateRow(id, nama, email, hakAkses, status) {
        const editBtn = tbody.querySelector(`.btn-edit-user[data-id="${id}"]`);
        if (!editBtn) return;
        const tr = editBtn.closest('tr');

        // Update data di sel DOM langsung
        tr.cells[1].textContent = nama;
        tr.cells[2].textContent = email;
        // Hak akses & status: simpan plain text di DT, render badge di DOM
        tr.cells[3].innerHTML   = badgeHakAkses(hakAkses);
        tr.cells[4].innerHTML   = badgeStatus(status);

        // Update data-attribute tombol
        editBtn.dataset.nama     = nama;
        editBtn.dataset.email    = email;
        editBtn.dataset.hakAkses = hakAkses;
        editBtn.dataset.status   = status;
        const hapusBtn = tr.querySelector('.btn-hapus-user');
        if (hapusBtn) hapusBtn.dataset.nama = nama;

        // Invalidate supaya DT tahu ada perubahan, lalu redraw tanpa pindah halaman
        if (dt) dt.row(tr).invalidate('dom').draw(false);
    }

    function appendRow(user) {
        if (!dt) return;
        const newNo = dt.rows().count() + 1;
        dt.row.add([
            newNo,
            user.nama,
            user.email,
            labelHakAkses(user.hak_akses),   // plain text untuk sorting DT
            user.status === 'aktif' ? 'Aktif' : 'Nonaktif',
            makeAksiHtml(
                user.id,
                user.nama,
                user.email,
                user.hak_akses,
                user.status,
                user.update_url,
                user.destroy_url,
                user.login_url
            ),
        ]).draw(false);

        // Render badge setelah baris ditambah (DT render plain text, kita ganti HTML-nya)
        const lastRow = dt.row(':last', { search: 'none' }).node();
        if (lastRow) {
            $(lastRow).find('td').eq(3).html(badgeHakAkses(user.hak_akses));
            $(lastRow).find('td').eq(4).html(badgeStatus(user.status));
        }

        updateTotal();
    }

    /* ══════════════════════════════════════════
       HELPER: LOADING STATE
    ══════════════════════════════════════════ */
    function setSimpanLoading(on) {
        const btn   = document.getElementById('btnSimpanUser');
        const batal = document.getElementById('btnBatalUser');
        const tutup = document.getElementById('btnTutupModalUser');
        btn.disabled    = on;
        btn.textContent = on ? 'Menyimpan...' : 'Simpan';
        batal.disabled  = on;
        tutup.disabled  = on;
    }

    function setHapusLoading(on) {
        const btn   = document.getElementById('btnHapusUser');
        const batal = document.getElementById('btnBatalHapusUser');
        btn.disabled    = on;
        btn.textContent = on ? 'Menghapus...' : 'Hapus';
        batal.disabled  = on;
    }

    /* ══════════════════════════════════════════
       MODAL: TAMBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnTambahUser').addEventListener('click', () => {
        activeMode = 'store';
        activeUpdateId = activeUpdateUrl = null;
        document.getElementById('modalUserTitle').textContent = 'Tambah User';
        document.getElementById('inputNama').value            = '';
        document.getElementById('inputEmail').value           = '';
        document.getElementById('inputHakAkses').value        = '';
        document.getElementById('inputPassword').value        = '';
        document.getElementById('statusAktif').checked        = true;
        document.getElementById('passwordHint').style.display = 'none';
        document.getElementById('labelPassword').textContent  = 'Password';
        setSimpanLoading(false);
        modalUser.show();
    });

    /* ══════════════════════════════════════════
       MODAL: UBAH / HAPUS (event delegation)
    ══════════════════════════════════════════ */
    tbody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-user');
        if (editBtn) {
            activeMode      = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;
            document.getElementById('modalUserTitle').textContent = 'Ubah User';
            document.getElementById('inputNama').value            = editBtn.dataset.nama;
            document.getElementById('inputEmail').value           = editBtn.dataset.email;
            document.getElementById('inputHakAkses').value        = editBtn.dataset.hakAkses;
            document.getElementById('inputPassword').value        = '';
            document.getElementById('passwordHint').style.display = 'block';
            document.getElementById('labelPassword').textContent  = 'Password (opsional)';
            const radioId = editBtn.dataset.status === 'nonaktif' ? 'statusNonaktif' : 'statusAktif';
            document.getElementById(radioId).checked = true;
            setSimpanLoading(false);
            modalUser.show();
            return;
        }

        const hapusBtn = e.target.closest('.btn-hapus-user');
        if (hapusBtn) {
            activeHapusId   = hapusBtn.dataset.id;
            activeHapusUrl  = hapusBtn.dataset.url;
            activeHapusNama = hapusBtn.dataset.nama;
            document.getElementById('namaUserHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapus.show();
        }
    });

    /* ══════════════════════════════════════════
       MODAL: TUTUP MANUAL
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupModalUser').addEventListener('click', () => { if (!isSaving)   modalUser.hide(); });
    document.getElementById('btnBatalUser').addEventListener('click',      () => { if (!isSaving)   modalUser.hide(); });
    document.getElementById('btnBatalHapusUser').addEventListener('click', () => { if (!isDeleting) modalHapus.hide(); });

    /* ══════════════════════════════════════════
       SUBMIT: TAMBAH / UBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnSimpanUser').addEventListener('click', async () => {
        if (isSaving) return;

        const nama     = document.getElementById('inputNama').value.trim();
        const email    = document.getElementById('inputEmail').value.trim();
        const hakAkses = document.getElementById('inputHakAkses').value;
        const status   = document.querySelector('input[name="statusUser"]:checked').value;
        const password = document.getElementById('inputPassword').value;
        const isUpdate = activeMode === 'update';

        if (!nama || !email || !hakAkses) {
            toast('Harap isi semua field yang wajib.', 'error');
            return;
        }
        if (!isUpdate && !password) {
            toast('Password wajib diisi untuk user baru.', 'error');
            return;
        }

        isSaving = true;
        setSimpanLoading(true);

        try {
            const res = await sendRequest(isUpdate ? activeUpdateUrl : STORE_URL, {
                _method   : isUpdate ? 'PUT' : 'POST',
                nama,
                email,
                hak_akses : hakAkses,
                status,
                ...(password ? { password } : {}),
            });

            if (res.success) {
                modalUser.hide();
                toast(isUpdate ? 'User berhasil diubah!' : 'User berhasil ditambahkan!');
                isUpdate
                    ? updateRow(activeUpdateId, nama, email, hakAkses, status)
                    : appendRow(res.user);
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
    document.getElementById('btnHapusUser').addEventListener('click', async () => {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapus.hide();
                toast(`User "${activeHapusNama}" berhasil dihapus!`);
                const hapusBtn = tbody.querySelector(`.btn-hapus-user[data-id="${activeHapusId}"]`);
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