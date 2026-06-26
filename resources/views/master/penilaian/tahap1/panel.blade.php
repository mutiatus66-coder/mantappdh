{{-- resources/views/master/penilaian/tahap1/panel.blade.php --}}

{{--
    PENTING: Jangan pakai datatables.css lokal karena versi nya lama (1.x)
    nanti tampilan DT v2.x + ColumnControl berantakan.
    pake CDN aja —Regan.
--}}
@push('styles')
<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
      rel="stylesheet"
      integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
      crossorigin="anonymous">
@endpush

<div class="rv-card">
    <div class="rv-card-header">
        <h6 class="rv-card-title">{{ $title }}</h6>

        <button class="btn-rv-simpan btn btn-success"
                data-group="{{ $group }}">
            <i class="bi bi-save me-1"></i>Simpan
        </button>
    </div>

    {{--
        Tidak pakai .table-responsive wrapper — DT mengelola scroll sendiri
        via responsive + scrollX. Wrapper bisa bentrok dengan DT.
    --}}
    <table class="rv-table display nowrap compact" id="{{ $tableId }}" style="width:100%">
        <thead>
            <tr>
                <th class="text-center" style="width:48px">
                    <input type="checkbox" class="rv-checkbox chk-all" data-group="{{ $group }}">
                </th>
                <th class="text-center" style="width:48px">No</th>
                <th>Inovator</th>
                <th>Nama Inovasi</th>
                <th class="text-center" style="width:110px">Total Nilai</th>
                @foreach($penilai as $p)
                <th class="text-center" style="width:84px" title="{{ $p['nama'] }}">
                    {{ $p['nama_singkat'] }}
                </th>
                @endforeach
                <th class="text-center" style="width:110px">Status</th>
                @if($penilaiLogin)
                <th class="text-center" style="width:80px">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($nominasi as $i => $nom)
            @php
                $sudahLengkap      = $nom['semua_penilai_sudah_nilai'] ?? false;
                $sudahDinilaiLogin = isset($nilaiLoginPerInovator[$nom['id']])
                                     && count($nilaiLoginPerInovator[$nom['id']]) > 0;
                $nilaiPerPenilai   = $nom['nilai'] ?? [];
                $totalNilai        = !empty($nilaiPerPenilai) ? array_sum($nilaiPerPenilai) : 0;
            @endphp
            <tr data-id="{{ $nom['id'] }}"
                data-group="{{ $group }}"
                data-sudah-lengkap="{{ $sudahLengkap ? '1' : '0' }}">

                {{-- Checkbox --}}
                <td class="text-center">
                    <input type="checkbox" class="rv-checkbox chk-row"
                           data-group="{{ $group }}"
                           data-id="{{ $nom['id'] }}"
                           {{ $sudahLengkap ? '' : 'disabled' }}
                           title="{{ $sudahLengkap ? 'Siap diloloskan' : 'Belum semua penilai menilai' }}">
                </td>

                {{-- No --}}
                <td class="text-center row-no">{{ $i + 1 }}</td>

                {{-- Inovator & Nama Inovasi --}}
                <td>{{ $nom['inovator'] }}</td>
                <td>{{ $nom['nama_inovasi'] }}</td>

                {{-- Total Nilai — simpan nilai numerik di data-sort agar DT sort dengan benar --}}
                <td class="text-center rv-nilai" data-sort="{{ $totalNilai }}">
                    @if($totalNilai > 0)
                        {{ number_format($totalNilai, 2) }}
                    @else
                        —
                    @endif
                </td>

                {{-- Nilai per penilai --}}
                @foreach($penilai as $p)
                @php $nilaiP = $nom['nilai'][$p['id']] ?? null; @endphp
                <td class="text-center rv-nilai-penilai"
                    data-penilai-id="{{ $p['id'] }}"
                    data-sort="{{ $nilaiP ?? -1 }}">
                    {{ $nilaiP !== null ? number_format($nilaiP, 2) : '—' }}
                </td>
                @endforeach

                {{-- Status --}}
                <td class="text-center" data-sort="{{ $sudahLengkap ? 2 : ($sudahDinilaiLogin ? 1 : 0) }}">
                    @if($sudahLengkap)
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>Lengkap
                        </span>
                    @elseif($sudahDinilaiLogin)
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-hourglass-split me-1"></i>Sebagian
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            <i class="bi bi-dash-circle me-1"></i>Belum
                        </span>
                    @endif
                </td>

                {{-- Aksi --}}
                @if($penilaiLogin)
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary btn-input-nilai"
                            data-inovator-id="{{ $nom['id'] }}"
                            data-inovator="{{ $nom['inovator'] }}"
                            data-nama-inovasi="{{ $nom['nama_inovasi'] }}"
                            data-group="{{ $group }}"
                            title="Input/Edit Nilai">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning btn-catatan"
                            data-usulan-id="{{ $nom['id'] }}"
                            data-inovator="{{ $nom['inovator'] }}"
                            data-nama-inovasi="{{ $nom['nama_inovasi'] }}"
                            data-group="{{ $group }}"
                            title="Catatan Penilai">
                        <i class="bi bi-chat-left-text"></i>
                    </button>
                </td>
                @endif
            </tr>
            @empty
            {{--
                Sengaja dikosongkan. Jangan taruh <tr colspan> di sini karena DT
                akan error tn/4 (mismatch jumlah kolom vs data yang di-parse).
                DT menampilkan pesan "Tidak ada data" sendiri via language.zeroRecords.
            --}}
            @endforelse
        </tbody>
    </table>
