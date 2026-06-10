@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Master Penilai</h3>
            <p>Kelola data penilai</p>
        </div>
        <button class="btn btn-primary" id="btnTambahPenilai">Tambah Penilai</button>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="total-badge">Total Penilai: <span id="totalPenilai">{{ $penilai->count() }}</span></div>
        <div class="search-box">
            <input type="text" id="searchPenilai" class="form-control" placeholder="Cari nama atau email...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama Penilai</th>
                    <th>Email</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPenilaiBody">
                @forelse($penilai as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->email }}</td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-aksi btn-edit-penilai"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-email="{{ $p->email }}"
                                    data-url="{{ route('penilai.update', $p->id) }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-aksi btn-hapus-penilai"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-url="{{ route('penilai.destroy', $p->id) }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="4" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data penilai
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Ubah Penilai ══ --}}
<div class="modal fade" id="modalPenilai" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
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
                <div class="mb-4">
                    <label class="form-label fw-semibold required">Nama</label>
                    <input type="text" id="penilaiNama"
                           class="form-control" placeholder="Nama penilai...">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold required">Email</label>
                    <input type="email" id="penilaiEmail"
                           class="form-control" placeholder="Email penilai...">
                </div>
            </div>

            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalPenilai">Batal</button>
                <button type="button" id="btnSimpanPenilai" class="btn btn-success px-4">Simpan</button>
            </div>

        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus ══ --}}
