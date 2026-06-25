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

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Master Penilai</h3>
            <p>Pilih sub event untuk mengatur penilai</p>
        </div>
    </div>

    {{-- Total --}}
    <div class="sub-event-stats">
        <div class="total-badge">
            Total Sub Event: <span id="totalSubEvent">{{ $subEvents->count() }}</span>
        </div>
    </div>

    {{--
        Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
        nggak perlu overflow-x wrapper karna DT mengelola scroll sendiri.
    --}}
    <table id="tabelPenilaiIndex" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Event</th>
                <th>Sub Event</th>
                <th>Jml Penilai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subEvents as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tahun }}</td>
                <td>{{ $item->event->nama_event ?? '-' }}</td>
                <td>{{ $item->sub_event }}</td>
                <td>{{ $item->penilai->count() }}</td>
                <td>
                    <a href="{{ route('penilai.detail', $item->id) }}"
                       class="btn btn-primary btn-sm">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada sub event
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/jquery/jquery-4.0.0.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js" integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ" crossorigin="anonymous"></script>

<script>
(function () {
    'use strict';

    const totalSpan = document.getElementById('totalSubEvent');
    let dt = null;

    /* ══════════════════════════════════════════
       Layout Datatables
    ══════════════════════════════════════════ */
    $(document).ready(function () {
        dt = $('#tabelPenilaiIndex').DataTable({
            responsive: true,

            language: {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : { first: '«', last: '»', next: '›', previous: '‹' },
                emptyTable  : 'Belum ada sub event.',
            },

            layout: {
                topStart: ['pageLength', { buttons: ['colvis'] }],
                topEnd  : 'search',
            },

            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order     : [[0, 'asc']],

            columnDefs: [
                {
                    targets   : [0],
                    searchable: false,
                    width     : '50px',
                    className : 'dt-center',
                },
                {
                    targets   : [1],
                    width     : '80px',
                    className : 'dt-center',
                },
                {
                    targets   : [4],
                    searchable: false,
                    width     : '100px',
                    className : 'dt-center',
                },
                {
                    targets   : [5],
                    orderable : false,
                    searchable: false,
                    width     : '100px',
                    className : 'dt-center',
                },
            ],
        });

        dt.on('draw', updateTotal);
        updateTotal();
    });

    function updateTotal() {
        if (!dt || !totalSpan) return;
        totalSpan.textContent = dt.rows({ search: 'applied' }).count();
    }

})();
</script>
@endpush