</div>

{{-- ════════ MODAL: dipush ke stack agar dirender di luar tab ════════ --}}
@if($penilaiLogin)
@push('penilaian-modals')

{{-- ── Modal Input Nilai Tahap 1 ── --}}
<div class="modal fade" id="modalNilaiTahap1{{ ucfirst($group) }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Input Nilai Tahap 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 p-3 rounded" style="background:rgba(27,132,255,0.06); border:1px solid rgba(27,132,255,0.15);">
                    <div class="fw-semibold modal-inovator-nama-{{ $group }}"></div>
                    <div class="text-muted small modal-inovasi-nama-{{ $group }}"></div>
                </div>
                <div id="formIndikatorWrapper{{ ucfirst($group) }}">
                    @foreach($indikators as $ind)
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="fw-semibold" style="color:var(--ri-primary)">{{ $ind['nama_indikator'] }}</div>
                            <span class="badge {{ $ind['jenis'] === 'makalah' ? 'bg-info' : 'bg-primary' }} text-uppercase" style="font-size:0.65rem">
                                {{ $ind['jenis'] }}
                            </span>
                        </div>
                        @foreach($ind['keterangans'] as $k)
                        <div class="d-flex align-items-start gap-3 mb-2 p-2 rounded" style="background:#f8f9fa;">
                            <div class="flex-grow-1 small">
                                <span class="badge bg-secondary me-1">{{ $k['nilai_minimal'] }} – {{ $k['nilai_maksimal'] }}</span>
                                {{ $k['keterangan'] }}
                            </div>
                            <input type="number" class="form-control form-control-sm input-nilai-item"
                                   style="width:80px; flex-shrink:0;"
                                   data-keterangan-id="{{ $k['id'] }}"
                                   data-group="{{ $group }}"
                                   min="{{ $k['nilai_minimal'] }}"
                                   max="{{ $k['nilai_maksimal'] }}"
                                   placeholder="0">
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                    @if(empty($indikators))
                    <div class="text-muted text-center py-3">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Belum ada indikator untuk sub event ini.
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-simpan-nilai-modal" data-group="{{ $group }}">
                    <i class="bi bi-save me-1"></i>Simpan Nilai
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Catatan Penilai ── --}}
<div class="modal fade" id="modalCatatan{{ ucfirst($group) }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Catatan Penilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 p-3 rounded" style="background:rgba(27,132,255,0.06); border:1px solid rgba(27,132,255,0.15);">
                    <div class="fw-semibold modal-catatan-inovator-{{ $group }}"></div>
                    <div class="text-muted small modal-catatan-inovasi-{{ $group }}"></div>
                </div>
                <textarea class="form-control textarea-catatan-{{ $group }}" rows="5"
                          placeholder="Tulis catatan untuk usulan ini..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning btn-simpan-catatan" data-group="{{ $group }}">
                    <i class="bi bi-save me-1"></i>Simpan Catatan
                </button>
            </div>
        </div>
    </div>
