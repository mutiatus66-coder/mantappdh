@extends('index', ['dummy' => true])

@section('content')
<div class="penilai-container">
    <div class="penilai-header">
        <div class="penilai-title">
            <h3>Master Penilai</h3>
            <p>Kelola data penilai</p>
        </div>
        <button class="btn btn-primary" id="btnTambahPenilai">Tambah Penilai</button>
    </div>

    @if(session('success'))
    <div class="alert alert-dismissible fade show mb-4" role="alert"
         style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#92400e;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

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
                                    data-email="{{ $p->email }}">Ubah</button>
                            <button class="btn btn-danger btn-hapus-penilai"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama }}"
                                    data-url="{{ route('penilai.destroy', $p->id) }}">Hapus</button>
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
                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Nama</label>
                        <input type="text" name="nama" id="inputNama"
                               class="form-control" placeholder="Nama penilai..." required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Email</label>
                        <input type="email" name="email" id="inputEmail"
                               class="form-control" placeholder="Email penilai..." required>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus ══ --}}
<div class="modal fade" id="modalHapusPenilai" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4" style="font-size:.875rem; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Penilai
                <strong id="namaPenilaiHapus"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusPenilai" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3 me-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl    = "{{ route('penilai.store') }}";
    const searchInput = document.getElementById('searchPenilai');
    const rows        = document.querySelectorAll('#tabelPenilaiBody tr');
    const totalSpan   = document.getElementById('totalPenilai');

    // Search
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
    });

    // Reset modal on close
    document.getElementById('modalPenilai').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formPenilai').action      = storeUrl;
        document.getElementById('formPenilaiMethod').value = 'POST';
        document.getElementById('modalPenilaiTitle').textContent = 'Tambah Penilai';
        document.getElementById('inputNama').value  = '';
        document.getElementById('inputEmail').value = '';
    });

    // Tambah
    document.getElementById('btnTambahPenilai').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalPenilai')).show();
    });

    // Edit
    document.querySelectorAll('.btn-edit-penilai').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalPenilaiTitle').textContent  = 'Ubah Penilai';
            document.getElementById('formPenilai').action             = `/penilai/${this.dataset.id}`;
            document.getElementById('formPenilaiMethod').value        = 'PUT';
            document.getElementById('inputNama').value                = this.dataset.nama;
            document.getElementById('inputEmail').value               = this.dataset.email;
            new bootstrap.Modal(document.getElementById('modalPenilai')).show();
        });
    });

    // Hapus
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