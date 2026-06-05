@extends('index', ['dummy' => true])

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

    @if(session('success'))
    <div class="alert alert-dismissible fade show mb-4" role="alert"
        style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#92400e;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

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
                                data-status="{{ $p->status }}">
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
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">
            Tidak ada data yang cocok
        </div>
    </div>

</div>


{{-- ══ MODAL — Tambah / Ubah Pengumuman ══ --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formPengumuman" method="POST" action="{{ route('pengumuman.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formPengumumanMethod" value="POST">

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
                            <input type="text" name="judul" id="pJudul" class="form-control"
                                placeholder="Masukkan judul pengumuman" required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" id="pDeskripsi" class="form-control"
                                rows="4" placeholder="Masukkan deskripsi pengumuman"></textarea>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Status</label>
                            <select name="status" id="pStatus" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Published">Published</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">File (PDF/DOC/DOCX/JPG/PNG, maks 2MB)</label>
                            <input type="file" name="file" id="pFile" class="form-control"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp,.svg">
                        </div>
                        {{-- Preview gambar --}}
                        <div class="col-md-12 mb-2" id="previewWrap" style="display:none;">
                            <img id="previewGambar" src="" alt="Preview"
                                style="max-height:180px; border-radius:8px; border:1px solid #e5e7eb; object-fit:cover;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
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
                <form id="formHapusPengumuman" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-aksi px-3">Hapus</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl    = "{{ route('pengumuman.store') }}";
    const searchInput = document.getElementById('searchPengumuman');
    const rows        = document.querySelectorAll('#tabelPengumumanBody tr');
    const totalSpan   = document.getElementById('totalPengumuman');

    // ── Search ────────────────────────────────────────────
    searchInput.addEventListener('keyup', function () {
        const kw = this.value.toLowerCase().trim();
        let n = 0;
        rows.forEach(r => {
            if (r.querySelector('.empty-row')) return;
            const show = r.textContent.toLowerCase().includes(kw);
            r.style.display = show ? '' : 'none';
            if (show) n++;
        });
        totalSpan.textContent = n;
        document.getElementById('emptySearchMessage').style.display = n === 0 ? 'block' : 'none';
    });

    // ── Reset modal saat ditutup (SATU listener saja) ─────
    document.getElementById('modalPengumuman').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formPengumuman').action            = storeUrl;
        document.getElementById('formPengumumanMethod').value       = 'POST';
        document.getElementById('modalPengumumanTitle').textContent = 'Tambah Pengumuman';
        document.getElementById('pJudul').value     = '';
        document.getElementById('pDeskripsi').value = '';
        document.getElementById('pStatus').value    = '';
        document.getElementById('pFile').value      = '';
        document.getElementById('previewWrap').style.display = 'none';  // ← reset preview
        document.getElementById('previewGambar').src         = '';
    });

    // ── Tambah ────────────────────────────────────────────
    document.getElementById('btnTambahPengumuman').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalPengumuman')).show();
    });

    // ── Edit ──────────────────────────────────────────────
    document.querySelectorAll('.btn-edit-pengumuman').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalPengumumanTitle').textContent = 'Ubah Pengumuman';
            document.getElementById('formPengumuman').action            = `/pengumuman/${this.dataset.id}`;
            document.getElementById('formPengumumanMethod').value       = 'PUT';
            document.getElementById('pJudul').value                     = this.dataset.judul;
            document.getElementById('pDeskripsi').value                 = this.dataset.deskripsi;
            document.getElementById('pStatus').value                    = this.dataset.status;
            new bootstrap.Modal(document.getElementById('modalPengumuman')).show();
        });
    });

    // ── Preview gambar sebelum upload ─────────────────────
    document.getElementById('pFile').addEventListener('change', function () {
        const file = this.files[0];
        const wrap = document.getElementById('previewWrap');
        const img  = document.getElementById('previewGambar');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                wrap.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            wrap.style.display = 'none';
            img.src = '';
        }
    });

    // ── Hapus ─────────────────────────────────────────────
    document.querySelectorAll('.btn-hapus-pengumuman').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaPengumumanHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusPengumuman').action      = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusPengumuman')).show();
        });
    });

}); // ← penutup DOMContentLoaded
</script>
@endpush