<div class="modal fade" id="modalHapusPenilai" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Penilai
                <strong id="namaPenilaiHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-aksi px-3" id="btnBatalHapusPenilai">Batal</button>
                <button type="button" id="btnHapusPenilai" class="btn btn-danger btn-aksi px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const STORE_URL = "{{ route('penilai.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody     = document.getElementById('tabelPenilaiBody');
    const totalSpan = document.getElementById('totalPenilai');

    // ── Modal ──
    const modalPenilai      = new bootstrap.Modal(document.getElementById('modalPenilai'));
    const modalHapusPenilai = new bootstrap.Modal(document.getElementById('modalHapusPenilai'));

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
    // HELPER: AJAX
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
    // HELPER: Loading state
    // ────────────────────────────────────────────
    function setSimpanLoading(loading) {
        document.getElementById('btnSimpanPenilai').disabled     = loading;
        document.getElementById('btnSimpanPenilai').textContent  = loading ? 'Menyimpan...' : 'Simpan';
        document.getElementById('btnBatalPenilai').disabled      = loading;
        document.getElementById('btnTutupModalPenilai').disabled = loading;
    }

    function setHapusLoading(loading) {
        document.getElementById('btnHapusPenilai').disabled      = loading;
        document.getElementById('btnHapusPenilai').textContent   = loading ? 'Menghapus...' : 'Hapus';
        document.getElementById('btnBatalHapusPenilai').disabled = loading;
    }

    // ────────────────────────────────────────────
    // HELPER: Update baris
    // ────────────────────────────────────────────
    function updateRow(id, nama, email) {
        const editBtn = tbody.querySelector(`.btn-edit-penilai[data-id="${id}"]`);
        if (!editBtn) return;
        const tr = editBtn.closest('tr');
        tr.cells[1].textContent = nama;
        tr.cells[2].textContent = email;
        editBtn.dataset.nama  = nama;
        editBtn.dataset.email = email;
        const hapusBtn = tr.querySelector('.btn-hapus-penilai');
        if (hapusBtn) hapusBtn.dataset.nama = nama;
    }

    // ────────────────────────────────────────────
    // HELPER: Tambah baris baru
    // ────────────────────────────────────────────
    function appendRow(p) {
        const emptyRow = tbody.querySelector('#emptyRow');
        if (emptyRow) emptyRow.remove();

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>${p.nama}</td>
            <td>${p.email}</td>
            <td style="text-align:center;">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-aksi btn-edit-penilai"
                            data-id="${p.id}"
                            data-nama="${p.nama}"
                            data-email="${p.email}"
                            data-url="${p.update_url}">
                        Ubah
                    </button>
                    <button class="btn btn-danger btn-aksi btn-hapus-penilai"
                            data-id="${p.id}"
                            data-nama="${p.nama}"
                            data-url="${p.destroy_url}">
                        Hapus
                    </button>
                </div>
            </td>`;
        tbody.appendChild(tr);
        totalSpan.textContent = tbody.querySelectorAll('tr').length;
    }

    // ────────────────────────────────────────────
    // HELPER: Renumber
    // ────────────────────────────────────────────
    function renumberRows() {
        let n = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            if (!tr.querySelector('.empty-row')) tr.cells[0].textContent = ++n;
        });
        totalSpan.textContent = n;
    }

    // ────────────────────────────────────────────
    // MODAL: buka Tambah
    // ────────────────────────────────────────────
    document.getElementById('btnTambahPenilai').addEventListener('click', function () {
        activeMode      = 'store';
        activeUpdateId  = null;
        activeUpdateUrl = null;
        document.getElementById('modalPenilaiTitle').textContent = 'Tambah Penilai';
        document.getElementById('penilaiNama').value             = '';
        document.getElementById('penilaiEmail').value            = '';
        setSimpanLoading(false);
        modalPenilai.show();
    });

    // ────────────────────────────────────────────
    // MODAL: buka Ubah / Hapus via event delegation
    // ────────────────────────────────────────────
    tbody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-penilai');
        if (editBtn) {
            activeMode      = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;
            document.getElementById('modalPenilaiTitle').textContent = 'Ubah Penilai';
            document.getElementById('penilaiNama').value             = editBtn.dataset.nama;
            document.getElementById('penilaiEmail').value            = editBtn.dataset.email;
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
            modalHapusPenilai.show();
        }
    });

    // ────────────────────────────────────────────
    // MODAL: tutup manual (guard saat loading)
    // ────────────────────────────────────────────
    document.getElementById('btnTutupModalPenilai').addEventListener('click', function () {
        if (isSaving) return;
        modalPenilai.hide();
    });
    document.getElementById('btnBatalPenilai').addEventListener('click', function () {
        if (isSaving) return;
        modalPenilai.hide();
    });
    document.getElementById('btnBatalHapusPenilai').addEventListener('click', function () {
        if (isDeleting) return;
        modalHapusPenilai.hide();
    });

    // ────────────────────────────────────────────
    // SUBMIT: Tambah / Ubah
    // ────────────────────────────────────────────
    document.getElementById('btnSimpanPenilai').addEventListener('click', async function () {
        if (isSaving) return;

        const nama  = document.getElementById('penilaiNama').value.trim();
        const email = document.getElementById('penilaiEmail').value.trim();

        if (!nama || !email) {
            toast('Nama dan email wajib diisi.', 'error');
            return;
        }

        const isUpdate = activeMode === 'update';
        isSaving = true;
        setSimpanLoading(true);

        try {
            const url = isUpdate ? activeUpdateUrl : STORE_URL;
            const res = await sendRequest(url, {
                _method: isUpdate ? 'PUT' : 'POST',
                nama, email,
            });

            if (res.success) {
                modalPenilai.hide();
                toast(isUpdate ? 'Penilai berhasil diubah!' : 'Penilai berhasil ditambahkan!');
                if (isUpdate) {
                    updateRow(activeUpdateId, nama, email);
                } else {
                    appendRow(res.penilai);
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
    document.getElementById('btnHapusPenilai').addEventListener('click', async function () {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapusPenilai.hide();
                toast(`Penilai "${activeHapusNama}" berhasil dihapus!`);
                const hapusBtn = tbody.querySelector(`.btn-hapus-penilai[data-id="${activeHapusId}"]`);
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
    document.getElementById('searchPenilai').addEventListener('input', function () {
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