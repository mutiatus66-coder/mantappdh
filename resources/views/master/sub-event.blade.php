@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/setel.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data Sub Event</h3>
            <p>Kelola semua sub event yang tersedia</p>
        </div>
        <button class="btn btn-primary" id="btnTambahSubEvent">
            Tambah Sub Event
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
            Total Sub Event: <span id="totalSubEvent">{{ count($subEvents ?? []) }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchSubEvent" placeholder="Cari sub event...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Tahun</th>
                    <th>Event</th>
                    <th>Sub Event</th>
                    <th>Kategori</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Berakhir</th>
                    <th width="180" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelSubEventBody">
                @forelse($subEvents as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['tahun'] ?? '-' }}</td>
                    <td>{{ $item['event'] ?? '-' }}</td>
                    <td>{{ $item['sub_event'] }}</td>
                    <td><span class="badge-kategori">{{ $item['kategori'] ?? '-' }}</span></td>
                    <td>{{ $item['mulai'] ?? '-' }}</td>
                    <td>{{ $item['berakhir'] ?? '-' }}</td>
                    <td style="text-align:center;">
                        <button class="btn btn-warning btn-edit-se me-2"
                                data-id="{{ $item['id'] }}"
                                data-tahun="{{ $item['tahun'] ?? '' }}"
                                data-event-id="{{ $item['event_id'] ?? '' }}"
                                data-sub-event="{{ $item['sub_event'] }}"
                                data-kategori="{{ $item['kategori'] ?? '' }}"
                                data-mulai="{{ $item['mulai'] ?? '' }}"
                                data-berakhir="{{ $item['berakhir'] ?? '' }}">
                            Ubah
                        </button>
                        <button class="btn btn-danger btn-hapus-se"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['sub_event'] }}"
                                data-url="{{ route('sub-event.destroy', $item['id']) }}">
                            Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data sub event
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Ubah Sub Event ══ --}}
<div class="modal fade" id="modalSubEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formSubEvent" method="POST" action="{{ route('sub-event.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formSEMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalSETitle">Tambah Sub Event</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Tahun</label>
                            <input type="number" name="tahun" id="seTahun" class="form-control" placeholder="cth. 2025" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Event</label>
                            <select name="event_id" id="seEvent" class="form-select" required>
                                <option value="">-- Pilih Event --</option>
                                @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Sub Event</label>
                            <input type="text" name="sub_event" id="seSubEvent" class="form-control" placeholder="Nama sub event" required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Kategori</label>
                            <input type="text" name="kategori" id="seKategori" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Tanggal Mulai</label>
                            <input type="date" name="mulai" id="seMulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Tanggal Berakhir</label>
                            <input type="date" name="berakhir" id="seBerakhir" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Sub Event ══ --}}
<div class="modal fade" id="modalHapusSE" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:#a32d2d;"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Sub event
                <strong id="namaSEHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusSE" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4">Hapus</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl    = "{{ route('sub-event.store') }}";
    const searchInput = document.getElementById('searchSubEvent');
    const rows        = document.querySelectorAll('#tabelSubEventBody tr');
    const totalSpan   = document.getElementById('totalSubEvent');

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
    document.getElementById('modalSubEvent').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formSubEvent').action    = storeUrl;
        document.getElementById('formSEMethod').value     = 'POST';
        document.getElementById('modalSETitle').textContent = 'Tambah Sub Event';
        ['seTahun', 'seSubEvent', 'seKategori', 'seMulai', 'seBerakhir'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('seEvent').value = '';
    });

    // Tambah
    document.getElementById('btnTambahSubEvent').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalSubEvent')).show();
    });

    // Edit
    document.querySelectorAll('.btn-edit-se').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalSETitle').textContent  = 'Ubah Sub Event';
            document.getElementById('formSubEvent').action       = `/sub-event/${this.dataset.id}`;
            document.getElementById('formSEMethod').value        = 'PUT';
            document.getElementById('seTahun').value             = this.dataset.tahun;
            document.getElementById('seSubEvent').value          = this.dataset.subEvent;
            document.getElementById('seKategori').value          = this.dataset.kategori;
            document.getElementById('seMulai').value             = this.dataset.mulai;
            document.getElementById('seBerakhir').value          = this.dataset.berakhir;
            document.getElementById('seEvent').value             = this.dataset.eventId;
            new bootstrap.Modal(document.getElementById('modalSubEvent')).show();
        });
    });

    // Hapus
    document.querySelectorAll('.btn-hapus-se').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaSEHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusSE').action      = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusSE')).show();
        });
    });

});
</script>
@endpush