{{-- resources/views/master/penilaian/tahap2/panel.blade.php --}}
<div class="rv-card">
    <div class="rv-card-header">
        <h6 class="rv-card-title">{{ $title }}</h6>
        <div class="d-flex gap-2">
            @if($penilaiLogin)
            <button class="btn btn-success btn-simpan-ranking"
                    data-group="{{ $group }}"
                    data-sub-event-id="{{ request()->route('id') }}">
                <i class="bi bi-trophy me-1"></i>Simpan Ranking
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
                    <th class="text-center" style="width:110px">Total Nilai</th>
                    @foreach($penilai as $p)
                    <th class="text-center" style="width:80px" title="{{ $p['nama'] }}">{{ $p['nama_singkat'] }}</th>
                    @endforeach
                    @if($penilaiLogin)
                    <th class="text-center" style="width:90px">Ranking Saya</th>
                    @endif
                    <th class="text-center" style="width:100px">Total Rank</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nominasi as $i => $nom)
                @php
                    $totalNilai  = $nom['total_nilai_tahap1'] ?? 0;
                    $totalRank   = $nom['total_rank'] ?? 0;
                    $rankingSaya = $rankingLogin[$nom['id']] ?? '';
                @endphp
                <tr data-id="{{ $nom['id'] }}">
                    <td class="text-center row-no">{{ $i + 1 }}</td>
                    <td>{{ $nom['inovator'] }}</td>
                    <td>{{ $nom['nama_inovasi'] }}</td>
                    <td class="text-center rv-nilai" data-nilai="{{ $totalNilai }}">
                        @if($totalNilai > 0)
                            <span class="badge"
                                  style="background:rgba(27,132,255,0.12); color:#1b84ff; font-size:0.82em;">
                                {{ number_format($totalNilai, 1) }}
                            </span>
                        @else
                            <span style="color:var(--ri-text-muted)">-</span>
                        @endif
                    </td>
                    @foreach($penilai as $p)
                    <td class="text-center rv-nilai-penilai" data-penilai-id="{{ $p['id'] }}">
                        {{ isset($nom['nilai_per_penilai'][$p['id']]) ? number_format($nom['nilai_per_penilai'][$p['id']], 1) : '-' }}
                    </td>
                    @endforeach
                    @if($penilaiLogin)
                    <td class="text-center">
                        <input type="number"
                               class="form-control form-control-sm text-center input-ranking"
                               style="width:64px; margin:auto;"
                               data-usulan-id="{{ $nom['id'] }}"
                               data-group="{{ $group }}"
                               min="1"
                               value="{{ $rankingSaya }}"
                               placeholder="-">
                    </td>
                    @endif
                    <td class="text-center rv-total-rank" data-usulan-id="{{ $nom['id'] }}">
                        @if($totalRank > 0)
                            <span class="badge bg-primary">{{ $totalRank }}</span>
                        @else
                            <span style="color:var(--ri-text-muted)">-</span>
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