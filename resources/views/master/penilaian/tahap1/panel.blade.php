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

    {{-- ── Simpan bar (hidden by default, shown via JS) ── --}}
    <div class="rv-simpan-bar" id="simpanBar{{ ucfirst($group) }}" style="display:none;">
        <span class="rv-simpan-info">
            <i class="bi bi-check2-circle me-1"></i>
            <span class="simpan-count">0</span> inovasi dipilih untuk lolos ke Tahap 2
        </span>
        <button class="btn-rv-simpan btn btn-success" data-group="{{ $group }}">
            <i class="bi bi-save me-1"></i>Simpan
        </button>
    </div>

    <div class="table-responsive">
        <table class="rv-table" id="{{ $tableId }}">
            <thead>
                <tr>
                    <th class="text-center" style="width:48px">
                        <input type="checkbox" class="rv-checkbox chk-all" data-group="{{ $group }}">
                    </th>
                    <th class="text-center" style="width:48px">No</th>
                    <th>Inovator</th>
                    <th>Nama Inovasi</th>
                    <th class="text-center" style="width:100px">Total Nilai</th>
                    @foreach($penilai as $p)
                    <th class="text-center" style="width:84px">{{ $p['nama_singkat'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($nominasi as $i => $nom)
                <tr data-id="{{ $nom['id'] }}">
                    <td class="text-center">
                        <input type="checkbox"
                               class="rv-checkbox chk-row"
                               data-group="{{ $group }}"
                               data-id="{{ $nom['id'] }}"
                               {{ !empty($nom['lolos']) ? 'checked' : '' }}>
                    </td>
                    <td class="text-center row-no">{{ $i + 1 }}</td>
                    <td>{{ $nom['inovator'] }}</td>
                    <td>{{ $nom['nama_inovasi'] }}</td>
                    <td class="text-center rv-nilai" data-nilai="{{ $nom['total_nilai'] }}">
                        {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 2) : '—' }}
                    </td>
                    @foreach($penilai as $p)
                    <td class="text-center">
                        {{ isset($nom['nilai'][$p['id']]) ? number_format($nom['nilai'][$p['id']], 2) : '—' }}
                    </td>
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