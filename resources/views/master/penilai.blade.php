@extends('index', ['dummy' => true])

@section('content')
<style>
    /* Gaya tambahan untuk card dan tabel (jika belum ada) */
    .penilai-container {
        background: var(--ri-card-bg);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
    }
    .penilai-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .penilai-title h3 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
        color: var(--ri-text-primary);
    }
    .penilai-title p {
        margin: 0;
        color: var(--ri-text-muted);
        font-size: 0.875rem;
    }
    .penilai-stats {
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
    .penilai-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid var(--ri-table-border-outer);
        border-radius: 8px;
        overflow: hidden;
    }
    .penilai-table th {
        background: var(--ri-table-head-bg);
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--ri-text-muted);
        border-bottom: 2px solid var(--ri-table-border-header);
    }
    .penilai-table td {
        padding: 12px;
        border-bottom: 1.5px solid var(--ri-table-border-row);
        color: var(--ri-text-primary);
        font-size: 0.875rem;
        background: var(--ri-table-row-bg);
    }
    .penilai-table tr:hover td {
        background: var(--ri-table-row-hover);
    }
    @media (max-width: 640px) {
        .penilai-container { margin: 10px; padding: 12px; }
        .penilai-table th, .penilai-table td { padding: 8px; }
        .search-box input { width: 100%; }
    }
</style>

<div class="penilai-container">
    <div class="penilai-header">
        <div class="penilai-title">
            <h3>Master Penilai</h3>
            <p>Kelola data penilai</p>
        </div>
        <!-- Tombol Tambah: btn-primary -->
        <button class="btn btn-primary" id="btnTambahPenilai">
            Tambah Penilai
        </button>
    </div>

    <div class="penilai-stats">
        <div class="total-badge">Total Penilai: <span id="totalPenilai">{{ count($penilai) }}</span></div>
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
                @forelse($penilai as $index => $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p['nama'] }}</td>
                    <td>{{ $p['email'] }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex; justify-content:center; gap:8px;">
                            <!-- Tombol Ubah: btn-warning -->
                            <button class="btn btn-warning btn-edit-penilai"
                                    data-id="{{ $p['id'] }}"
                                    data-nama="{{ $p['nama'] }}"
                                    data-email="{{ $p['email'] }}">
                                Ubah
                            </button>
                            <!-- Tombol Hapus: btn-danger -->
                            <button class="btn btn-danger btn-hapus-penilai"
                                    data-id="{{ $p['id'] }}"
                                    data-nama="{{ $p['nama'] }}"
                                    data-url="{{ route('penilai.destroy', $p['id']) }}">
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
        <div id="emptySearchMessage" style="display:none; text-align:center; padding:20px; color:var(--ri-text-muted);">
            Tidak ada data yang cocok
        </div>
    </div>
</div>

{{-- MODAL Tambah / Ubah Penilai --}}
<div class="modal fade" id="modalPenilai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formPenilai" method="POST" action="{{ route('penilai.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formPenilaiMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPenilaiTitle">Tambah Penilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Penilai</label>
                        <input type="text" class="form-control" name="nama" id="penilaiNama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="penilaiEmail" required>
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
<div class="modal fade" id="modalHapusPenilai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus penilai <strong id="namaPenilaiHapus"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusPenilai" method="POST">
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
    const storeUrl = "{{ route('penilai.store') }}";

    // Search
    const searchInput = document.getElementById('searchPenilai');
    const rows = document.querySelectorAll('#tabelPenilaiBody tr');
    const emptyMsg = document.getElementById('emptySearchMessage');
    const totalSpan = document.getElementById('totalPenilai');
    searchInput.addEventListener('keyup', function () {
        let kw = this.value.toLowerCase();
        let n = 0;
        rows.forEach(row => {
            const nama = row.cells[1]?.innerText.toLowerCase() || '';
            const email = row.cells[2]?.innerText.toLowerCase() || '';
            const show = nama.includes(kw) || email.includes(kw);
            row.style.display = show ? '' : 'none';
            if (show) n++;
        });
        emptyMsg.style.display = n === 0 ? 'block' : 'none';
        totalSpan.innerText = n;
    });

    // Reset modal tambah/ubah
    document.getElementById('modalPenilai').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formPenilai').action = storeUrl;
        document.getElementById('formPenilaiMethod').value = 'POST';
        document.getElementById('modalPenilaiTitle').textContent = 'Tambah Penilai';
        document.getElementById('penilaiNama').value = '';
        document.getElementById('penilaiEmail').value = '';
    });

    // Tombol Tambah
    document.getElementById('btnTambahPenilai').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalPenilai')).show();
    });

    // Tombol Ubah
    document.querySelectorAll('.btn-edit-penilai').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            document.getElementById('modalPenilaiTitle').textContent = 'Ubah Penilai';
            document.getElementById('formPenilai').action = `/admin/penilai/${id}`;
            document.getElementById('formPenilaiMethod').value = 'PUT';
            document.getElementById('penilaiNama').value = this.dataset.nama;
            document.getElementById('penilaiEmail').value = this.dataset.email;
            new bootstrap.Modal(document.getElementById('modalPenilai')).show();
        });
    });

    // Tombol Hapus
    const hapusModal = document.getElementById('modalHapusPenilai');
    document.querySelectorAll('.btn-hapus-penilai').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaPenilaiHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusPenilai').action = this.dataset.url;
            new bootstrap.Modal(hapusModal).show();
        });
    });
});
</script>
@endsection
