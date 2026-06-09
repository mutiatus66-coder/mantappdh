@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="penilai-container">
    <div class="penilai-header">
        <div class="penilai-title">
            <h3>Master Penilai</h3>
            <p>Kelola data penilai</p>
        </div>
        <button class="btn btn-primary" id="btnTambahPenilai">Tambah Penilai</button>
    </div>

    <div class="penilai-stats">
        <div class="total-badge">Total Penilai: <span id="totalPenilai">{{ $penilai->count() }}</span></div>
        <div class="search-box">
            <input type="text" id="searchPenilai" placeholder="Cari nama atau email...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="penilai-table">
            <thead>
                <tr>
                    <th>No</th>
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
                            <button class="btn btn-warning btn-edit-penilai"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-email="{{ $p->email }}"
                                    data-url="{{ route('penilai.update', $p->id) }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-hapus-penilai"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-url="{{ route('penilai.destroy', $p->id) }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-row">Belum ada data penilai</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ══ MODAL — Tambah / Ubah Penilai ══ --}}
<div class="modal fade" id="modalPenilai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalPenilaiTitle">Tambah Penilai</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        data-bs-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSimpanPenilai" class="btn btn-success px-4">Simpan</button>
            </div>

        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus ══ --}}
<div class="modal fade" id="modalHapusPenilai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4" style="font-size:.875rem; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Penilai
                <strong id="namaPenilaiHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnHapusPenilai" class="btn btn-danger">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl  = "{{ route('penilai.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody     = document.getElementById('tabelPenilaiBody');
    const totalSpan = document.getElementById('totalPenilai');

    async function sendRequest(url, method, data) {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify(data),
        });
        return res.json();
    }
    const modalPenilai      = new bootstrap.Modal(document.getElementById('modalPenilai'));
    const modalHapusPenilai = new bootstrap.Modal(document.getElementById('modalHapusPenilai'));
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = 'alert alert-dismissible fade show position-fixed bottom-0 end-0 m-4';
        el.style.cssText = `z-index:9999; min-width:280px;
            background:${type === 'success' ? 'rgba(245,158,11,0.12)' : 'rgba(163,45,45,0.12)'};
            border:1px solid ${type === 'success' ? 'rgba(245,158,11,0.4)' : 'rgba(163,45,45,0.3)'};
            color:${type === 'success' ? '#92400e' : '#A32D2D'};`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'} me-2"></i>${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    function updateRow(id, nama, email) {
        document.querySelectorAll('.btn-edit-penilai').forEach(btn => {
            if (btn.dataset.id == id) {
                const tr = btn.closest('tr');
                tr.cells[1].textContent = nama;
                tr.cells[2].textContent = email;
                btn.dataset.nama  = nama;
                btn.dataset.email = email;
            }
        });
    }

    function appendRow(p) {
        const emptyRow = tbody.querySelector('.empty-row');
        if (emptyRow) emptyRow.closest('tr').remove();

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>${p.nama}</td>
            <td>${p.email}</td>
            <td style="text-align:center;">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-edit-penilai"
                            data-id="${p.id}"
                            data-nama="${p.nama}"
                            data-email="${p.email}"
                            data-url="${p.update_url}">
                        Ubah
                    </button>
                    <button class="btn btn-danger btn-hapus-penilai"
                            data-id="${p.id}"
                            data-nama="${p.nama}"
                            data-url="${p.destroy_url}">
                        Hapus
                    </button>
                </div>
            </td>`;
        tbody.appendChild(tr);

        tr.querySelector('.btn-edit-penilai').addEventListener('click', handleEdit);
        tr.querySelector('.btn-hapus-penilai').addEventListener('click', handleHapus);

        totalSpan.textContent = tbody.querySelectorAll('tr').length;
    }

    function resetModal() {
        document.getElementById('modalPenilaiTitle').textContent = 'Tambah Penilai';
        document.getElementById('penilaiNama').value  = '';
        document.getElementById('penilaiEmail').value = '';
        const btn = document.getElementById('btnSimpanPenilai');
        btn.disabled    = false;
        btn.textContent = 'Simpan';
        delete btn.dataset.updateId;
        delete btn.dataset.updateUrl;
        btn.dataset.mode = 'store';
    }

    document.getElementById('modalPenilai').addEventListener('hidden.bs.modal', resetModal);

    document.getElementById('btnTambahPenilai').addEventListener('click', function () {
        resetModal();
        new bootstrap.Modal(document.getElementById('modalPenilai')).show();
    });

    function handleEdit() {
        resetModal();
        document.getElementById('modalPenilaiTitle').textContent = 'Ubah Penilai';
        document.getElementById('penilaiNama').value             = this.dataset.nama;
        document.getElementById('penilaiEmail').value            = this.dataset.email;
        const btn = document.getElementById('btnSimpanPenilai');
        btn.dataset.mode      = 'update';
        btn.dataset.updateId  = this.dataset.id;
        btn.dataset.updateUrl = this.dataset.url;
        new bootstrap.Modal(document.getElementById('modalPenilai')).show();
    }

    document.querySelectorAll('.btn-edit-penilai').forEach(btn => {
        btn.addEventListener('click', handleEdit);
    });

    document.getElementById('btnSimpanPenilai').addEventListener('click', async function () {
        const nama  = document.getElementById('penilaiNama').value.trim();
        const email = document.getElementById('penilaiEmail').value.trim();

        if (!nama || !email) {
            toast('Nama dan email wajib diisi.', 'error');
            return;
        }

        this.disabled    = true;
        this.textContent = 'Menyimpan...';

        try {
            const isUpdate = this.dataset.mode === 'update';
            const url      = isUpdate ? this.dataset.updateUrl : storeUrl;
            const res      = await sendRequest(url, 'POST', {
                _method: isUpdate ? 'PUT' : 'POST',
                nama, email,
            });

            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalPenilai')).hide();
                toast(isUpdate ? 'Penilai berhasil diubah!' : 'Penilai berhasil ditambahkan!');
                if (isUpdate) {
                    updateRow(this.dataset.updateId, nama, email);
                } else {
                    appendRow(res.penilai);
                }
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
                this.disabled    = false;
                this.textContent = 'Simpan';
            }
        } catch {
            toast('Terjadi kesalahan.', 'error');
            this.disabled    = false;
            this.textContent = 'Simpan';
        }
    });

    function handleHapus() {
        document.getElementById('namaPenilaiHapus').textContent    = this.dataset.nama;
        document.getElementById('btnHapusPenilai').dataset.id      = this.dataset.id;
        document.getElementById('btnHapusPenilai').dataset.url     = this.dataset.url;
        document.getElementById('btnHapusPenilai').dataset.nama    = this.dataset.nama;
        new bootstrap.Modal(document.getElementById('modalHapusPenilai')).show();
    }

    document.querySelectorAll('.btn-hapus-penilai').forEach(btn => {
        btn.addEventListener('click', handleHapus);
    });

    document.getElementById('btnHapusPenilai').addEventListener('click', async function () {
        const url  = this.dataset.url;
        const id   = this.dataset.id;
        const nama = this.dataset.nama;

        this.disabled    = true;
        this.textContent = 'Menghapus...';

        try {
            const res = await sendRequest(url, 'POST', { _method: 'DELETE' });
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalHapusPenilai')).hide();
                toast(`Penilai "${nama}" berhasil dihapus!`);
                document.querySelectorAll('.btn-hapus-penilai').forEach(btn => {
                    if (btn.dataset.id == id) btn.closest('tr').remove();
                });
                tbody.querySelectorAll('tr').forEach((tr, i) => {
                    if (!tr.querySelector('.empty-row')) tr.cells[0].textContent = i + 1;
                });
                totalSpan.textContent = tbody.querySelectorAll('tr:not(:has(.empty-row))').length;
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch {
            toast('Terjadi kesalahan.', 'error');
        }

        this.disabled    = false;
        this.textContent = 'Hapus';
    });

    document.getElementById('searchPenilai').addEventListener('keyup', function () {
        const kw = this.value.toLowerCase().trim();
        let n = 0;
        tbody.querySelectorAll('tr').forEach(r => {
            if (r.querySelector('.empty-row')) return;
            const show = r.textContent.toLowerCase().includes(kw);
            r.style.display = show ? '' : 'none';
            if (show) n++;
        });
        totalSpan.textContent = n;
    });

});
</script>
@endpush