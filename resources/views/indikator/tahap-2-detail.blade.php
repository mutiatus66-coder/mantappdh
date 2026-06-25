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
             style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);color:#92400e;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Detail Indikator Tahap 2</h3>
            <p>Sub Event: <strong>{{ $subEvent->sub_event }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('indikator.tahap2') }}" class="btn btn-dark">← Kembali</a>
            <button class="btn btn-primary" id="btnTambahIndikator">Tambah Indikator</button>
        </div>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Keterangan: <span id="totalKeterangan">{{ $indikators->sum(fn($i) => $i->keterangans->count()) }}</span>
        </div>
    </div>

    {{--
        Kolom index 6 (jenis) disembunyikan via columnDefs visible:false.
        Digunakan oleh drawCallback untuk menyisipkan baris group header.
        Urutan kolom di HTML harus sama dengan urutan di columnDefs.
    --}}
    <table id="tabelTahap2Detail" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Indikator</th>
                <th>Keterangan</th>
                <th>Nilai Minimal</th>
                <th>Nilai Maksimal</th>
                <th>Aksi</th>
                <th>Jenis</th>{{-- Kolom tersembunyi untuk grouping --}}
            </tr>
        </thead>
        <tbody id="tabelTahap2DetailBody">
            @php $no = 1; @endphp
            @foreach(['Subtansi Inovasi', 'Peragaan'] as $jenis)
                @foreach($indikators->where('jenis', $jenis) as $ind)
                    @forelse($ind->keterangans as $ket)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $ind->nama_indikator }}</td>
                        <td>{{ $ket->keterangan }}</td>
                        <td>{{ $ket->nilai_minimal }}</td>
                        <td>{{ $ket->nilai_maksimal }}</td>
                        <td>
                            <div class="btn-aksi-wrap" style="display:flex;gap:6px;justify-content:center;">
                                <button class="btn btn-warning btn-sm btn-edit-indikator"
                                        data-id="{{ $ket->id }}"
                                        data-indikator-id="{{ $ind->id }}"
                                        data-nama-indikator="{{ $ind->nama_indikator }}"
                                        data-jenis="{{ $ind->jenis }}"
                                        data-keterangan="{{ $ket->keterangan }}"
                                        data-nilai-minimal="{{ $ket->nilai_minimal }}"
                                        data-nilai-maksimal="{{ $ket->nilai_maksimal }}">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm btn-hapus-indikator"
                                        data-id="{{ $ket->id }}"
                                        data-url="{{ route('indikator.tahap2.indikator.destroy', [$subEvent->id, $ket->id]) }}">
                                    Hapus
                                </button>
                            </div>
                        </td>
                        <td>{{ $jenis }}</td>{{-- Data grouping tersembunyi --}}
                    </tr>
                    @empty
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $ind->nama_indikator }}</td>
                        <td colspan="3" style="color:var(--ri-text-muted);font-style:italic;">
                            Belum ada keterangan
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit-indikator"
                                    data-id=""
                                    data-indikator-id="{{ $ind->id }}"
                                    data-nama-indikator="{{ $ind->nama_indikator }}"
                                    data-jenis="{{ $ind->jenis }}"
                                    data-keterangan=""
                                    data-nilai-minimal=""
                                    data-nilai-maksimal="">
                                Edit
                            </button>
                        </td>
                        <td>{{ $jenis }}</td>
                    </tr>
                    @endforelse
                @endforeach
            @endforeach
        </tbody>
    </table>

</div>


{{-- ===== MODAL: Tambah / Edit Indikator ===== --}}
<div class="modal fade" id="modalIndikator" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formIndikator" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" id="formIndikatorMethod" value="POST">
                <input type="hidden" name="keterangan_id" id="hiddenKetId" value="">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalIndikatorTitle">Tambah Indikator Nominator</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            id="btnTutupModalIndikator2" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Sub Event</label>
                        <input type="text" class="form-control"
                               value="{{ $subEvent->sub_event }}" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Indikator</label>
                        <input type="text" name="nama_indikator" id="inputNamaIndikator"
                               class="form-control" placeholder="Nama indikator..." required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Jenis Indikator</label>
                        <select name="jenis" id="inputJenis" class="form-select" required>
                            <option value="Subtansi Inovasi">Subtansi Inovasi</option>
                            <option value="Peragaan">Peragaan</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Keterangan</label>
                        <input type="text" name="keterangan" id="inputKeterangan"
                               class="form-control" placeholder="Keterangan..." required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-4">
                            <label class="form-label fw-semibold required">Nilai Minimal</label>
                            <input type="number" name="nilai_minimal" id="inputNilaiMinimal"
                                   class="form-control" placeholder="0" min="0" required>
                        </div>
                        <div class="col-6 mb-4">
                            <label class="form-label fw-semibold required">Nilai Maksimal</label>
                            <input type="number" name="nilai_maksimal" id="inputNilaiMaksimal"
                                   class="form-control" placeholder="0" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark px-4" id="btnBatalIndikator2">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ===== MODAL: Konfirmasi Hapus ===== --}}
