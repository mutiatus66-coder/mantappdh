@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data Event</h3>
            <p>Kelola semua event yang tersedia</p>
        </div>
        <button class="btn btn-primary" id="btnTambahEvent">
            Tambah Event
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
            Total Event: <span id="totalEvent">{{ $events->count() }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchEvent" placeholder="Cari event...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama Event</th>
                    <th style="text-align:center;">Jenis</th>
                    <th width="220" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelEventBody">
                @forelse($events as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_event }}</td>
                    <td style="text-align:center;"><span class="badge-kategori">{{ $item->jenis ?? '-' }}</span></td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-edit-event btn-aksi"
                                    data-id="{{ $item->id }}"
                                    data-nama-event="{{ $item->nama_event }}"
                                    data-jenis="{{ $item->jenis }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-hapus-event btn-aksi"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_event }}"
                                    data-url="{{ route('event.destroy', $item->id) }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data event
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Ubah Event ══ --}}
<div class="modal fade" id="modalEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formEvent" method="POST" action="{{ route('event.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formEventMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalEventTitle">Tambah Event</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Nama Event</label>
                            <input type="text" name="nama_event" id="inputNamaEvent"
                                   class="form-control" placeholder="Masukkan nama event..." required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Jenis</label>
                            <select name="jenis" id="inputJenis" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Jenis --</option>
                                <option value="INOTEK">INOTEK</option>
                                <option value="INODA">INODA</option>
                            </select>
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


{{-- ══ MODAL — Konfirmasi Hapus Event ══ --}}
<div class="modal fade" id="modalHapusEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Data event
                <strong id="namaEventHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-aksi px-3" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusEvent" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-aksi px-3">
                        Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl    = "{{ route('event.store') }}";
    const searchInput = document.getElementById('searchEvent');
    const rows        = document.querySelectorAll('#tabelEventBody tr');
    const totalSpan   = document.getElementById('totalEvent');

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
    document.getElementById('modalEvent').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formEvent').action        = storeUrl;
        document.getElementById('formEventMethod').value   = 'POST';
        document.getElementById('modalEventTitle').textContent = 'Tambah Event';
        document.getElementById('inputNamaEvent').value    = '';
        document.getElementById('inputJenis').value        = '';
    });

    // Tambah
    document.getElementById('btnTambahEvent').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalEvent')).show();
    });

    // Edit
    document.querySelectorAll('.btn-edit-event').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalEventTitle').textContent  = 'Ubah Event';
            document.getElementById('formEvent').action             = `/event/${this.dataset.id}`;
            document.getElementById('formEventMethod').value        = 'PUT';
            document.getElementById('inputNamaEvent').value         = this.dataset.namaEvent;
            document.getElementById('inputJenis').value             = this.dataset.jenis;
            new bootstrap.Modal(document.getElementById('modalEvent')).show();
        });
    });

    // Hapus
    document.querySelectorAll('.btn-hapus-event').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaEventHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusEvent').action      = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusEvent')).show();
        });
    });

});
</script>
@endpush