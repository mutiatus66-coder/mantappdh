@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
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

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="total-badge">Total Penilai: <span id="totalPenilai">{{ $penilai->count() }}</span></div>
        <div class="search-box">
            <input type="text" id="searchPenilai" class="form-control" placeholder="Cari nama atau email...">
        </div>
    </div>

    <div style="overflow-x:auto;">
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
                                    data-user-id="{{ $p->user_id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-email="{{ $p->email }}"
                                    data-url="{{ route('penilai.update', $p->id) }}">Ubah</button>
                            <button class="btn btn-danger btn-aksi btn-hapus-penilai"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-url="{{ route('penilai.destroy', $p->id) }}">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="4" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada penilai untuk sub event ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- MODAL Tambah / Ubah (Sekarang menggunakan Dropdown User) --}}
<div class="modal fade" id="modalPenilai" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalPenilaiTitle">Tambah Penilai</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalPenilai"><i class="bi bi-x-lg fs-5"></i></button>
            </div>
            <div class="modal-body px-5 py-4">
                {{-- PILIH USER --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold required">Pilih User Penilai</label>
                    <select id="penilaiUserId" class="form-select">
                        <option value="" disabled selected>Pilih User Penilai...</option>
                        @foreach($usersPenilai as $u)
                        <option value="{{ $u->id }}" data-nama="{{ $u->nama }}" data-email="{{ $u->email }}">
                            {{ $u->nama }} ({{ $u->email }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PREVIEW NAMA & EMAIL (otomatis terisi saat user dipilih) --}}
                <div id="previewPenilai" class="p-2 bg-light rounded d-none">
                    <small class="text-muted">
                        Nama: <span id="previewNama" class="fw-semibold"></span> &nbsp;|&nbsp; Email: <span id="previewEmail" class="fw-semibold"></span>
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

{{-- MODAL Hapus --}}
<div class="modal fade" id="modalHapusPenilai" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">
            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>
            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4" style="font-size:.875rem; color:#6b7280;">
                Penilai <strong id="namaPenilaiHapus"></strong> akan dihapus secara permanen.
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

    const SUB_EVENT_ID = {{ $subEvent->id }};
    const STORE_URL    = "{{ route('penilai.store') }}";
    const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody        = document.getElementById('tabelPenilaiBody');
    const totalSpan    = document.getElementById('totalPenilai');

    const modalPenilai      = new bootstrap.Modal(document.getElementById('modalPenilai'));
    const modalHapusPenilai = new bootstrap.Modal(document.getElementById('modalHapusPenilai'));

    let activeMode      = 'store';
    let activeUpdateId  = null;
    let activeUpdateUrl = null;
    let activeHapusId   = null;
    let activeHapusUrl  = null;
    let activeHapusNama = null;
    let isSaving        = false;
    let isDeleting      = false;

    // ── Preview Nama & Email saat dropdown berubah ──
    document.getElementById('penilaiUserId').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const previewEl = document.getElementById('previewPenilai');
        if (selected.value) {
            previewEl.classList.remove('d-none');
            document.getElementById('previewNama').textContent  = selected.dataset.nama;
            document.getElementById('previewEmail').textContent = selected.dataset.email;
        } else {
            previewEl.classList.add('d-none');
        }
    });

    async function sendRequest(url, data) {
        const form = new FormData();
        Object.entries(data).forEach(([k, v]) => {
            if (v !== null && v !== undefined && v !== '') form.append(k, v);
        });
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: form,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

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

    function setSimpanLoading(loading) {
        document.getElementById('btnSimpanPenilai').disabled    = loading;
        document.getElementById('btnSimpanPenilai').textContent = loading ? 'Menyimpan...' : 'Simpan';
        document.getElementById('btnBatalPenilai').disabled     = loading;
        document.getElementById('btnTutupModalPenilai').disabled = loading;
    }

    function setHapusLoading(loading) {
        document.getElementById('btnHapusPenilai').disabled     = loading;
        document.getElementById('btnHapusPenilai').textContent  = loading ? 'Menghapus...' : 'Hapus';
        document.getElementById('btnBatalHapusPenilai').disabled = loading;
    }

    // Update baris dengan data baru
    function updateRow(id, nama, email, userId) {
        const editBtn = tbody.querySelector(`.btn-edit-penilai[data-id="${id}"]`);
        if (!editBtn) return;
        const tr = editBtn.closest('tr');
        tr.cells[1].textContent = nama;
        tr.cells[2].textContent = email;
        editBtn.dataset.nama    = nama;
        editBtn.dataset.email   = email;
        editBtn.dataset.userId  = userId;
        const hapusBtn = tr.querySelector('.btn-hapus-penilai');
        if (hapusBtn) hapusBtn.dataset.nama = nama;
    }

    // Tambah baris baru
    function appendRow(p) {
        document.getElementById('emptyRow')?.remove();
        const n  = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${n}</td>
            <td>${p.nama}</td>
            <td>${p.email}</td>
            <td style="text-align:center;">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-aksi btn-edit-penilai"
                            data-id="${p.id}" data-user-id="${p.user_id}"
                            data-nama="${p.nama}" data-email="${p.email}"
                            data-url="${p.update_url}">Ubah</button>
                    <button class="btn btn-danger btn-aksi btn-hapus-penilai"
                            data-id="${p.id}" data-nama="${p.nama}"
                            data-url="${p.destroy_url}">Hapus</button>
                </div>
            </td>`;
        tbody.appendChild(tr);
        totalSpan.textContent = tbody.querySelectorAll('tr').length;
    }

    function renumberRows() {
        let n = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            if (!tr.querySelector('.empty-row')) tr.cells[0].textContent = ++n;
        });
        totalSpan.textContent = n;
    }

    // ── BUKA MODAL TAMBAH ──
    document.getElementById('btnTambahPenilai').addEventListener('click', function () {
        activeMode = 'store'; activeUpdateId = null; activeUpdateUrl = null;
        document.getElementById('modalPenilaiTitle').textContent = 'Tambah Penilai';
        document.getElementById('penilaiUserId').value = '';
        document.getElementById('previewPenilai').classList.add('d-none');
        setSimpanLoading(false);
        modalPenilai.show();
    });

    // ── BUKA MODAL EDIT / HAPUS ──
    tbody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-penilai');
        if (editBtn) {
            activeMode = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;
            document.getElementById('modalPenilaiTitle').textContent = 'Ganti Penilai';

            // Set dropdown sesuai user yang sedang dipilih, lalu trigger perubahan
            document.getElementById('penilaiUserId').value = editBtn.dataset.userId;
            document.getElementById('penilaiUserId').dispatchEvent(new Event('change'));

            setSimpanLoading(false);
            modalPenilai.show();
            return;
        }
        const hapusBtn = e.target.closest('.btn-hapus-penilai');
        if (hapusBtn) {
            activeHapusId = hapusBtn.dataset.id; activeHapusUrl = hapusBtn.dataset.url; activeHapusNama = hapusBtn.dataset.nama;
            document.getElementById('namaPenilaiHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapusPenilai.show();
        }
    });

    // ── TUTUP MODAL (Guard saat loading) ──
    document.getElementById('btnTutupModalPenilai').addEventListener('click', () => { if (!isSaving) modalPenilai.hide(); });
    document.getElementById('btnBatalPenilai').addEventListener('click',       () => { if (!isSaving) modalPenilai.hide(); });
    document.getElementById('btnBatalHapusPenilai').addEventListener('click',  () => { if (!isDeleting) modalHapusPenilai.hide(); });

    // ── SIMPAN (STORE / UPDATE) ──
    document.getElementById('btnSimpanPenilai').addEventListener('click', async function () {
        if (isSaving) return;
        const userId = document.getElementById('penilaiUserId').value;
        if (!userId) { toast('Harap pilih user penilai.', 'error'); return; }

        isSaving = true; setSimpanLoading(true);
        try {
            const isUpdate = activeMode === 'update';
            const url      = isUpdate ? activeUpdateUrl : STORE_URL;
            const res = await sendRequest(url, {
                _method: isUpdate ? 'PUT' : 'POST',
                sub_event_id: SUB_EVENT_ID,
                user_id: userId,
            });
            if (res.success) {
                modalPenilai.hide();
                toast(isUpdate ? 'Penilai berhasil diganti!' : 'Penilai berhasil ditambahkan!');
                if (isUpdate) {
                    updateRow(activeUpdateId, res.penilai.nama, res.penilai.email, res.penilai.user_id);
                } else {
                    appendRow(res.penilai);
                }
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
            }
        } catch (e) {
            toast(e.message ?? 'Terjadi kesalahan.', 'error');
        } finally {
            isSaving = false; setSimpanLoading(false);
        }
    });

    // ── HAPUS ──
    document.getElementById('btnHapusPenilai').addEventListener('click', async function () {
        if (isDeleting) return;
        isDeleting = true; setHapusLoading(true);
        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapusPenilai.hide();
                toast(`Penilai "${activeHapusNama}" berhasil dihapus!`);
                tbody.querySelector(`.btn-hapus-penilai[data-id="${activeHapusId}"]`)?.closest('tr').remove();
                renumberRows();
                if (!tbody.querySelector('tr')) {
                    tbody.innerHTML = `<tr id="emptyRow"><td colspan="4" class="empty-row"><i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada penilai untuk sub event ini</td></tr>`;
                }
            } else {
                toast(res.message ?? 'Gagal menghapus.', 'error');
            }
        } catch (e) {
            toast(e.message ?? 'Terjadi kesalahan.', 'error');
        } finally {
            isDeleting = false; setHapusLoading(false);
        }
    });

    // ── SEARCH ──
    document.getElementById('searchPenilai').addEventListener('input', function () {
        const kw = this.value.toLowerCase();
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
