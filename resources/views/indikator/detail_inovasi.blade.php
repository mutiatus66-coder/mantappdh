@extends('index', ['dummy' => true])

@push('styles')
    <link rel="stylesheet" href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}">
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
          rel="stylesheet"
          integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
          crossorigin="anonymous">
@endpush

@section('content')
<div class="page-container">

    @if(session('success'))
        <div class="alert alert-success-detail alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data Detail Inovasi</h3>
            <p class="sub-event-name">Sub Event: {{ $subEventName ?? 'N/A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('indikator.tahap1') }}" class="btn btn-dark">← Kembali</a>
            <button class="btn btn-primary" id="btnTambahIndikator">Tambah Indikator</button>
        </div>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Indikator: <span id="totalIndikator">{{ count($indikators ?? []) }}</span>
        </div>
    </div>

    <table id="tabelDetailInovasi" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Indikator</th>
                <th>Jenis</th>
                <th>Detail Indikator</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelDetailInovasiBody">
            @forelse($indikators ?? [] as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['nama_indikator'] ?? '-' }}</td>
                <td>
                    @if(($item['jenis'] ?? '') === 'makalah')
                        <span class="badge bg-info text-white">Makalah</span>
                    @else
                        <span class="badge bg-primary text-white">Substansi</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('indikator.tahap1.detail', [$subEventId, $item['id']]) }}"
                       class="btn btn-primary btn-aksi-wrap btn-sm" style="width: 75px; margin-left: 15%">
                        Detail
                    </a>
                </td>
                <td>
                    <div class="btn-aksi-wrap">
                        <button class="btn btn-warning btn-sm btn-edit-indikator"
                                data-id="{{ $item['id'] }}"
                                data-indikator="{{ $item['nama_indikator'] ?? '-' }}"
                                data-jenis="{{ $item['jenis'] ?? 'substansi' }}">
                            Ubah
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus-indikator"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['nama_indikator'] ?? '-' }}"
                                data-url="{{ route('indikator.tahap1.inovasi.destroy', [$subEventId, $item['id']]) }}">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="rv-empty">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data indikator
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>


