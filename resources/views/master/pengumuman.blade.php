@extends('index', ['dummy' => true])

@section('content')
<style>
    .btn-warning {
    background: #65A605 !important;
    border-color: #65A605 !important;
    color: #fff !important;
}
.btn-warning:hover {
    background: #538a04 !important;
    border-color: #538a04 !important;
}
.pengumuman-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}
.pengumuman-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.pengumuman-title h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}
.pengumuman-title p {
    margin: 0;
    color: var(--ri-text-muted);
    font-size: 0.875rem;
}
.btn-tambah {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}
.btn-tambah:hover { opacity: 0.9; }
.stats-search {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.total-badge {
    background: #dcfce7;
    color: #166534;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.875rem;
    font-weight: 600;
}
.search-box input {
    padding: 6px 12px 6px 32px;
    border: 1px solid var(--ri-border);
    border-radius: 8px;
    font-size: 0.875rem;
    width: 240px;
    background: var(--ri-input-bg);
    color: var(--ri-text-primary);
    transition: background 0.2s, color 0.2s, border-color 0.2s;
}
.pengumuman-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid var(--ri-table-border-outer);
    border-radius: 8px;
    overflow: hidden;
}
.pengumuman-table th {
    background: var(--ri-table-head-bg);
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ri-text-muted);
    border-bottom: 2px solid var(--ri-table-border-header);
    transition: background 0.2s, color 0.2s;
}
.pengumuman-table td {
    padding: 12px;
    border-bottom: 1.5px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    font-size: 0.875rem;
    background: var(--ri-table-row-bg);
    transition: background 0.2s, color 0.2s;
}
.pengumuman-table tr:hover td { background: var(--ri-table-row-hover); }
.pengumuman-table tr:last-child td { border-bottom: none; }
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-published { background: #dcfce7; color: #166534; }
.status-draft     { background: #fef9c3; color: #854d0e; }
.action-buttons   { display: flex; justify-content: center; gap: 8px; }
.btn-edit-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
    border: none; border-radius: 6px;
    padding: 6px 14px; font-size: 0.8rem; font-weight: 600;
    cursor: pointer; transition: opacity 0.15s;
}
.btn-edit-icon:hover { opacity: 0.88; }
.btn-hapus {
    background: #A32D2D; color: #ffffff !important;
    border: none; font-weight: 600;
    padding: 6px 14px; border-radius: 6px;
    cursor: pointer; font-size: 0.8rem; transition: background 0.15s;
}
.btn-hapus:hover { background: #8b2424; }
.empty-row {
    text-align: center; padding: 30px;
    color: var(--ri-text-muted); background: var(--ri-table-row-bg);
}
/* Hapus modal */
.hapus-icon-circle {
    width: 56px; height: 56px; border-radius: 50%;
    background: #FCEBEB;
    display: flex; align-items: center; justify-content: center;
}
[data-bs-theme="dark"] .hapus-icon-circle  { background: rgba(163,45,45,0.20); }
[data-bs-theme="dark"] .hapus-teks-muted   { color: rgba(245,240,232,.55) !important; }
[data-bs-theme="dark"] .hapus-nama-strong  { color: #F5F0E8 !important; }
@media (max-width: 640px) {
    .pengumuman-container { margin: 10px; padding: 12px; }
    .pengumuman-table th, .pengumuman-table td { padding: 8px; }
    .search-box input { width: 100%; }
}
</style>

<div class="pengumuman-container">
    <div class="pengumuman-header">
        <div class="pengumuman-title">
            <h3>Master Pengumuman</h3>
            <p>Kelola pengumuman yang ditampilkan ke publik</p>
        </div>
        <button class="btn-tambah" id="btnTambahPengumuman">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pengumuman
        </button>
    </div>

    <div class="stats-search">
        <div class="total-badge">Total Pengumuman: <span id="totalPengumuman">{{ count($pengumuman ?? []) }}</span></div>
        <div class="search-box">
            <input type="text" id="searchPengumuman" placeholder="Cari judul atau deskripsi...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="pengumuman-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="pengumumanBody">
                @forelse(($pengumuman ?? []) as $index => $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p['judul'] }}</td>
                    <td>{{ Str::limit($p['deskripsi'], 60) }}</td>
                    <td>
                        <span class="status-badge {{ $p['status'] == 'Published' ? 'status-published' : 'status-draft' }}">
                            {{ $p['status'] }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div class="action-buttons">
                            <button class="btn-edit-icon btn-edit-pengumuman"
                                    data-id="{{ $p['id'] }}"
                                    data-judul="{{ $p['judul'] }}"
                                    data-deskripsi="{{ $p['deskripsi'] }}"
                                    data-status="{{ $p['status'] }}">
                                Ubah
                            </button>
                            <button class="btn-hapus btn-hapus-pengumuman"
                                    data-id="{{ $p['id'] }}"
                                    data-judul="{{ $p['judul'] }}"
                                    data-url="{{ route('admin.pengumuman.destroy', $p['id']) }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-row">Belum ada pengumuman. Silakan tambah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">
            Tidak ada pengumuman yang cocok
        </div>
    </div>
</div>


{{-- ══ MODAL — Tambah / Ubah Pengumuman ══ --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formPengumuman" method="POST" action="{{ route('admin.pengumuman.store') }}">
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
                            <input type="text" name="judul" id="pengumumanJudul"
                                   class="form-control" placeholder="Masukkan judul pengumuman..." required>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Deskripsi</label>
                            <textarea name="deskripsi" id="pengumumanDeskripsi"
                                      class="form-control" rows="4"
                                      placeholder="Masukkan isi pengumuman..." required></textarea>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="d-flex gap-4 mt-1">
                                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                    <input type="radio" name="status" id="statusPublished" value="Published" checked> Published
                                </label>
                                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                    <input type="radio" name="status" id="statusDraft" value="Draft"> Draft
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4">Simpan</button>
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
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Pengumuman
                <strong id="judulPengumumanHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusPengumuman" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-trash3 me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl = "{{ route('admin.pengumuman.store') }}";

    // Search
    const searchInput = document.getElementById('searchPengumuman');
    const rows        = document.querySelectorAll('#pengumumanBody tr');
    const emptyMsg    = document.getElementById('emptySearchMessage');
    const totalSpan   = document.getElementById('totalPengumuman');

    searchInput.addEventListener('keyup', function () {
        let kw = this.value.toLowerCase();
        let n  = 0;
        rows.forEach(row => {
            const judul  = row.cells[1]?.innerText.toLowerCase() || '';
            const desk   = row.cells[2]?.innerText.toLowerCase() || '';
            const show   = judul.includes(kw) || desk.includes(kw);
            row.style.display = show ? '' : 'none';
            if (show) n++;
        });
        emptyMsg.style.display = n === 0 ? 'block' : 'none';
        totalSpan.innerText = n;
    });

    // ── Reset modal ──
    document.getElementById('modalPengumuman').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formPengumuman').action      = storeUrl;
        document.getElementById('formPengumumanMethod').value = 'POST';
        document.getElementById('modalPengumumanTitle').textContent = 'Tambah Pengumuman';
        document.getElementById('pengumumanJudul').value      = '';
        document.getElementById('pengumumanDeskripsi').value  = '';
        document.getElementById('statusPublished').checked    = true;
    });

    // ── Tambah ──
    document.getElementById('btnTambahPengumuman').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalPengumuman')).show();
    });

    // ── Ubah ──
    document.querySelectorAll('.btn-edit-pengumuman').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;

            document.getElementById('modalPengumumanTitle').textContent    = 'Ubah Pengumuman';
            document.getElementById('formPengumuman').action               = `/admin/pengumuman/${id}`;
            document.getElementById('formPengumumanMethod').value          = 'PUT';
            document.getElementById('pengumumanJudul').value               = this.dataset.judul;
            document.getElementById('pengumumanDeskripsi').value           = this.dataset.deskripsi;

            if (this.dataset.status === 'Draft') {
                document.getElementById('statusDraft').checked = true;
            } else {
                document.getElementById('statusPublished').checked = true;
            }

            new bootstrap.Modal(document.getElementById('modalPengumuman')).show();
        });
    });

    // ── Hapus ──
    document.querySelectorAll('.btn-hapus-pengumuman').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('judulPengumumanHapus').textContent  = this.dataset.judul;
            document.getElementById('formHapusPengumuman').action        = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusPengumuman')).show();
        });
    });

});
</script>
@endsection