</div>

@endpush
@endif

@push('scripts')
<script src="<?= asset('assets/jquery/jquery-4.0.0.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js"
        integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ"
        crossorigin="anonymous"></script>
<script>
(function () {
    'use strict';

    /* ── Jumlah kolom penilai (dinamis dari Blade) ── */
    const JUMLAH_PENILAI  = {{ count($penilai) }};
    const ADA_AKSI        = {{ $penilaiLogin ? 'true' : 'false' }};

    /*
     * Indeks kolom:
     *   0  = checkbox
     *   1  = no
     *   2  = inovator
     *   3  = nama inovasi
     *   4  = total nilai
     *   5..(5+JUMLAH_PENILAI-1) = nilai per penilai
     *   5+JUMLAH_PENILAI        = status
     *   5+JUMLAH_PENILAI+1      = aksi (jika ada)
     */
    const COL_CHECKBOX    = 0;
    const COL_NO          = 1;
    const COL_STATUS      = 5 + JUMLAH_PENILAI;
    const COL_AKSI        = ADA_AKSI ? (6 + JUMLAH_PENILAI) : null;

    /* ── Kumpulkan semua index kolom penilai ── */
    const colsPenilai = Array.from({ length: JUMLAH_PENILAI }, (_, i) => 5 + i);

    $(document).ready(function () {
        const columnDefs = [
            /* Checkbox — tidak bisa di-sort/search */
            {
                targets    : COL_CHECKBOX,
                orderable  : false,
                searchable : false,
                width      : '48px',
                className  : 'dt-center',
            },
            /* No — tidak perlu search */
            {
                targets    : COL_NO,
                searchable : false,
                width      : '48px',
                className  : 'dt-center',
            },
            /* Total Nilai — sort pakai data-sort (nilai numerik) */
            {
                targets   : 4,
                className : 'dt-center',
                type      : 'num',
            },
            /* Kolom nilai per penilai — sort pakai data-sort */
            {
                targets   : colsPenilai,
                className : 'dt-center',
                type      : 'num',
            },
            /* Status — sort pakai data-sort (0/1/2), tidak perlu search bebas */
            {
                targets    : COL_STATUS,
                className  : 'dt-center',
                searchable : false,
                width      : '110px',
            },
        ];

        /* Aksi: orderable:false, searchable:false */
        if (COL_AKSI !== null) {
            columnDefs.push({
                targets    : COL_AKSI,
                orderable  : false,
                searchable : false,
                width      : '80px',
                className  : 'dt-center',
            });
        }

        $('#{{ $tableId }}').DataTable({
            responsive  : true,
            language    : {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data ditemukan',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : { first: '«', last: '»', next: '›', previous: '‹' },
            },
            layout: {
                topStart: [
                    'pageLength',
                    {
                        buttons: ['colvis'],
                    },
                ],
                topEnd     : 'search',
                bottomStart: 'info',
                bottomEnd  : 'paging',
            },
            pageLength  : 10,
            lengthMenu  : [10, 25, 50, 100],
            order       : [[1, 'asc']],
            columnDefs,
        });
    });
})();
</script>
@endpush