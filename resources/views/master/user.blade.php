@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

@php
$hakAksesLabel = [
    'admin_bapperida' => 'Admin Bapperida',
    'admin_kecamatan' => 'Admin Kecamatan',
    'admin_opd'       => 'Admin OPD',
    'peserta'         => 'Peserta',
    'penilai'         => 'Penilai',
];
@endphp

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data User</h3>
            <p>Kelola semua user yang terdaftar</p>
        </div>
        <button class="btn btn-primary" id="btnTambahUser">
            Tambah User
        </button>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="total-badge">
            Total User: <span id="totalUser">{{ $users->count() }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchUser" class="form-control" placeholder="Cari nama atau email...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="text-align:center;">Hak Akses</th>
                    <th style="text-align:center;">Status</th>
                    <th width="320" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelUserBody">
                @forelse($users ?? [] as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->email }}</td>
                    <td style="text-align:center;">
                        <span class="badge-kategori">{{ $hakAksesLabel[$item->hak_akses] ?? $item->hak_akses }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($item->status === 'aktif')
                            <span class="badge-aktif px-3 py-2">Aktif</span>
                        @else
                            <span class="badge-nonaktif px-3 py-2">Nonaktif</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-aksi btn-edit-user"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama }}"
                                    data-email="{{ $item->email }}"
                                    data-hak-akses="{{ $item->hak_akses }}"
                                    data-status="{{ $item->status }}"
                                    data-url="{{ route('user.update', $item->id) }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-aksi btn-hapus-user"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama }}"
                                    data-url="{{ route('user.destroy', $item->id) }}">
                                Hapus
                            </button>
                            <a href="{{ route('user.login-as', $item->id) }}"
                               class="btn btn-success btn-aksi">
                                Login As
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="6" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data user
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Edit User ══ --}}
<div class="modal fade" id="modalUser" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
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
                        <input type="text" id="inputNama"
                               class="form-control" placeholder="Masukkan nama...">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Email</label>
                        <input type="email" id="inputEmail"
                               class="form-control" placeholder="Masukkan email...">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Hak Akses</label>
                        <select id="inputHakAkses" class="form-select">
                            <option value="" disabled selected>-- Pilih Hak Akses --</option>
                            <option value="admin_bapperida">Admin Bapperida</option>
                            <option value="admin_kecamatan">Admin Kecamatan</option>
                            <option value="admin_opd">Admin OPD</option>
                            <option value="peserta">Peserta</option>
                            <option value="penilai">Penilai</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <div class="d-flex gap-4 mt-1">
                            <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="statusUser" id="statusAktif" value="aktif" checked> Aktif
                            </label>
                            <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="statusUser" id="statusNonaktif" value="nonaktif"> Nonaktif
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label fw-semibold" id="labelPassword">Password</label>
                        <input type="password" id="inputPassword"
                               class="form-control" placeholder="Masukkan password...">
                        <div id="passwordHint" style="display:none; font-size:0.78rem; color:var(--ri-text-muted); margin-top:4px;">
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


