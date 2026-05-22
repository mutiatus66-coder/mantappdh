<div class="rv-card">

    <div class="rv-card-header">
        <h6 class="rv-card-title">{{ $title }}</h6>
        <div class="d-flex gap-2">
            <button class="btn-rv-rank btn btn-primary"
                    data-table="{{ $tableId }}">
                <i class="bi bi-sort-numeric-down me-1"></i>Rangking
            </button>
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
                    <th class="text-center" style="width:88px">Rangking</th>
                    <th class="text-center" style="width:100px">Total Nilai</th>
                    @foreach($penilai as $p)
                    <th class="text-center" style="width:80px">{{ $p['nama_singkat'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($nominasi as $i => $nom)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $nom['inovator'] }}</td>
                    <td>{{ $nom['nama_inovasi'] }}</td>
                    <td class="text-center">
                        @if($nom['rangking'])
                            <span class="rv-badge-rank">{{ $nom['rangking'] }}</span>
                        @else
                            <span style="color:var(--ri-text-muted)">—</span>
                        @endif
                    </td>
                    <td class="text-center rv-nilai">
                        {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 1) : '—' }}
                    </td>
                    @foreach($penilai as $p)
                    <td class="text-center">{{ $nom['nilai'][$p['id']] ?? '—' }}</td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 5 + count($penilai) }}" class="rv-empty">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data nominasi {{ $group }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>