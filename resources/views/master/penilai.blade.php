@extends('index', ['dummy' => true])

@section('content')
<style>
.btn-simpan {
    background: #00838F !important;
    border-color: #00838F !important;
    color: #fff !important;
    font-weight: 600;
    transition: background 0.15s, border-color 0.15s;
}
.btn-simpan:hover {
    background: #006064 !important;
    border-color: #006064 !important;
    color: #fff !important;
}
.btn-batal {
    background: #546E7A !important;
    border-color: #546E7A !important;
    color: #fff !important;
    font-weight: 600;
    transition: background 0.15s, border-color 0.15s;
}
.btn-batal:hover {
    background: #455A64 !important;
    border-color: #455A64 !important;
    color: #fff !important;
}
.penilai-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
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
.btn-tambah:hover { opacity: 0.9; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
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
    transition: background 0.2s, color 0.2s, border-color 0.2s;
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
    letter-spacing: 0.05em;
    color: var(--ri-text-muted);
    border-bottom: 2px solid var(--ri-table-border-header);
    transition: background 0.2s, color 0.2s;
}
.penilai-table td {
    padding: 12px;
    border-bottom: 1.5px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    font-size: 0.875rem;
    background: var(--ri-table-row-bg);
    transition: background 0.2s, color 0.2s;
}
.penilai-table tr:hover td { background: var(--ri-table-row-hover); }
.penilai-table tr:last-child td { border-bottom: none; }
.btn-gold {
    background: linear-gradient(135deg, #142D54, #0C4C8A);
    color: white !important;
    border: none; border-radius: 6px;
    padding: 6px 14px; font-size: 0.8rem; font-weight: 600;
    cursor: pointer; transition: opacity .18s;
}
.btn-gold:hover { opacity: .88; }
.btn-hapus {
    background: #A32D2D; color: #ffffff !important;
    border: none; font-weight: 600;
    padding: 6px 14px; border-radius: 6px;
    cursor: pointer; font-size: 0.8rem;
    display: inline-flex; align-items: center; gap: 4px;
    transition: background 0.15s;
}
.btn-hapus:hover { background: #8b2424; }
.empty-row {
    text-align: center; padding: 30px;
    color: var(--ri-text-muted); background: var(--ri-table-row-bg);
}
.hapus-icon-circle {
    width: 56px; height: 56px; border-radius: 50%;
    background: #FCEBEB;
    display: flex; align-items: center; justify-content: center;
}
[data-bs-theme="dark"] .hapus-icon-circle  { background: rgba(163,45,45,0.20); }
[data-bs-theme="dark"] .hapus-teks-muted   { color: rgba(245,240,232,.55) !important; }
[data-bs-theme="dark"] .hapus-nama-strong  { color: #F5F0E8 !important; }
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
        <button class="btn-tambah" id="btnTambahPenilai">
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
                            <button class="btn-gold btn-edit-penilai"
                                    data-id="{{ $p['id'] }}"
                                    data-nama="{{ $p['nama'] }}"
                                    data-email="{{ $p['email'] }}">
                                Ubah
                            </button>
                            <button class="btn-hapus btn-hapus-penilai"
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
                    <td colspan="4" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data penilai
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


{{-- ══ MODAL — Tambah / Ubah Penilai ══ --}}
<div class="modal fade" id="modalPenilai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formPenilai" method="POST" action="{{ route('penilai.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formPenilaiMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalPenilaiTitle">Tambah Penilai</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Nama Penilai</label>
                            <input type="text" name="nama" id="penilaiNama"
                                   class="form-control" placeholder="Masukkan nama penilai..." required>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-semibold required">Email</label>
                            <input type="email" name="email" id="penilaiEmail"
                                   class="form-control" placeholder="Masukkan email..." required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-simpan px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Penilai ══ --}}
<div class="modal fade" id="modalHapusPenilai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Penilai
                <strong id="namaPenilaiHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-batal btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusPenilai" method="POST">
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

    const storeUrl = "{{ route('penilai.store') }}";

    const searchInput = document.getElementById('searchPenilai');
    const rows        = document.querySelectorAll('#tabelPenilaiBody tr');
    const emptyMsg    = document.getElementById('emptySearchMessage');
    const totalSpan   = document.getElementById('totalPenilai');

    searchInput.addEventListener('keyup', function () {
        let kw = this.value.toLowerCase();
        let n  = 0;
        rows.forEach(row => {
            const nama  = row.cells[1]?.innerText.toLowerCase() || '';
            const email = row.cells[2]?.innerText.toLowerCase() || '';
            const show  = nama.includes(kw) || email.includes(kw);
            row.style.display = show ? '' : 'none';
            if (show) n++;
        });
        emptyMsg.style.display = n === 0 ? 'block' : 'none';
        totalSpan.innerText = n;
    });

    document.getElementById('modalPenilai').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formPenilai').action      = storeUrl;
        document.getElementById('formPenilaiMethod').value = 'POST';
        document.getElementById('modalPenilaiTitle').textContent = 'Tambah Penilai';
        document.getElementById('penilaiNama').value  = '';
        document.getElementById('penilaiEmail').value = '';
    });

    document.getElementById('btnTambahPenilai').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalPenilai')).show();
    });

    document.querySelectorAll('.btn-edit-penilai').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalPenilaiTitle').textContent = 'Ubah Penilai';
            document.getElementById('formPenilai').action            = `/admin/penilai/${this.dataset.id}`;
            document.getElementById('formPenilaiMethod').value       = 'PUT';
            document.getElementById('penilaiNama').value             = this.dataset.nama;
            document.getElementById('penilaiEmail').value            = this.dataset.email;
            new bootstrap.Modal(document.getElementById('modalPenilai')).show();
        });
    });

    document.querySelectorAll('.btn-hapus-penilai').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaPenilaiHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusPenilai').action      = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusPenilai')).show();
        });
    });

});
</script>
@endsection