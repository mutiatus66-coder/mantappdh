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
                    @if($penilaiLogin)
                    <th class="text-center" style="width:80px">Aksi</th>
                    @endif
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
                    @if($penilaiLogin)
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary btn-input-nilai"
                                data-inovator-id="{{ $nom['id'] }}"
                                data-inovator="{{ $nom['inovator'] }}"
                                data-nama-inovasi="{{ $nom['nama_inovasi'] }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 5 + count($penilai) + ($penilaiLogin ? 1 : 0) }}" class="rv-empty">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data nominasi {{ $group }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Modal Input Nilai Tahap 1 ── --}}
@if($penilaiLogin)
<div class="modal fade" id="modalNilaiTahap1{{ ucfirst($group) }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Input Nilai Tahap 1
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 p-3 rounded" style="background:rgba(27,132,255,0.06); border:1px solid rgba(27,132,255,0.15);">
                    <div class="fw-semibold modal-inovator-nama"></div>
                    <div class="text-muted small modal-inovasi-nama"></div>
                </div>

                <div id="formIndikatorWrapper{{ ucfirst($group) }}">
                    @foreach($indikators as $ind)
                    <div class="mb-4">
                        <div class="fw-semibold mb-2" style="color:var(--ri-primary)">
                            {{ $ind['nama_indikator'] }}
                        </div>
                        @foreach($ind['keterangans'] as $k)
                        <div class="d-flex align-items-start gap-3 mb-2 p-2 rounded" style="background:#f8f9fa;">
                            <div class="flex-grow-1 small">
                                <span class="badge bg-secondary me-1">{{ $k['nilai_minimal'] }}–{{ $k['nilai_maksimal'] }}</span>
                                {{ $k['keterangan'] }}
                            </div>
                            <input type="number"
                                   class="form-control form-control-sm input-nilai-item"
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
@endif