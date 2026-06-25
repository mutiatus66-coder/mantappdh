@extends('index', ['dummy' => true])

@push('styles')
    <link rel="stylesheet" href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}">
    {{--
        PENTING: Jangan pakai datatables.css lokal karena versi nya lama (1.x)
        nanti tampilan DT v2.x + ColumnControl berantakan.
        pake CDN aja yh —Regan.
    --}}
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
          rel="stylesheet"
          integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
          crossorigin="anonymous">
@endpush

@section('content')
<div class="page-container">

    {{-- ── HEADER ── --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="ec-title">Rekap Nilai Pendaftar</h3>
            <p class="ec-subtitle">{{ $subEventNama ?? '' }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ url('/inovasi/rekap-nilai') }}" class="btn btn-dark">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <button class="btn btn-danger" id="pdfBtn">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
            </button>
            <button class="btn btn-success" id="excelBtn">
                <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
            </button>
        </div>
    </div>

    {{--
        Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
        nggak perlu overflow-x wrapper karna DT mengelola scroll sendiri.
    --}}
    <table id="tabelRekap" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Inovasi</th>
                <th>Instansi / Organisasi</th>
                <th>Link Youtube</th>
                <th>No Handphone</th>
                <th>Kategori</th>
                <th>Nilai T1</th>
                <th>Nilai T2</th>
                <th>Nilai Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usulan as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['judul'] ?? '-' }}</td>
                <td>{{ $item['instansi'] ?? '-' }}</td>
                <td>
                    @if(!empty($item['link_youtube']))
                        <a href="{{ $item['link_youtube'] }}" target="_blank" class="link-primary">
                            <i class="bi bi-youtube me-1"></i>Lihat
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item['no_hp'] ?? '-' }}</td>
                <td><span class="badge-kategori">{{ $item['kategori'] ?? '-' }}</span></td>
                <td>{{ $item['nilai_t1'] ?? '-' }}</td>
                <td>{{ $item['nilai_t2'] ?? '-' }}</td>
                <td><strong>{{ $item['nilai_total'] ?? '-' }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada data pendaftar untuk sub event ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection

@push('scripts')
{{-- JANGAN load DT core JS lokal karena kemungkinan versi lawas --}}
<script src="{{ asset('assets/jquery/jquery-4.0.0.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js"
        integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ"
        crossorigin="anonymous"></script>
<script>
(function () {
    'use strict';

    /* ══════════════════════════════════════════
       DATATABLE REKAP PENDAFTAR
    ══════════════════════════════════════════ */
    let dt = null;

    $(document).ready(function () {
        dt = $('#tabelRekap').DataTable({
            responsive: true,
            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : {
                    first    : '«',
                    last     : '»',
                    next     : '›',
                    previous : '‹'
                }
            },
            layout: {
                topStart: [
                    'pageLength',
                    {
                        buttons: [
                            'colvis',
                            'excelHtml5',
                            'pdfHtml5',
                            'print'
                        ]
                    }
                ],
                topEnd: 'search'
            },
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: [0],
                    searchable: false,
                    width: '50px',
                    className: 'dt-center'
                },
                {
                    targets: [3],
                    orderable: false,
                    className: 'dt-center'
                },
                {
                    targets: [5, 6, 7, 8],
                    className: 'dt-center'
                }
            ]
        });
    });

    /* ══════════════════════════════════════════
       EXPORT BUTTON CUSTOM — trigger DT internal
    ══════════════════════════════════════════ */
    document.getElementById('pdfBtn')?.addEventListener('click', function () {
        $('.buttons-pdf').click();
    });
    document.getElementById('excelBtn')?.addEventListener('click', function () {
        $('.buttons-excel').click();
    });

})();
</script>
@endpush