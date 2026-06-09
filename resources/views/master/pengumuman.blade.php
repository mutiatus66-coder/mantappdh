@extends('index')

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Master Pengumuman</h3>
            <p>Kelola pengumuman yang ditampilkan ke publik</p>
        </div>
        <button class="btn btn-primary" id="btnTambahPengumuman">
            Tambah Pengumuman
        </button>
    </div>

    <div class="sub-event-stats">
        <div class="total-badge">
            Total Pengumuman: <span id="totalPengumuman">{{ $pengumuman->count() }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchPengumuman" placeholder="Cari judul atau deskripsi...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>File</th>
                    <th width="220" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPengumumanBody">
                @forelse($pengumuman as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->judul }}</td>
                    <td>{{ Str::limit($p->deskripsi, 80) }}</td>
                    <td><span class="badge-kategori {{ $p->status == 'Published' ? 'status-published' : 'status-draft' }}">{{ $p->status }}</span></td>
                    <td>
                        @if($p->file_path)
                            <a href="{{ asset('storage/'.$p->file_path) }}" target="_blank">Lihat File</a>
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-edit-pengumuman btn-aksi"
                                data-id="{{ $p->id }}"
                                data-judul="{{ $p->judul }}"
                                data-deskripsi="{{ $p->deskripsi }}"
                                data-status="{{ $p->status }}"
                                data-url="{{ route('pengumuman.update', $p->id) }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-hapus-pengumuman btn-aksi"
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
                    <td colspan="6" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data pengumuman
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Ubah Pengumuman ══ --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalPengumumanTitle">Tambah Pengumuman</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        data-bs-dismiss="modal" aria-label="Close">
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
                        <textarea id="pDeskripsi" class="form-control"
                            rows="4" placeholder="Masukkan deskripsi pengumuman"></textarea>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold required">Status</label>
                        <select id="pStatus" class="form-select">
                            <option value="">-- Pilih Status --</option>
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
                            style="max-height:180px; border-radius:8px; border:1px solid #e5e7eb; object-fit:cover;">
                    </div>
                </div>
            </div>

            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSimpanPengumuman" class="btn btn-success px-4">Simpan</button>
            </div>

        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Pengumuman ══ --}}
