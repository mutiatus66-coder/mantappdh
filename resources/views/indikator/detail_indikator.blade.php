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
        <div class="alert alert-dismissible fade show mb-4" role="alert"
             style="background:rgba(37,99,235,0.08);border:1px solid rgba(37,99,235,0.25);color:#1e40af;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Detail Keterangan Indikator</h3>
            <p style="color:#2563eb;font-weight:700;">Indikator: {{ $indikatorName ?? 'N/A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('indikator.tahap1.inovasi', $subEventId) }}" class="btn btn-dark">
                ← Kembali
            </a>
            <button class="btn btn-primary" id="btnTambahKeterangan">Tambah Keterangan</button>
        </div>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Keterangan: <span id="totalKeterangan">{{ count($keterangans ?? []) }}</span>
        </div>
    </div>

    <table id="tabelKeterangan" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Keterangan</th>
                <th>Nilai Minimal</th>
                <th>Nilai Maksimal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelKeteranganBody">
            @forelse($keterangans as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['keterangan'] }}</td>
                <td>{{ $item['nilai_minimal'] }}</td>
                <td>{{ $item['nilai_maksimal'] }}</td>
                <td>
                    <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
                        <button class="btn btn-warning btn-sm btn-edit-keterangan"
                                data-id="{{ $item['id'] }}"
                                data-keterangan="{{ $item['keterangan'] }}"
                                data-nilai-minimal="{{ $item['nilai_minimal'] }}"
                                data-nilai-maksimal="{{ $item['nilai_maksimal'] }}">
                            Edit
                        </button>
                        <button class="btn btn-danger btn-sm btn-hapus-keterangan"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['keterangan'] }}"
                                data-url="{{ route('indikator.tahap1.detail.destroy', [$subEventId, $indikatorId, $item['id']]) }}">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data keterangan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>


{{-- ===== MODAL: Tambah / Ubah Keterangan ===== --}}
<div class="modal fade" id="modalKeterangan" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formKeterangan" method="POST"
                  action="{{ route('indikator.tahap1.detail.store', [$subEventId, $indikatorId]) }}">
                @csrf
                <input type="hidden" name="_method" id="formKeteranganMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalKeteranganTitle">Tambah Keterangan</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            id="btnTutupModalKeterangan" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Keterangan</label>
                        <input type="text" name="keterangan" id="inputKeterangan"
                               class="form-control" placeholder="Masukkan keterangan..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold required">Nilai Minimal</label>
                            <input type="number" name="nilai_minimal" id="inputNilaiMinimal"
                                   class="form-control" placeholder="0" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold required">Nilai Maksimal</label>
                            <input type="number" name="nilai_maksimal" id="inputNilaiMaksimal"
                                   class="form-control" placeholder="0" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark px-4" id="btnBatalKeterangan">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ===== MODAL: Konfirmasi Hapus ===== --}}
<div class="modal fade" id="modalHapusKeterangan" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem;color:#dc2626;"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem;line-height:1.6;">
                Tindakan ini tidak dapat dibatalkan. Keterangan
                <strong id="namaKeteranganHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapusKeterangan">Batal</button>
                <form id="formHapusKeterangan" method="POST">
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
        integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n"
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
    const STORE_URL   = "{{ route('indikator.tahap1.detail.store', [$subEventId, $indikatorId]) }}";
    const SUB_ID      = "{{ $subEventId }}";
    const INDIKATOR_ID = "{{ $indikatorId }}";
    const tbody       = document.getElementById('tabelKeteranganBody');
    const totalSpan   = document.getElementById('totalKeterangan');

    const modalKeteranganEl = document.getElementById('modalKeterangan');
    const modalHapusEl      = document.getElementById('modalHapusKeterangan');
    const modalKeterangan   = new bootstrap.Modal(modalKeteranganEl);
    const modalHapus        = new bootstrap.Modal(modalHapusEl);

    let dt;

    /* ══════════════════════════════════════════
       INIT DATATABLES
    ══════════════════════════════════════════ */
    $(document).ready(function () {
        dt = $('#tabelKeterangan').DataTable({
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
                { targets: [2, 3], className: 'dt-center', width: '130px' },
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
       HELPER: RESET FORM MODAL
    ══════════════════════════════════════════ */
    function resetModalKeterangan() {
        document.getElementById('formKeterangan').action          = STORE_URL;
        document.getElementById('formKeteranganMethod').value     = 'POST';
        document.getElementById('modalKeteranganTitle').textContent = 'Tambah Keterangan';
        document.getElementById('inputKeterangan').value          = '';
        document.getElementById('inputNilaiMinimal').value        = '';
        document.getElementById('inputNilaiMaksimal').value       = '';
    }

    modalKeteranganEl.addEventListener('hidden.bs.modal', resetModalKeterangan);

    /* ══════════════════════════════════════════
       MODAL: TAMBAH
    ══════════════════════════════════════════ */
    document.getElementById('btnTambahKeterangan').addEventListener('click', () => {
        resetModalKeterangan();
        modalKeterangan.show();
    });

    /* ══════════════════════════════════════════
       MODAL: TUTUP MANUAL
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupModalKeterangan').addEventListener('click', () => modalKeterangan.hide());
    document.getElementById('btnBatalKeterangan').addEventListener('click',      () => modalKeterangan.hide());
    document.getElementById('btnBatalHapusKeterangan').addEventListener('click', () => modalHapus.hide());

    /* ══════════════════════════════════════════
       EVENT DELEGATION: UBAH & HAPUS
    ══════════════════════════════════════════ */
    tbody.addEventListener('click', function (e) {

        // ── Tombol Edit ──
        const editBtn = e.target.closest('.btn-edit-keterangan');
        if (editBtn) {
            document.getElementById('modalKeteranganTitle').textContent = 'Ubah Keterangan';
            document.getElementById('formKeterangan').action =
                `/indikator/tahap-1/${SUB_ID}/detail/${INDIKATOR_ID}/${editBtn.dataset.id}`;
            document.getElementById('formKeteranganMethod').value  = 'PUT';
            document.getElementById('inputKeterangan').value       = editBtn.dataset.keterangan;
            document.getElementById('inputNilaiMinimal').value     = editBtn.dataset.nilaiMinimal;
            document.getElementById('inputNilaiMaksimal').value    = editBtn.dataset.nilaiMaksimal;
            modalKeterangan.show();
            return;
        }

        // ── Tombol Hapus ──
        const hapusBtn = e.target.closest('.btn-hapus-keterangan');
        if (hapusBtn) {
            document.getElementById('namaKeteranganHapus').textContent = hapusBtn.dataset.nama;
            document.getElementById('formHapusKeterangan').action      = hapusBtn.dataset.url;
            modalHapus.show();
        }
    });

})();
</script>
@endpush