<div class="modal fade" id="modalHapus" tabindex="-1"
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
                Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-sm px-3" id="btnBatalHapus2">Batal</button>
                <form id="formHapus" method="POST">
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
    const STORE_URL = "{{ route('indikator.tahap2.indikator.store', $subEvent->id) }}";
    const SUB_ID    = "{{ $subEvent->id }}";
    const tbody     = document.getElementById('tabelTahap2DetailBody');
    const totalSpan = document.getElementById('totalKeterangan');

    const modalIndikatorEl = document.getElementById('modalIndikator');
    const modalHapusEl     = document.getElementById('modalHapus');
    const modalIndikator   = new bootstrap.Modal(modalIndikatorEl);
    const modalHapus       = new bootstrap.Modal(modalHapusEl);

    let dt;

    /* ══════════════════════════════════════════
       INIT DATATABLES
       Kolom index 6 (Jenis) disembunyikan, tapi
       tetap ada di data — dipakai drawCallback
       untuk menyisipkan baris group header
    ══════════════════════════════════════════ */
    $(document).ready(function () {
        dt = $('#tabelTahap2Detail').DataTable({
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
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],

            // Urutkan berdasarkan kolom Jenis (index 6) agar group terkumpul,
            // lalu dalam grup urut berdasarkan No (index 0)
            order: [[6, 'asc'], [0, 'asc']],

            columnDefs: [
                { targets: [0], searchable: false, width: '50px', className: 'dt-center' },
                { targets: [3, 4], className: 'dt-center', width: '130px' },
                { targets: [5], orderable: false, searchable: false, className: 'dt-center', width: '180px' },
                // Kolom Jenis: sembunyikan dari tampilan, tetap bisa di-sort & search
                { targets: [6], visible: false },
            ],

            /* ══════════════════════════════════════════
               drawCallback: Sisipkan baris group header
               setiap kali DT menggambar ulang tabel.
               Strategi: scan semua baris yang tampil,
               deteksi pergantian nilai Jenis, lalu
               sisipkan <tr class="group-header"> sebelum
               baris pertama grup tersebut.
            ══════════════════════════════════════════ */
            drawCallback: function () {
                const api      = this.api();
                const rows     = api.rows({ page: 'current' }).nodes();
                let lastJenis  = null;

                api.rows({ page: 'current' }).every(function () {
                    const jenis = this.data()[6]; // Kolom tersembunyi index 6
                    const node  = this.node();

                    if (jenis !== lastJenis) {
                        // Sisipkan baris header grup sebelum baris ini
                        $(node).before(
                            `<tr class="group-header">
                                <td colspan="6">Indikator ${jenis}</td>
                            </tr>`
                        );
                        lastJenis = jenis;
                    }
                });

                updateTotal();
            },
        });
    });

    function updateTotal() {
        if (dt && totalSpan) totalSpan.textContent = dt.rows({ search: 'applied' }).count();
    }

    /* ══════════════════════════════════════════
       HELPER: RESET FORM MODAL
    ══════════════════════════════════════════ */
    function resetModalIndikator() {
        document.getElementById('formIndikator').action          = STORE_URL;
        document.getElementById('formIndikatorMethod').value     = 'POST';
        document.getElementById('modalIndikatorTitle').textContent = 'Tambah Indikator Nominator';
        document.getElementById('inputNamaIndikator').value      = '';
        document.getElementById('inputJenis').value              = 'Subtansi Inovasi';
        document.getElementById('inputKeterangan').value         = '';
        document.getElementById('inputNilaiMinimal').value       = '';
        document.getElementById('inputNilaiMaksimal').value      = '';
        document.getElementById('hiddenKetId').value             = '';
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
    document.getElementById('btnTutupModalIndikator2').addEventListener('click', () => modalIndikator.hide());
    document.getElementById('btnBatalIndikator2').addEventListener('click',      () => modalIndikator.hide());
    document.getElementById('btnBatalHapus2').addEventListener('click',          () => modalHapus.hide());

    /* ══════════════════════════════════════════
       EVENT DELEGATION: EDIT & HAPUS
    ══════════════════════════════════════════ */
    tbody.addEventListener('click', function (e) {

        // ── Tombol Edit ──
        const editBtn = e.target.closest('.btn-edit-indikator');
        if (editBtn) {
            const ketId = editBtn.dataset.id;

            document.getElementById('modalIndikatorTitle').textContent = 'Edit Indikator Nominator';
            document.getElementById('formIndikatorMethod').value       = 'PUT';
            document.getElementById('hiddenKetId').value               = ketId;
            document.getElementById('formIndikator').action            =
                `/indikator/tahap-2/${SUB_ID}/indikator/${ketId}`;

            document.getElementById('inputNamaIndikator').value = editBtn.dataset.namaIndikator;
            document.getElementById('inputJenis').value         = editBtn.dataset.jenis;
            document.getElementById('inputKeterangan').value    = editBtn.dataset.keterangan;
            document.getElementById('inputNilaiMinimal').value  = editBtn.dataset.nilaiMinimal;
            document.getElementById('inputNilaiMaksimal').value = editBtn.dataset.nilaiMaksimal;

            modalIndikator.show();
            return;
        }

        // ── Tombol Hapus ──
        const hapusBtn = e.target.closest('.btn-hapus-indikator');
        if (hapusBtn) {
            document.getElementById('formHapus').action = hapusBtn.dataset.url;
            modalHapus.show();
        }
    });

})();
</script>
@endpush