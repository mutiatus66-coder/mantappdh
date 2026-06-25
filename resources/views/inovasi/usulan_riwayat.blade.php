@extends('index', ['dummy' => true])

@push('styles')
    <link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
    {{--
        PENTING: Jangan pakai datatables.css lokal karena versi nya lama (1.x)
        pake CDN aja yh —Regan.
    --}}
    <link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
          rel="stylesheet"
          integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
          crossorigin="anonymous">
@endpush

@section('content')
<div class="page-container">

    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="ec-title">Riwayat Usulan</h3>
            <p class="ec-subtitle">
                {{ $eventNama ?? '' }}
                @if(!empty($eventNama) && !empty($subEventNama)) &mdash; @endif
                {{ $subEventNama ?? '' }}
            </p>
        </div>
        <a href="{{ url('/inovasi/riwayat') }}" class="btn btn-dark">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{--
        Tabel: class "display" = stylesheet DT default (stripe + hover + order-column).
        nggak perlu overflow-x wrapper karna DT mengelola scroll sendiri.
        Search manual JS dihapus — DT sudah handle via topEnd: 'search'.
    --}}
    <table id="tabelRiwayat" class="display nowrap compact" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Status</th>
                <th>Nama Inovasi</th>
                <th>Inovator / Instansi</th>
                <th>Ketua</th>
                <th>Bidang</th>
                <th>Kategori</th>
                <th>Dokumen</th>
                <th>Terkirim</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usulan as $u)
            @php
                $color = match($u->status) {
                    'Melengkapi Data' => 'warning',
                    'Sedang Dinilai'  => 'primary',
                    default           => 'success',
                };
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}
                                 border border-{{ $color }} border-opacity-25"
                          style="font-size:.74rem;padding:2px 10px;border-radius:20px">
                        {{ $u->status }}
                    </span>
                </td>
                <td>
                    <div class="fw-semibold" style="color:var(--ri-text-primary)">{{ $u->nama_inovasi ?? '-' }}</div>
                    <small class="text-muted">{{ $u->judul ?? '' }}</small>
                </td>
                <td>{{ $u->inovator ?? '-' }}</td>
                <td>
                    <div>{{ $u->ketua_nama ?? '-' }}</div>
                    <small class="text-muted">{{ $u->ketua_email ?? '' }}</small>
                </td>
                <td>{{ $u->bidang->nama ?? '-' }}</td>
                <td><span class="badge-kategori">{{ ucfirst($u->kategori ?? '-') }}</span></td>
                <td>
                    <div class="d-flex flex-column gap-1" style="font-size:.76rem">
                        @if($u->file_surat_pernyataan)
                        <a href="{{ asset('storage/'.$u->file_surat_pernyataan) }}" target="_blank"
                           class="text-decoration-none" style="color:#1b84ff">
                            <i class="bi bi-file-earmark-text me-1"></i>Surat
                        </a>
                        @endif
                        @if($u->file_proposal)
                        <a href="{{ asset('storage/'.$u->file_proposal) }}" target="_blank"
                           class="text-decoration-none" style="color:#1b84ff">
                            <i class="bi bi-file-earmark-richtext me-1"></i>Proposal
                        </a>
                        @endif
                        @if($u->file_gambar)
                        <a href="{{ asset('storage/'.$u->file_gambar) }}" target="_blank"
                           class="text-decoration-none" style="color:#1b84ff">
                            <i class="bi bi-image me-1"></i>Gambar
                        </a>
                        @endif
                        @if($u->link_video)
                        <a href="{{ $u->link_video }}" target="_blank"
                           class="text-decoration-none text-danger">
                            <i class="bi bi-youtube me-1"></i>Video
                        </a>
                        @endif
                        @if(!$u->file_surat_pernyataan && !$u->file_proposal && !$u->file_gambar && !$u->link_video)
                        <span class="text-muted">—</span>
                        @endif
                    </div>
                </td>
                <td>
                    @if($u->is_submitted)
                        <i class="bi bi-check-circle-fill text-success"></i>
                    @else
                        <span class="text-muted">Belum</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:32px;color:#888;">
                    <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    Belum ada usulan untuk sub event ini
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
       DATATABLE RIWAYAT USULAN
    ══════════════════════════════════════════ */
    $(document).ready(function () {
        $('#tabelRiwayat').DataTable({
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
                    { buttons: ['colvis'] }
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
                    targets: [1],
                    className: 'dt-center'
                },
                {
                    // Kolom Dokumen — tidak perlu di-sort/search karena berisi link
                    targets: [7],
                    orderable: false,
                    searchable: false,
                    className: 'dt-center'
                },
                {
                    targets: [8],
                    className: 'dt-center'
                }
            ]
        });
    });

})();
</script>
@endpush