{{-- ===== MODAL: Tambah / Ubah Indikator ===== --}}
<div class="modal fade" id="modalIndikator" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formIndikator" method="POST"
                  action="{{ route('indikator.tahap1.inovasi.store', $subEventId) }}">
                @csrf
                <input type="hidden" name="_method" id="formIndikatorMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalIndikatorTitle">Tambah Indikator Inovasi</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            id="btnTutupModalIndikator" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Sub Event</label>
                        <input type="text" class="form-control"
                               value="{{ $subEventName ?? '' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold required">Indikator</label>
                        <input type="text" name="nama_indikator" id="inputNamaIndikator"
                               class="form-control" placeholder="Masukkan nama indikator..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold required">Jenis</label>
                        <select name="jenis" id="selectJenis" class="form-select" required>
                            <option value="substansi">Substansi Inovasi</option>
                            <option value="makalah">Makalah</option>
                        </select>
                        <div class="form-text text-muted">
                            Sesuaikan dengan bobot formulasi yang sudah dikonfigurasi.
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark px-4" id="btnBatalIndikator">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ===== MODAL: Konfirmasi Hapus ===== --}}
<div class="modal fade" id="modalHapusIndikator" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3 hapus-icon-trash"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1 hapus-judul">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted hapus-desc">
                Tindakan ini tidak dapat dibatalkan. Indikator
                <strong id="namaIndikatorHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapusIndikator">Batal</button>
                <form id="formHapusIndikator" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-3">Hapus</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="{{ asset('assets/jquery/jquery-4.0.0.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"
        integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"
        integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zo9/GxcBPRKOEcESxaxufwRXqzq6n"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js"
        integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ"
        crossorigin="anonymous"></script>

<script>
(function () {
    'use strict';

    /* ══════════════════════════════════════════
       KONSTANTA
    ══════════════════════════════════════════ */
    const STORE_URL  = "{{ route('indikator.tahap1.inovasi.store', $subEventId) }}";
    const SUB_ID     = "{{ $subEventId }}";
    const tbody      = document.getElementById('tabelDetailInovasiBody');
    const totalSpan  = document.getElementById('totalIndikator');

    const modalIndikatorEl = document.getElementById('modalIndikator');
    const modalHapusEl     = document.getElementById('modalHapusIndikator');
    const modalIndikator   = new bootstrap.Modal(modalIndikatorEl);
    const modalHapus       = new bootstrap.Modal(modalHapusEl);

    let dt;

    /* ══════════════════════════════════════════
       INIT DATATABLES
    ══════════════════════════════════════════ */
    $(document).ready(function () {
        dt = $('#tabelDetailInovasi').DataTable({
            responsive: true,
            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : { first: '«', last: '»', next: '›', previous: '‹' },
            },
            layout: {
                topStart: ['pageLength', { buttons: ['colvis'] }],
                topEnd  : 'search',
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order     : [[0, 'asc']],
            columnDefs: [
                { targets: [0], searchable: false, width: '50px', className: 'dt-center' },
                { targets: [2], className: 'dt-center' },
                { targets: [3], orderable: false, searchable: false, className: 'dt-center', width: '140px' },
                { targets: [4], orderable: false, searchable: false, className: 'dt-center', width: '180px' },
            ],
        });

        dt.on('draw', updateTotal);
        updateTotal();
    });

    function updateTotal() {
        if (dt && totalSpan) totalSpan.textContent = dt.rows({ search: 'applied' }).count();
    }

    /* ══════════════════════════════════════════
       HELPER: RESET FORM MODAL INDIKATOR
    ══════════════════════════════════════════ */
    function resetModalIndikator() {
        document.getElementById('formIndikator').action            = STORE_URL;
        document.getElementById('formIndikatorMethod').value       = 'POST';
        document.getElementById('modalIndikatorTitle').textContent = 'Tambah Indikator Inovasi';
        document.getElementById('inputNamaIndikator').value        = '';
        document.getElementById('selectJenis').value               = 'substansi';
    }

    modalIndikatorEl.addEventListener('hidden.bs.modal', resetModalIndikator);

    /* ══════════════════════════════════════════
       MODAL: TAMBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnTambahIndikator').addEventListener('click', () => {
        resetModalIndikator();
        modalIndikator.show();
    });

    /* ══════════════════════════════════════════
       MODAL: TUTUP MANUAL
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupModalIndikator').addEventListener('click', () => modalIndikator.hide());
    document.getElementById('btnBatalIndikator').addEventListener('click',      () => modalIndikator.hide());
    document.getElementById('btnBatalHapusIndikator').addEventListener('click', () => modalHapus.hide());

    /* ══════════════════════════════════════════
       EVENT DELEGATION: UBAH & HAPUS
    ══════════════════════════════════════════ */
    tbody.addEventListener('click', function (e) {

        // ── Tombol Ubah ──
        const editBtn = e.target.closest('.btn-edit-indikator');
        if (editBtn) {
            document.getElementById('modalIndikatorTitle').textContent = 'Ubah Indikator';
            document.getElementById('formIndikator').action =
                `/indikator/tahap-1/${SUB_ID}/inovasi/${editBtn.dataset.id}`;
            document.getElementById('formIndikatorMethod').value = 'PUT';
            document.getElementById('inputNamaIndikator').value  = editBtn.dataset.indikator;
            document.getElementById('selectJenis').value         = editBtn.dataset.jenis || 'substansi';
            modalIndikator.show();
            return;
        }

        // ── Tombol Hapus ──
        const hapusBtn = e.target.closest('.btn-hapus-indikator');
        if (hapusBtn) {
            document.getElementById('namaIndikatorHapus').textContent = hapusBtn.dataset.nama;
            document.getElementById('formHapusIndikator').action      = hapusBtn.dataset.url;
            modalHapus.show();
        }
    });

})();
</script>
@endpush