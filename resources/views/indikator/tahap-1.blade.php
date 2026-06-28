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

    {{-- Alert session --}}
    @if(session('success'))
        <div class="alert alert-dismissible fade show mb-4" role="alert"
             style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);color:#92400e;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->has('total'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('total') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Setting Indikator Penilaian Tahap 1 &amp; Formulasi Nilai</h3>
            <p>Kelola indikator dan formulasi nilai untuk penilaian tahap 1</p>
        </div>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Sub Event: <span id="totalSubEvent">{{ count($subEvents ?? []) }}</span>
        </div>
    </div>

    <table id="tabelTahap1" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Sub Event</th>
                <th>Detail Indikator</th>
                <th>Detail Formulasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subEvents ?? [] as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['sub_event'] }}</td>
                <td>
                    @if($detailValid1[$item['id']] ?? false)
                        <a href="{{ route('indikator.tahap1.inovasi', $item['id']) }}"
                           class="btn-detail-indikator btn-primary">
                            <i></i> Detail
                        </a>
                    @else
                        <button class="btn-detail-indikator btn-info"
                                style="background:#9ca3af;cursor:not-allowed;opacity:0.7;"
                                title="Isi formulasi hingga 100% terlebih dahulu" disabled>
                            <i></i> Detail
                        </button>
                    @endif
                </td>
                <td>
                    @if(in_array($item['id'], $formulasis1 ?? []))
                        <button class="btn-detail-formulasi btn-open-formulasi1"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['sub_event'] }}">
                            <i></i> Detail
                        </button>
                    @else
                        <button class="btn-tambah-formulasi btn-open-formulasi1"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['sub_event'] }}">
                            <i></i> Tambah Formulasi
                        </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data sub event
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>


