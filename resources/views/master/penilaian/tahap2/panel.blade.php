{{-- resources/views/master/penilaian/tahap2/panel.blade.php --}}
{{--
    PARTIAL — di-include 2x (group umum & pelajar) dari show.blade.php.
    JANGAN push CDN <script> di sini — akan di-load duplikat dan bentrok.
    CDN jQuery + pdfmake + DataTables ada di show.blade.php induk.
    Panel ini hanya push init DT untuk tabelnya sendiri via @push('dt-init').
--}}

<div class="rv-card">
    <div class="rv-card-header">
        <h6 class="rv-card-title">{{ $title }}</h6>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-warning btn-auto-ranking"
                    data-group="{{ $group }}"
                    data-table-id="{{ $tableId }}"
                    title="Urutkan berdasarkan nilai tertinggi dan isi ranking otomatis">
                <i class="bi bi-sort-numeric-down me-1"></i>Ranking
            </button>
            @if($penilaiLogin)
            <button class="btn btn-success btn-simpan-ranking"
                    data-group="{{ $group }}"
                    data-sub-event-id="{{ request()->route('id') }}">
                <i class="bi bi-floppy me-1"></i>Simpan Ranking
            </button>
            @endif
        </div>
    </div>

    {{--
        Tidak pakai .table-responsive wrapper — DT kelola scroll sendiri.
        class "display nowrap compact" = stylesheet DT default (stripe, hover, order-column).
    --}}
    <table class="rv-table display nowrap compact" id="{{ $tableId }}" style="width:100%">
        <thead>
            <tr>
                <th class="text-center" style="width:48px">No</th>
                <th>Inovator</th>
                <th>Nama Inovasi</th>
                <th class="text-center" style="width:120px">Total Nilai</th>
                @foreach($penilai as $p)
                <th class="text-center" style="width:80px" title="{{ $p['nama'] }}">
                    {{ $p['nama_singkat'] }}
                </th>
                @endforeach
                @if($penilaiLogin)
                <th class="text-center" style="width:90px">Ranking Saya</th>
                @endif
                <th class="text-center" style="width:100px">Total Rank</th>
            </tr>
        </thead>
        <tbody id="tbody-{{ $group }}">
            @forelse($nominasi as $i => $nom)
            @php
                $nilaiPerPenilai = $nom['nilai_per_penilai'] ?? [];
                $totalNilai      = !empty($nilaiPerPenilai) ? array_sum($nilaiPerPenilai) : 0;
                $totalRank       = (int) ($nom['total_rank'] ?? 0);
                $rankingSaya     = $rankingLogin[$nom['id']] ?? null;
                $rankTampil      = ($rankingSaya !== null && $rankingSaya !== '')
                                    ? (int) $rankingSaya
                                    : $totalRank;
            @endphp
            <tr data-id="{{ $nom['id'] }}" data-nilai="{{ $totalNilai }}">

                <td class="text-center row-no">{{ $i + 1 }}</td>
                <td>{{ $nom['inovator'] }}</td>
                <td>{{ $nom['nama_inovasi'] }}</td>

                {{-- data-sort = nilai numerik agar DT sort benar (bukan sort string "1.234,56") --}}
                <td class="text-center" data-sort="{{ $totalNilai }}">
                    @if($totalNilai > 0)
                        <span class="rv-total-nilai-badge">{{ number_format($totalNilai, 1) }}</span>
                    @else
                        <span style="color:var(--ri-text-muted)">-</span>
                    @endif
                </td>

                @foreach($penilai as $p)
                @php $nilaiP = $nom['nilai_per_penilai'][$p['id']] ?? null; @endphp
                <td class="text-center rv-nilai-penilai"
                    data-penilai-id="{{ $p['id'] }}"
                    data-sort="{{ $nilaiP ?? -1 }}">
                    @if($nilaiP !== null)
                        <span class="rv-nilai-cell">{{ number_format($nilaiP, 1) }}</span>
                    @else
                        <span style="color:var(--ri-text-muted)">-</span>
                    @endif
                </td>
                @endforeach

                {{-- Ranking Saya: orderable:false agar DT tidak bingung dengan <input> --}}
                @if($penilaiLogin)
                <td class="text-center">
                    <input type="number"
                           class="form-control form-control-sm text-center input-ranking"
                           style="width:64px; margin:auto;"
                           data-usulan-id="{{ $nom['id'] }}"
                           data-group="{{ $group }}"
                           min="1"
                           value="{{ $rankingSaya ?? '' }}"
                           placeholder="-">
                </td>
                @endif

                {{--
                    Total Rank: data-sort pakai 9999 jika belum ada rank
                    agar baris tanpa rank turun ke bawah saat sort ascending.
                --}}
                <td class="text-center rv-total-rank"
                    data-usulan-id="{{ $nom['id'] }}"
                    data-sort="{{ $rankTampil > 0 ? $rankTampil : 9999 }}">
                    @if($rankTampil > 0)
                        @if($rankTampil === 1)
                            <span class="badge rv-rank-badge rv-rank-top rv-rank-gold" data-rank="1">
                                <i class="bi bi-trophy-fill me-1" style="font-size:0.7em"></i>1
                            </span>
                        @elseif($rankTampil === 2)
                            <span class="badge rv-rank-badge rv-rank-top rv-rank-silver" data-rank="2">
                                <i class="bi bi-award-fill me-1" style="font-size:0.7em"></i>2
                            </span>
                        @elseif($rankTampil === 3)
                            <span class="badge rv-rank-badge rv-rank-top rv-rank-bronze" data-rank="3">
                                <i class="bi bi-award-fill me-1" style="font-size:0.7em"></i>3
                            </span>
                        @else
                            <span class="badge rv-rank-badge rv-rank-normal" data-rank="{{ $rankTampil }}">
                                {{ $rankTampil }}
                            </span>
                        @endif
                    @else
                        <span class="rv-rank-empty" style="color:var(--ri-text-muted)">-</span>
                    @endif
                </td>
            </tr>
            @empty
            {{--
                Kosongkan @empty — jangan taruh <tr colspan> karena DT akan error tn/4
                (mismatch jumlah kolom vs baris). DT tampilkan zeroRecords sendiri.
            --}}
            @endforelse
        </tbody>
    </table>
