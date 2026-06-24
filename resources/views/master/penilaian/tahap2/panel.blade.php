{{-- resources/views/master/penilaian/tahap2/panel.blade.php --}}
<div class="rv-card">
    <div class="rv-card-header">
        <h6 class="rv-card-title">{{ $title }}</h6>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-warning btn-auto-ranking"
                    data-group="{{ $group }}"
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
            <button class="btn-rv-excel btn btn-info"
                    data-table="{{ $tableId }}"
                    data-filename="{{ $filename }}">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="rv-table" id="{{ $tableId }}">
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
                    // Total Nilai = JUMLAH semua nilai per penilai (bukan rata-rata)
                    $nilaiPerPenilai = $nom['nilai_per_penilai'] ?? [];
                    $totalNilai      = !empty($nilaiPerPenilai) ? array_sum($nilaiPerPenilai) : 0;

                    $totalRank   = (int) ($nom['total_rank'] ?? 0);
                    $rankingSaya = $rankingLogin[$nom['id']] ?? null;
                    $rankTampil  = ($rankingSaya !== null && $rankingSaya !== '')
                                    ? (int) $rankingSaya
                                    : $totalRank;
                @endphp
                <tr data-id="{{ $nom['id'] }}" data-nilai="{{ $totalNilai }}">
                    <td class="text-center row-no">{{ $i + 1 }}</td>
                    <td>{{ $nom['inovator'] }}</td>
                    <td>{{ $nom['nama_inovasi'] }}</td>

                    {{-- ── Total Nilai (jumlah) ── --}}
                    <td class="text-center">
                        @if($totalNilai > 0)
                            <span class="rv-total-nilai-badge">
                                {{ number_format($totalNilai, 1) }}
                            </span>
                        @else
                            <span style="color:var(--ri-text-muted)">-</span>
                        @endif
                    </td>

                    {{-- ── Nilai per penilai ── --}}
                    @foreach($penilai as $p)
                    <td class="text-center rv-nilai-penilai" data-penilai-id="{{ $p['id'] }}">
                        @if(isset($nom['nilai_per_penilai'][$p['id']]))
                            <span class="rv-nilai-cell">
                                {{ number_format($nom['nilai_per_penilai'][$p['id']], 1) }}
                            </span>
                        @else
                            <span style="color:var(--ri-text-muted)">-</span>
                        @endif
                    </td>
                    @endforeach

                    {{-- ── Input Ranking Saya ── --}}
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

                    {{-- ── Total Rank Badge ── --}}
                    <td class="text-center rv-total-rank" data-usulan-id="{{ $nom['id'] }}">
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
                <tr>
                    <td colspan="{{ 4 + count($penilai) + ($penilaiLogin ? 2 : 1) }}" class="rv-empty">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data nominasi {{ $group }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>