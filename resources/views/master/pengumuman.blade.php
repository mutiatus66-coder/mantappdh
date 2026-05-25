@extends('index', ['dummy' => true])

@section('content')
<style>
    /* Gaya tambahan untuk card dan tabel (konsisten dengan penilai) */
    .pengumuman-container {
        background: var(--ri-card-bg);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
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
        font-weight: bold;@extends('index', ['dummy' => true])

@section('content')
<style>
    .pengumuman-container {
        background: var(--ri-card-bg);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
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
    .pengumuman-stats {
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
        color: var(--ri-text-muted);
        border-bottom: 2px solid var(--ri-table-border-header);
    }
    .pengumuman-table td {
        padding: 12px;
        border-bottom: 1.5px solid var(--ri-table-border-row);
        color: var(--ri-text-primary);
        font-size: 0.875rem;
        background: var(--ri-table-row-bg);
    }
    .pengumuman-table tr:hover td {
        background: var(--ri-table-row-hover);
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-published {
        background: #dcfce7;
        color: #166534;
    }
    .status-draft {
        background: #fef9c3;
        color: #854d0e;
    }
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
        <button class="btn btn-primary" id="btnTambahPengumuman">Tambah Pengumuman</button>
    </div>

    <div class="pengumuman-stats">
        <div class="total-badge">Total Pengumuman: <span id="totalPengumuman">{{ $pengumuman->count() }}</span></div>
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
                    <th>File</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPengumumanBody">
                @forelse($pengumuman as $index => $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->judul }}</td>
                    <td>{{ Str::limit($p->deskripsi, 80) }}</td>
                    <td>
                        <span class="status-badge {{ $p->status == 'Published' ? 'status-published' : 'status-draft' }}">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td>
                        @if($p->file_path)
                            <a href="{{ asset('storage/' . $p->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat File</a>
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; justify-content:center; gap:8px;">
                            <button class="btn btn-warning btn-edit-pengumuman"
                                    data-id="{{ $p->id }}"
                                    data-judul="{{ $p->judul }}"
                                    data-deskripsi="{{ $p->deskripsi }}"
                                    data-status="{{ $p->status }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-hapus-pengumuman"
                                    data-id="{{ $p->id }}"
                                    data-judul="{{ $p->judul }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-row">Belum ada pengumuman</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">
            Tidak ada data yang cocok
        </div>
    </div>
</div>

{{-- MODAL Tambah / Ubah --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="formPengumuman" method="POST" action="{{ route('admin.pengumuman.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formPengumumanMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPengumumanTitle">Tambah Pengumuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" id="judul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File (PDF/DOC)</label>
                        <input type="file" class="form-control" name="file" id="file">
                        <small class="text-muted">Kosongkan jika tidak mengubah (pada edit).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL Hapus --}}
<div class="modal fade" id="modalHapusPengumuman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus pengumuman <strong id="hapusJudulSpan"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusPengumuman" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Search
    const searchInput = document.getElementById('searchPengumuman');
    const rows = document.querySelectorAll('#tabelPengumumanBody tr');
    const emptyMsg = document.getElementById('emptySearchMessage');
    const totalSpan = document.getElementById('totalPengumuman');
    searchInput.addEventListener('keyup', function () {
        let kw = this.value.toLowerCase();
        let n = 0;
        rows.forEach(row => {
            const judul = row.cells[1]?.innerText.toLowerCase() || '';
            const deskripsi = row.cells[2]?.innerText.toLowerCase() || '';
            const show = judul.includes(kw) || deskripsi.includes(kw);
            row.style.display = show ? '' : 'none';
            if (show) n++;
        });
        emptyMsg.style.display = n === 0 ? 'block' : 'none';
        totalSpan.innerText = n;
    });

    // Reset modal tambah/ubah
    const modalPengumuman = document.getElementById('modalPengumuman');
    modalPengumuman.addEventListener('hidden.bs.modal', function () {
        document.getElementById('formPengumuman').action = "{{ route('admin.pengumuman.store') }}";
        document.getElementById('formPengumumanMethod').value = 'POST';
        document.getElementById('modalPengumumanTitle').textContent = 'Tambah Pengumuman';
        document.getElementById('judul').value = '';
        document.getElementById('deskripsi').value = '';
        document.getElementById('status').value = 'Published';
        document.getElementById('file').value = '';
    });

    // Tambah
    document.getElementById('btnTambahPengumuman').addEventListener('click', function () {
        new bootstrap.Modal(modalPengumuman).show();
    });

    // Edit
    document.querySelectorAll('.btn-edit-pengumuman').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            document.getElementById('modalPengumumanTitle').textContent = 'Ubah Pengumuman';
            document.getElementById('formPengumuman').action = `/pengumuman/${id}`;
            document.getElementById('formPengumumanMethod').value = 'PUT';
            document.getElementById('judul').value = this.dataset.judul;
            document.getElementById('deskripsi').value = this.dataset.deskripsi;
            document.getElementById('status').value = this.dataset.status;
            document.getElementById('file').value = '';
            new bootstrap.Modal(modalPengumuman).show();
        });
    });

    // Hapus
    const hapusModal = document.getElementById('modalHapusPengumuman');
    document.querySelectorAll('.btn-hapus-pengumuman').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('hapusJudulSpan').textContent = this.dataset.judul;
            document.getElementById('formHapusPengumuman').action = `/pengumuman/${this.dataset.id}`;
            new bootstrap.Modal(hapusModal).show();
        });
    });
});
</script>
@endsection
        margin: 0;
        color: var(--ri-text-primary);
    }
    .pengumuman-title p {
        margin: 0;
        color: var(--ri-text-muted);
        font-size: 0.875rem;
    }
    .pengumuman-stats {
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
        color: var(--ri-text-muted);
        border-bottom: 2px solid var(--ri-table-border-header);
    }
    .pengumuman-table td {
        padding: 12px;
        border-bottom: 1.5px solid var(--ri-table-border-row);
        color: var(--ri-text-primary);
        font-size: 0.875rem;
        background: var(--ri-table-row-bg);
    }
    .pengumuman-table tr:hover td {
        background: var(--ri-table-row-hover);
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-published {
        background: #dcfce7;
        color: #166534;
    }
    .status-draft {
        background: #fef9c3;
        color: #854d0e;
    }
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
        <!-- Tombol Tambah: btn-primary -->
        <button class="btn btn-primary" id="btnTambahPengumuman">
            Tambah Pengumuman
        </button>
    </div>

    <div class="pengumuman-stats">
        <div class="total-badge">Total Pengumuman: <span id="totalPengumuman">{{ $pengumuman->count() }}</span></div>
        <div class="search-box">
            <input type="text" id="searchPengumuman" placeholder="Cari judul atau deskripsi...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="pengumuman-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Pengumuman</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelPengumumanBody">
                @forelse($pengumuman as $index => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->judul }}</td>
                    <td>{{ Str::limit($item->deskripsi, 80) }}</td>
                    <td>
                        <span class="status-badge {{ $item->status == 'Published' ? 'status-published' : 'status-draft' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap;">
                            @if($item->file_path)
                            <a href="{{ asset('storage/'.$item->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat File">📄</a>
                            @endif
                            <button class="btn btn-warning btn-sm btn-edit-pengumuman"
                                    data-id="{{ $item->id }}"
                                    data-judul="{{ $item->judul }}"
                                    data-deskripsi="{{ $item->deskripsi }}"
                                    data-status="{{ $item->status }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-sm btn-hapus-pengumuman"
                                    data-id="{{ $item->id }}"
                                    data-judul="{{ $item->judul }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-row">Belum ada pengumuman</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">
            Tidak ada pengumuman yang cocok
        </div>
    </div>