<div class="modal fade" id="modalHapusPengumuman" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Pengumuman
                <strong id="namaPengumumanHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-aksi px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnHapusPengumuman" class="btn btn-danger btn-aksi px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl  = "{{ route('pengumuman.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody     = document.getElementById('tabelPengumumanBody');
    const totalSpan = document.getElementById('totalPengumuman');

    // ── Singleton modal ──
    const modalPengumuman      = new bootstrap.Modal(document.getElementById('modalPengumuman'));
    const modalHapusPengumuman = new bootstrap.Modal(document.getElementById('modalHapusPengumuman'));

    async function sendFormData(url, formData) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: formData,
        });
        return res.json();
    }

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

    function updateRow(id, judul, deskripsi, status, fileUrl) {
        document.querySelectorAll('.btn-edit-pengumuman').forEach(btn => {
            if (btn.dataset.id == id) {
                const tr = btn.closest('tr');
                tr.cells[1].textContent = judul;
                tr.cells[2].textContent = deskripsi.length > 80 ? deskripsi.substring(0, 80) + '...' : deskripsi;
                tr.cells[3].innerHTML   = `<span class="badge-kategori ${status === 'Published' ? 'status-published' : 'status-draft'}">${status}</span>`;
                tr.cells[4].innerHTML   = fileUrl ? `<a href="${fileUrl}" target="_blank">Lihat File</a>` : '-';
                btn.dataset.judul     = judul;
                btn.dataset.deskripsi = deskripsi;
                btn.dataset.status    = status;
            }
        });
    }

    function appendRow(p) {
        const emptyRow = tbody.querySelector('.empty-row');
        if (emptyRow) emptyRow.closest('tr').remove();

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const deskripsiShort = p.deskripsi
            ? (p.deskripsi.length > 80 ? p.deskripsi.substring(0, 80) + '...' : p.deskripsi) : '';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>${p.judul}</td>
            <td>${deskripsiShort}</td>
            <td><span class="badge-kategori ${p.status === 'Published' ? 'status-published' : 'status-draft'}">${p.status}</span></td>
            <td>${p.file_url ? `<a href="${p.file_url}" target="_blank">Lihat File</a>` : '-'}</td>
            <td style="text-align:center;">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-edit-pengumuman btn-aksi"
                        data-id="${p.id}" data-judul="${p.judul}"
                        data-deskripsi="${p.deskripsi ?? ''}" data-status="${p.status}"
                        data-url="${p.update_url}">Ubah</button>
                    <button class="btn btn-danger btn-hapus-pengumuman btn-aksi"
                        data-id="${p.id}" data-nama="${p.judul}"
                        data-url="${p.destroy_url}">Hapus</button>
                </div>
            </td>`;
        tbody.appendChild(tr);

        tr.querySelector('.btn-edit-pengumuman').addEventListener('click', handleEdit);
        tr.querySelector('.btn-hapus-pengumuman').addEventListener('click', handleHapus);

        totalSpan.textContent = tbody.querySelectorAll('tr').length;
    }

    function resetModal() {
        document.getElementById('modalPengumumanTitle').textContent = 'Tambah Pengumuman';
        document.getElementById('pJudul').value     = '';
        document.getElementById('pDeskripsi').value = '';
        document.getElementById('pStatus').value    = '';
        document.getElementById('pFile').value      = '';
        document.getElementById('previewWrap').style.display = 'none';
        document.getElementById('previewGambar').src         = '';
        const btn = document.getElementById('btnSimpanPengumuman');
        btn.disabled    = false;
        btn.textContent = 'Simpan';
        btn.dataset.mode = 'store';   // ← WAJIB
        delete btn.dataset.updateId;
        delete btn.dataset.updateUrl;
    }

    document.getElementById('btnTambahPengumuman').addEventListener('click', function () {
        resetModal();
        modalPengumuman.show();
    });

    function handleEdit() {
        resetModal();
        document.getElementById('modalPengumumanTitle').textContent = 'Ubah Pengumuman';
        document.getElementById('pJudul').value                     = this.dataset.judul;
        document.getElementById('pDeskripsi').value                 = this.dataset.deskripsi;
        document.getElementById('pStatus').value                    = this.dataset.status;
        const btn = document.getElementById('btnSimpanPengumuman');
        btn.dataset.mode      = 'update';
        btn.dataset.updateId  = this.dataset.id;
        btn.dataset.updateUrl = this.dataset.url;
        modalPengumuman.show();
    }

    document.querySelectorAll('.btn-edit-pengumuman').forEach(btn => {
        btn.addEventListener('click', handleEdit);
    });

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

    document.getElementById('btnSimpanPengumuman').addEventListener('click', async function () {
        const judul     = document.getElementById('pJudul').value.trim();
        const deskripsi = document.getElementById('pDeskripsi').value.trim();
        const status    = document.getElementById('pStatus').value;
        const fileInput = document.getElementById('pFile');
        const isUpdate  = this.dataset.mode === 'update';

        if (!judul || !status) {
            toast('Judul dan status wajib diisi.', 'error');
            return;
        }

        this.disabled    = true;
        this.textContent = 'Menyimpan...';

        const fd = new FormData();
        fd.append('_method',   isUpdate ? 'PUT' : 'POST');
        fd.append('judul',     judul);
        fd.append('deskripsi', deskripsi);
        fd.append('status',    status);
        if (fileInput.files[0]) fd.append('file', fileInput.files[0]);

        try {
            const url = isUpdate ? this.dataset.updateUrl : storeUrl;
            const res = await sendFormData(url, fd);

            if (res.success) {
                modalPengumuman.hide();
                toast(isUpdate ? 'Pengumuman berhasil diubah!' : 'Pengumuman berhasil ditambahkan!');
                if (isUpdate) {
                    updateRow(this.dataset.updateId, judul, deskripsi, status, res.file_url);
                } else {
                    appendRow(res.pengumuman);
                }
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
                this.disabled    = false;
                this.textContent = 'Simpan';
            }
        } catch (e) {
            console.error(e);
            toast('Terjadi kesalahan.', 'error');
            this.disabled    = false;
            this.textContent = 'Simpan';
        }
    });

    function handleHapus() {
        document.getElementById('namaPengumumanHapus').textContent = this.dataset.nama;
        const btn = document.getElementById('btnHapusPengumuman');
        btn.dataset.id   = this.dataset.id;
        btn.dataset.url  = this.dataset.url;
        btn.dataset.nama = this.dataset.nama;
        modalHapusPengumuman.show();
    }

    document.querySelectorAll('.btn-hapus-pengumuman').forEach(btn => {
        btn.addEventListener('click', handleHapus);
    });

    document.getElementById('btnHapusPengumuman').addEventListener('click', async function () {
        const url  = this.dataset.url;
        const id   = this.dataset.id;
        const nama = this.dataset.nama;

        this.disabled    = true;
        this.textContent = 'Menghapus...';

        try {
            const res = await sendRequest(url, 'POST', { _method: 'DELETE' });
            if (res.success) {
                modalHapusPengumuman.hide();
                toast(`Pengumuman "${nama}" berhasil dihapus!`);
                document.querySelectorAll('.btn-hapus-pengumuman').forEach(btn => {
                    if (btn.dataset.id == id) btn.closest('tr').remove();
                });
                tbody.querySelectorAll('tr').forEach((tr, i) => {
                    if (!tr.querySelector('.empty-row')) tr.cells[0].textContent = i + 1;
                });
                totalSpan.textContent = tbody.querySelectorAll('tr:not(:has(.empty-row))').length;
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch (e) {
            console.error(e);
            toast('Terjadi kesalahan.', 'error');
        }

        this.disabled    = false;
        this.textContent = 'Hapus';
    });

    document.getElementById('searchPengumuman').addEventListener('keyup', function () {
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