{{-- ══ MODAL — Konfirmasi Hapus User ══ --}}
<div class="modal fade" id="modalHapusUser" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus User Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. User
                <strong id="namaUserHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-aksi px-3" id="btnBatalHapusUser">Batal</button>
                <button type="button" id="btnHapusUser" class="btn btn-danger btn-aksi px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Konstanta ──
    const STORE_URL = "{{ route('user.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Mapping hak akses → label ──
    const HAK_AKSES_LABEL = {
        'admin_bapperida': 'Admin Bapperida',
        'admin_kecamatan': 'Admin Kecamatan',
        'admin_opd':       'Admin OPD',
        'peserta':         'Peserta',
        'penilai':         'Penilai',
    };
    function labelHakAkses(val) {
        return HAK_AKSES_LABEL[val] ?? val;
    }

    // ── Elemen ──
    const tbody       = document.getElementById('tabelUserBody');
    const totalSpan   = document.getElementById('totalUser');
    const searchInput = document.getElementById('searchUser');

    // ── Modal: singleton + static backdrop ──
    const modalUserEl  = document.getElementById('modalUser');
    const modalHapusEl = document.getElementById('modalHapusUser');
    const modalUser    = new bootstrap.Modal(modalUserEl);
    const modalHapus   = new bootstrap.Modal(modalHapusEl);

    // ── State ──
    let activeMode      = 'store';
    let activeUpdateId  = null;
    let activeUpdateUrl = null;
    let activeHapusId   = null;
    let activeHapusUrl  = null;
    let activeHapusNama = null;
    let isSaving        = false;
    let isDeleting      = false;

    // ────────────────────────────────────────────
    // HELPER: AJAX pakai FormData agar _method terbaca Laravel
    // ────────────────────────────────────────────
    async function sendRequest(url, data) {
        const form = new FormData();
        Object.entries(data).forEach(([k, v]) => {
            if (v !== null && v !== undefined && v !== '') form.append(k, v);
        });
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: form,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

    // ────────────────────────────────────────────
    // HELPER: Badge status
    // ────────────────────────────────────────────
    function badgeStatus(status) {
        return status === 'aktif'
            ? `<span class="badge-aktif px-3 py-2">Aktif</span>`
            : `<span class="badge-nonaktif px-3 py-2">Nonaktif</span>`;
    }

    // ────────────────────────────────────────────
    // HELPER: Toast
    // ────────────────────────────────────────────
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = 'alert alert-dismissible fade show position-fixed bottom-0 end-0 m-4';
        el.style.cssText = [
            'z-index:9999',
            'min-width:280px',
            `background:${type === 'success' ? 'rgba(245,158,11,0.12)' : 'rgba(163,45,45,0.12)'}`,
            `border:1px solid ${type === 'success' ? 'rgba(245,158,11,0.4)' : 'rgba(163,45,45,0.3)'}`,
            `color:${type === 'success' ? '#92400e' : '#A32D2D'}`,
        ].join(';');
        el.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'} me-2"></i>
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    // ────────────────────────────────────────────
    // HELPER: Update baris yang sudah ada
    // ────────────────────────────────────────────
    function updateRow(id, nama, email, hakAkses, status) {
        const editBtn = tbody.querySelector(`.btn-edit-user[data-id="${id}"]`);
        if (!editBtn) return;
        const tr = editBtn.closest('tr');
        tr.cells[1].textContent = nama;
        tr.cells[2].textContent = email;
        tr.cells[3].innerHTML   = `<span class="badge-kategori">${labelHakAkses(hakAkses)}</span>`;
        tr.cells[4].innerHTML   = badgeStatus(status);
        editBtn.dataset.nama     = nama;
        editBtn.dataset.email    = email;
        editBtn.dataset.hakAkses = hakAkses;
        editBtn.dataset.status   = status;
        const hapusBtn = tr.querySelector('.btn-hapus-user');
        if (hapusBtn) hapusBtn.dataset.nama = nama;
    }

    // ────────────────────────────────────────────
    // HELPER: Tambah baris baru
    // ────────────────────────────────────────────
    function appendRow(user) {
        const emptyRow = tbody.querySelector('#emptyRow');
        if (emptyRow) emptyRow.remove();

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>${user.nama}</td>
            <td>${user.email}</td>
            <td style="text-align:center;"><span class="badge-kategori">${labelHakAkses(user.hak_akses)}</span></td>
            <td style="text-align:center;">${badgeStatus(user.status)}</td>
            <td style="text-align:center;">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-aksi btn-edit-user"
                            data-id="${user.id}"
                            data-nama="${user.nama}"
                            data-email="${user.email}"
                            data-hak-akses="${user.hak_akses}"
                            data-status="${user.status}"
                            data-url="${user.update_url}">
                        Ubah
                    </button>
                    <button class="btn btn-danger btn-aksi btn-hapus-user"
                            data-id="${user.id}"
                            data-nama="${user.nama}"
                            data-url="${user.destroy_url}">
                        Hapus
                    </button>
                    <a href="${user.login_url}" class="btn btn-success btn-aksi">Login As</a>
                </div>
            </td>`;
        tbody.appendChild(tr);
        totalSpan.textContent = tbody.querySelectorAll('tr').length;
    }

    // ────────────────────────────────────────────
    // HELPER: Renumber baris
    // ────────────────────────────────────────────
    function renumberRows() {
        let n = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            if (!tr.querySelector('.empty-row')) tr.cells[0].textContent = ++n;
        });
        totalSpan.textContent = n;
    }

    // ────────────────────────────────────────────
    // HELPER: Loading state tombol Simpan
    // ────────────────────────────────────────────
    function setSimpanLoading(loading) {
        document.getElementById('btnSimpanUser').disabled      = loading;
        document.getElementById('btnSimpanUser').textContent   = loading ? 'Menyimpan...' : 'Simpan';
        document.getElementById('btnBatalUser').disabled       = loading;
        document.getElementById('btnTutupModalUser').disabled  = loading;
    }

    // ────────────────────────────────────────────
    // HELPER: Loading state tombol Hapus
    // ────────────────────────────────────────────
    function setHapusLoading(loading) {
        document.getElementById('btnHapusUser').disabled       = loading;
        document.getElementById('btnHapusUser').textContent    = loading ? 'Menghapus...' : 'Hapus';
        document.getElementById('btnBatalHapusUser').disabled  = loading;
    }

    // ────────────────────────────────────────────
    // MODAL: buka untuk Tambah
    // ────────────────────────────────────────────
    document.getElementById('btnTambahUser').addEventListener('click', function () {
        activeMode      = 'store';
        activeUpdateId  = null;
        activeUpdateUrl = null;
        document.getElementById('modalUserTitle').textContent  = 'Tambah User';
        document.getElementById('inputNama').value             = '';
        document.getElementById('inputEmail').value            = '';
        document.getElementById('inputHakAkses').value         = '';
        document.getElementById('inputPassword').value         = '';
        document.getElementById('statusAktif').checked         = true;
        document.getElementById('passwordHint').style.display  = 'none';
        document.getElementById('labelPassword').textContent   = 'Password';
        setSimpanLoading(false);
        modalUser.show();
    });

    // ────────────────────────────────────────────
    // MODAL: buka Ubah / Hapus via event delegation
    // ────────────────────────────────────────────
    tbody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-user');
        if (editBtn) {
            activeMode      = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;
            document.getElementById('modalUserTitle').textContent  = 'Ubah User';
            document.getElementById('inputNama').value             = editBtn.dataset.nama;
            document.getElementById('inputEmail').value            = editBtn.dataset.email;
            document.getElementById('inputHakAkses').value         = editBtn.dataset.hakAkses;
            document.getElementById('inputPassword').value         = '';
            document.getElementById('passwordHint').style.display  = 'block';
            document.getElementById('labelPassword').textContent   = 'Password (opsional)';
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

    // ────────────────────────────────────────────
    // MODAL: tutup manual (guard saat loading)
    // ────────────────────────────────────────────
    document.getElementById('btnTutupModalUser').addEventListener('click', function () {
        if (isSaving) return;
        modalUser.hide();
    });
    document.getElementById('btnBatalUser').addEventListener('click', function () {
        if (isSaving) return;
        modalUser.hide();
    });
    document.getElementById('btnBatalHapusUser').addEventListener('click', function () {
        if (isDeleting) return;
        modalHapus.hide();
    });

    // ────────────────────────────────────────────
    // SUBMIT: Tambah / Ubah
    // ────────────────────────────────────────────
    document.getElementById('btnSimpanUser').addEventListener('click', async function () {
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
            const url  = isUpdate ? activeUpdateUrl : STORE_URL;
            const data = {
                _method:    isUpdate ? 'PUT' : 'POST',
                nama, email,
                hak_akses:  hakAkses,
                status,
                ...(password ? { password } : {}),
            };
            const res = await sendRequest(url, data);

            if (res.success) {
                modalUser.hide();
                toast(isUpdate ? 'User berhasil diubah!' : 'User berhasil ditambahkan!');
                if (isUpdate) {
                    updateRow(activeUpdateId, nama, email, hakAkses, status);
                } else {
                    appendRow(res.user);
                }
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
            }
        } catch (e) {
            console.error(e);
            toast(e.message ?? 'Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isSaving = false;
            setSimpanLoading(false);
        }
    });

    // ────────────────────────────────────────────
    // SUBMIT: Hapus
    // ────────────────────────────────────────────
    document.getElementById('btnHapusUser').addEventListener('click', async function () {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapus.hide();
                toast(`User "${activeHapusNama}" berhasil dihapus!`);
                const hapusBtn = tbody.querySelector(`.btn-hapus-user[data-id="${activeHapusId}"]`);
                if (hapusBtn) hapusBtn.closest('tr').remove();
                renumberRows();
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch (e) {
            console.error(e);
            toast(e.message ?? 'Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isDeleting = false;
            setHapusLoading(false);
        }
    });

    // ────────────────────────────────────────────
    // SEARCH
    // ────────────────────────────────────────────
    searchInput.addEventListener('input', function () {
        const kw = this.value.toLowerCase().trim();
        let n = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            if (tr.querySelector('.empty-row')) return;
            const show = tr.textContent.toLowerCase().includes(kw);
            tr.style.display = show ? '' : 'none';
            if (show) n++;
        });
        totalSpan.textContent = n;
    });

});
</script>
@endpush