</div>

{{--
    Push init DT per-tabel ke stack 'dt-init'.
    Stack ini di-@stack dari show.blade.php SETELAH CDN DT di-load,
    sehingga jQuery & DT pasti sudah tersedia saat script ini berjalan.
    Karena panel di-include 2x, push ini terpanggil 2x — OK karena
    masing-masing menarget tableId yang berbeda.
--}}
@push('dt-init')
<script>
(function () {
    'use strict';

    const TABLE_ID       = '{{ $tableId }}';
    const JUMLAH_PENILAI = {{ count($penilai) }};
    const ADA_RANKING    = {{ $penilaiLogin ? 'true' : 'false' }};

    /*
     * Layout kolom:
     *   0                        → No
     *   1                        → Inovator
     *   2                        → Nama Inovasi
     *   3                        → Total Nilai
     *   4 .. 4+JUMLAH_PENILAI-1  → nilai per penilai (dinamis)
     *   4+JUMLAH_PENILAI          → Ranking Saya   (hanya jika ADA_RANKING)
     *   COL_TOTAL_RANK            → Total Rank      (kolom terakhir)
     */
    const COL_TOTAL_RANK = 4 + JUMLAH_PENILAI + (ADA_RANKING ? 1 : 0);
    const COL_RANKING    = ADA_RANKING ? (4 + JUMLAH_PENILAI) : null;
    const colsPenilai    = Array.from({ length: JUMLAH_PENILAI }, (_, i) => 4 + i);

    /* Kolom yang di-export Excel: semua kecuali No (0) dan Ranking Saya (input) */
    const exportCols = Array.from(
        { length: COL_TOTAL_RANK + 1 },
        (_, i) => i
    ).filter(i => i !== 0 && i !== COL_RANKING);

    $(document).ready(function () {

        /* Guard: jika sudah pernah diinit (hot-reload), destroy dulu */
        if ($.fn.DataTable.isDataTable('#' + TABLE_ID)) {
            $('#' + TABLE_ID).DataTable().destroy();
        }

        const columnDefs = [
            { targets: 0,           searchable: false, width: '48px',  className: 'dt-center' },
            { targets: 3,           type: 'num',                        className: 'dt-center' },
            { targets: colsPenilai, type: 'num',                        className: 'dt-center' },
            {
                targets    : COL_TOTAL_RANK,
                type       : 'num',
                searchable : false,
                width      : '100px',
                className  : 'dt-center',
            },
        ];

        if (COL_RANKING !== null) {
            columnDefs.push({
                targets    : COL_RANKING,
                orderable  : false,
                searchable : false,
                width      : '90px',
                className  : 'dt-center',
            });
        }

        /* Simpan instance DT di window agar bisa diakses btn-auto-ranking */
        window['dt_' + TABLE_ID] = $('#' + TABLE_ID).DataTable({
            responsive : true,
            language   : {
                lengthMenu  : 'Tampilkan _MENU_ data',
                search      : 'Cari:',
                zeroRecords : 'Tidak ada data nominasi.',
                info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty   : 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate    : { first: '«', last: '»', next: '›', previous: '‹' },
            },
            layout     : {
                topStart: [
                    'pageLength',
                    {
                        buttons: [
                            'colvis',
                            {
                                extend       : 'excelHtml5',
                                text         : '<i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel',
                                className    : 'btn btn-info btn-sm',
                                title        : '{{ addslashes($title) }}',
                                exportOptions: { columns: exportCols },
                            },
                        ],
                    },
                ],
                topEnd     : 'search',
                bottomStart: 'info',
                bottomEnd  : 'paging',
            },
            pageLength : 10,
            lengthMenu : [10, 25, 50, 100],
            order      : [[COL_TOTAL_RANK, 'asc']],
            columnDefs,
        });

        /*
         * btn-auto-ranking — wajib pakai DT API (bukan DOM langsung).
         * Setelah DT aktif, baris di-manage DT; DOM manipulation langsung
         * tidak ter-reflect di internal state DT dan bisa menyebabkan tn/4.
         */
        document.querySelector(
            `.btn-auto-ranking[data-table-id="{{ $tableId }}"]`
        )?.addEventListener('click', function () {
            const dt = window['dt_' + TABLE_ID];
            if (!dt) return;

            /* Ambil semua node baris via DT API */
            const rows = dt.rows().nodes().toArray().map(tr => ({
                node : tr,
                nilai: parseFloat(tr.dataset.nilai) || 0,
            }));

            /* Sort descending by total nilai */
            rows.sort((a, b) => b.nilai - a.nilai);

            rows.forEach(({ node }, idx) => {
                const rank = idx + 1;

                /* Update <input> ranking */
                const inp = node.querySelector('.input-ranking');
                if (inp) inp.value = rank;

                /* Update data-sort & badge di cell Total Rank */
                const rankCell = node.querySelector('.rv-total-rank');
                if (rankCell) {
                    rankCell.dataset.sort = rank;
                    rankCell.innerHTML   = renderRankBadge(rank);
                }
            });

            /* Beritahu DT bahwa DOM sudah berubah, redraw tanpa reset halaman/pagination */
            dt.rows().invalidate('dom').draw(false);
        });
    });

    /* ── Helper render badge rank ── */
    function renderRankBadge(rank) {
        const meta = {
            1: { cls: 'rv-rank-gold',   icon: 'trophy-fill' },
            2: { cls: 'rv-rank-silver', icon: 'award-fill'  },
            3: { cls: 'rv-rank-bronze', icon: 'award-fill'  },
        };
        if (meta[rank]) {
            return `<span class="badge rv-rank-badge rv-rank-top ${meta[rank].cls}" data-rank="${rank}">
                        <i class="bi bi-${meta[rank].icon} me-1" style="font-size:0.7em"></i>${rank}
                    </span>`;
        }
        return `<span class="badge rv-rank-badge rv-rank-normal" data-rank="${rank}">${rank}</span>`;
    }
})();
</script>
@endpush