{{-- ===== MODAL: Tambah / Edit Formulasi Nilai Tahap 1 ===== --}}
<div class="modal fade" id="modalFormulasi1" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formFormulasi1" method="POST" action="">
                @csrf

                <div class="modal-header px-5 py-4" style="border-bottom:1px solid #e5e7eb;">
                    <h5 class="modal-title fw-semibold" id="modalFormulasi1Title">Tambah Formulasi Nilai</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            id="btnTutupFormulasi1" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Sub Event</label>
                        <div class="input-icon-group">
                            <input type="text" id="inputSubEventName1" class="form-control" disabled>
                            <span class="icon-badge" style="background:#2563eb;">
                                <i class="bi bi-info-lg"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Nilai Makalah</label>
                        <div class="input-icon-group">
                            <input type="number" name="nilai_makalah" id="inputNilaiMakalah"
                                   class="form-control" placeholder="0" min="1" max="100"
                                   required oninput="hitungTotal1()">
                            <span class="icon-badge" style="background:#dedede;">%</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Nilai Substansi Inovasi</label>
                        <div class="input-icon-group">
                            <input type="number" name="nilai_substansi" id="inputNilaiSubstansi"
                                   class="form-control" placeholder="0" min="1" max="100"
                                   required oninput="hitungTotal1()">
                            <span class="icon-badge" style="background:#dedede;">%</span>
                        </div>
                    </div>

                    <div id="totalPreview1" class="total-preview" style="display:none;">
                        Total: <span id="totalAngka1">0</span>%
                        <span id="totalStatus1"></span>
                    </div>

                    <p class="mt-3 mb-0" style="font-size:0.82rem;color:#dc2626;font-weight:500;">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Catatan: Nilai makalah dan nilai substansi jika ditotal harus menjadi 100%.
                    </p>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark" id="btnBatalFormulasi1">Batal</button>
                    <button type="submit" id="btnSimpan1" class="btn btn-success px-4" disabled>Simpan</button>
                </div>
            </form>
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
       DATA FORMULASI — di-pass dari Blade ke JS
       Digunakan untuk menentukan apakah fetch
       data diperlukan saat modal dibuka
    ══════════════════════════════════════════ */
    const formulasiIds = @json($formulasis1 ?? []);

    /* ══════════════════════════════════════════
       STATE MODAL
    ══════════════════════════════════════════ */
    let isSubmitting = false;

    const modalEl       = document.getElementById('modalFormulasi1');
    const modalFormulasi = new bootstrap.Modal(modalEl);

    /* ══════════════════════════════════════════
       INIT DATATABLES
    ══════════════════════════════════════════ */
    let dt;
    $(document).ready(function () {
        dt = $('#tabelTahap1').DataTable({
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
                // Kolom Detail Indikator & Formulasi: tidak sortable, tidak di-search, center
                { targets: [2, 3], orderable: false, searchable: false, className: 'dt-center', width: '180px' },
            ],
        });

        dt.on('draw', updateTotal);
        updateTotal();
    });

    /* ══════════════════════════════════════════
       HELPER: UPDATE COUNTER
    ══════════════════════════════════════════ */
    function updateTotal() {
        const span = document.getElementById('totalSubEvent');
        if (dt && span) span.textContent = dt.rows({ search: 'applied' }).count();
    }

    /* ══════════════════════════════════════════
       HITUNG TOTAL REALTIME
       Aktifkan tombol Simpan hanya jika = 100%
    ══════════════════════════════════════════ */
    window.hitungTotal1 = function () {
        const makalah   = parseInt(document.getElementById('inputNilaiMakalah').value)   || 0;
        const substansi = parseInt(document.getElementById('inputNilaiSubstansi').value) || 0;
        const total     = makalah + substansi;
        const preview   = document.getElementById('totalPreview1');
        const angka     = document.getElementById('totalAngka1');
        const status    = document.getElementById('totalStatus1');
        const btnSimpan = document.getElementById('btnSimpan1');

        preview.style.display = 'block';
        angka.textContent     = total;

        if (total === 100) {
            preview.className      = 'total-preview total-ok';
            status.textContent     = ' ✓ Valid';
            btnSimpan.disabled     = false;
        } else {
            preview.className  = 'total-preview total-warn';
            status.textContent = total < 100
                ? ` (kurang ${100 - total}%)`
                : ` (lebih ${total - 100}%)`;
            btnSimpan.disabled = true;
        }
    };

    /* ══════════════════════════════════════════
       HELPER: RESET FORM MODAL
    ══════════════════════════════════════════ */
    function resetFormulasi1() {
        document.getElementById('formFormulasi1').action         = '';
        document.getElementById('modalFormulasi1Title').textContent = 'Tambah Formulasi Nilai';
        document.getElementById('inputNilaiMakalah').value       = '';
        document.getElementById('inputNilaiSubstansi').value     = '';
        document.getElementById('totalPreview1').style.display   = 'none';
        document.getElementById('btnSimpan1').disabled           = true;
    }

    /* ══════════════════════════════════════════
       RESET SAAT MODAL DITUTUP
    ══════════════════════════════════════════ */
    modalEl.addEventListener('hidden.bs.modal', resetFormulasi1);

    /* ══════════════════════════════════════════
       TUTUP MANUAL — block dismiss saat submit
    ══════════════════════════════════════════ */
    document.getElementById('btnTutupFormulasi1').addEventListener('click', () => {
        if (!isSubmitting) modalFormulasi.hide();
    });
    document.getElementById('btnBatalFormulasi1').addEventListener('click', () => {
        if (!isSubmitting) modalFormulasi.hide();
    });

    /* ══════════════════════════════════════════
       EVENT DELEGATION: BUKA MODAL FORMULASI
       Tangkap klik tombol di dalam tabel DT
       (termasuk baris di halaman lain pagination)
    ══════════════════════════════════════════ */
    document.querySelector('#tabelTahap1 tbody').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-open-formulasi1');
        if (!btn) return;

        const subEventId   = btn.dataset.id;
        const subEventNama = btn.dataset.nama;

        // Judul modal bergantung pada mode tambah/detail
        document.getElementById('modalFormulasi1Title').textContent =
            btn.classList.contains('btn-tambah-formulasi')
                ? 'Tambah Formulasi Nilai'
                : 'Edit Formulasi Nilai';

        document.getElementById('inputSubEventName1').value = subEventNama;
        document.getElementById('formFormulasi1').action    =
            `/indikator/tahap-1/${subEventId}/formulasi`;

        // Reset field sebelum isi
        document.getElementById('inputNilaiMakalah').value     = '';
        document.getElementById('inputNilaiSubstansi').value   = '';
        document.getElementById('totalPreview1').style.display = 'none';
        document.getElementById('btnSimpan1').disabled         = true;

        // Jika sudah ada formulasi, fetch data lalu isi field
        if (formulasiIds.includes(parseInt(subEventId))) {
            fetch(`/indikator/tahap-1/${subEventId}/formulasi/get`)
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(data => {
                    document.getElementById('inputNilaiMakalah').value   = data.nilai_makalah;
                    document.getElementById('inputNilaiSubstansi').value = data.nilai_substansi;
                    hitungTotal1();
                })
                .catch(err => {
                    console.error('Gagal memuat data formulasi tahap 1:', err);
                    alert('Gagal memuat data formulasi. Silakan coba lagi.');
                });
        }

        modalFormulasi.show();
    });

})();
</script>
@endpush