</div>

{{-- MODAL Tambah / Edit Pengumuman --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formPengumuman" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPengumumanTitle">Tambah Pengumuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Pengumuman</label>
                        <input type="text" class="form-control" name="judul" id="judul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File (PDF/DOC)</label>
                        <input type="file" class="form-control" name="file" id="file">
                        <small class="text-muted">Kosongkan jika tidak mengubah file (pada edit).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapusPengumuman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus pengumuman "<strong id="hapusJudulSpan"></strong>"?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusPengumuman" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search filter
        const searchInput = document.getElementById('searchPengumuman');
        const rows = document.querySelectorAll('#tabelPengumumanBody tr');
        const emptyMsg = document.getElementById('emptySearchMessage');
        const totalSpan = document.getElementById('totalPengumuman');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                let keyword = this.value.toLowerCase();
                let visibleCount = 0;
                rows.forEach(row => {
                    const judul = row.cells[1]?.innerText.toLowerCase() || '';
                    const deskripsi = row.cells[2]?.innerText.toLowerCase() || '';
                    if (judul.includes(keyword) || deskripsi.includes(keyword)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
                totalSpan.innerText = visibleCount;
            });
        }

        // Reset modal
        const modalPengumuman = document.getElementById('modalPengumuman');
        modalPengumuman.addEventListener('hidden.bs.modal', function() {
            document.getElementById('formPengumuman').action = "{{ route('pengumuman.store') }}";
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('modalPengumumanTitle').innerText = 'Tambah Pengumuman';
            document.getElementById('judul').value = '';
            document.getElementById('deskripsi').value = '';
            document.getElementById('status').value = 'Published';
            document.getElementById('file').value = '';
        });

        // Tombol Tambah
        document.getElementById('btnTambahPengumuman').addEventListener('click', function() {
            new bootstrap.Modal(modalPengumuman).show();
        });

        // Tombol Edit
        document.querySelectorAll('.btn-edit-pengumuman').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('modalPengumumanTitle').innerText = 'Edit Pengumuman';
                document.getElementById('formPengumuman').action = `/pengumuman/${id}`;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('judul').value = this.dataset.judul;
                document.getElementById('deskripsi').value = this.dataset.deskripsi;
                document.getElementById('status').value = this.dataset.status;
                document.getElementById('file').value = ''; // reset file input
                new bootstrap.Modal(modalPengumuman).show();
            });
        });

        // Tombol Hapus
        const hapusModal = document.getElementById('modalHapusPengumuman');
        document.querySelectorAll('.btn-hapus-pengumuman').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('hapusJudulSpan').innerText = this.dataset.judul;
                document.getElementById('formHapusPengumuman').action = `/pengumuman/${this.dataset.id}`;
                new bootstrap.Modal(hapusModal).show();
            });
        });
    });
</script>
@endsection
