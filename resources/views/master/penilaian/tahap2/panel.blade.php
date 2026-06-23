{{-- panel.blade.php - tabel nominasi + modal input nilai Tahap 2 --}}
<div class="rv-card">
    <div class="rv-card-header">
        <h6 class="rv-card-title">{{ $title }}</h6>
        <div class="d-flex gap-2">
            <button class="btn-rv-rank btn btn-primary" data-table="{{ $tableId }}">
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
                    <th class="text-center" style="width:100px">Nilai Tahap 1</th>
                    <th class="text-center" style="width:100px">Total Nilai Tahap 2</th>
                    @foreach($penilai as $p)
                    <th class="text-center" style="width:80px" title="{{ $p['nama'] }}">{{ $p['nama_singkat'] }}</th>
                    @endforeach
                    <th class="text-center" style="width:110px">Status</th>
                    @if($penilaiLogin)
                    <th class="text-center" style="width:80px">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($nominasi as $i => $nom)
                @php $sudahLengkap = $nom['semua_penilai_sudah_nilai'] ?? false; @endphp
                <tr data-id="{{ $nom['id'] }}" data-sudah-lengkap="{{ $sudahLengkap ? '1' : '0' }}">
                    <td class="text-center row-no">{{ $i + 1 }}</td>
                    <td>{{ $nom['inovator'] }}</td>
                    <td>{{ $nom['nama_inovasi'] }}</td>
                    <td class="text-center rv-rank-cell">
                        <span style="color:var(--ri-text-muted)">-</span>
                    </td>
                    <td class="text-center" style="color:var(--ri-text-muted);">
                        @php $nt1 = $nom['total_nilai_tahap1'] ?? 0; @endphp
                        @if($nt1 > 0)
                            <span class="badge" style="background:rgba(27,132,255,0.12); color:#1b84ff; font-size:0.82em;">
                                {{ number_format($nt1, 1) }}
                            </span>
                        @else
                            <span style="color:var(--ri-text-muted)">-</span>
                        @endif
                    </td>
                    <td class="text-center rv-nilai" data-nilai="{{ $nom['total_nilai'] }}">
                        {{ $nom['total_nilai'] > 0 ? number_format($nom['total_nilai'], 1) : '-' }}
                    </td>
                    @foreach($penilai as $p)
                    <td class="text-center rv-nilai-penilai" data-penilai-id="{{ $p['id'] }}">
                        {{ isset($nom['nilai'][$p['id']]) ? number_format($nom['nilai'][$p['id']], 2) : '-' }}
                    </td>
                    @endforeach
                    <td class="text-center">
                        @if($sudahLengkap)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lengkap</span>
                        @elseif(isset($nilaiLoginPerInovator[$nom['id']]) && count($nilaiLoginPerInovator[$nom['id']]) > 0)
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Sebagian</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-dash-circle me-1"></i>Belum</span>
                        @endif
                    </td>
                    @if($penilaiLogin)
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary btn-input-nilai-t2"
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
                    <td colspan="{{ 7 + count($penilai) + ($penilaiLogin ? 1 : 0) }}" class="rv-empty">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data nominasi {{ $group }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Input Nilai Tahap 2 --}}
@if($penilaiLogin)
<div class="modal fade" id="modalNilaiTahap2{{ ucfirst($group) }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Input Nilai Tahap 2
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 p-3 rounded" style="background:rgba(27,132,255,0.06); border:1px solid rgba(27,132,255,0.15);">
                    <div class="fw-semibold modal-inovator-nama-t2"></div>
                    <div class="text-muted small modal-inovasi-nama-t2"></div>
                </div>
                @if(empty($indikators))
                    <div class="text-muted text-center py-3">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Belum ada indikator Tahap 2 untuk sub event ini.
                    </div>
                @else
                    @php
                        $grouped = [];
                        foreach ($indikators as $ind) {
                            $grouped[$ind['jenis']][] = $ind;
                        }
                    @endphp
                    @foreach($grouped as $jenis => $inds)
                    <div class="mb-1 fw-bold" style="color:var(--ri-primary); border-bottom:1px solid rgba(27,132,255,0.2); padding-bottom:4px;">
                        {{ $jenis }}
                    </div>
                    @foreach($inds as $ind)
                    <div class="mb-4 mt-2">
                        <div class="fw-semibold mb-2 small text-secondary">{{ $ind['nama_indikator'] }}</div>
                        @foreach($ind['keterangans'] as $k)
                        <div class="d-flex align-items-start gap-3 mb-2 p-2 rounded" style="background:#f8f9fa;">
                            <div class="flex-grow-1 small">
                                <span class="badge bg-secondary me-1">{{ $k['nilai_minimal'] }}\u2013{{ $k['nilai_maksimal'] }}</span>
                                {{ $k['keterangan'] }}
                            </div>
                            <input type="number"
                                   class="form-control form-control-sm input-nilai-item-t2"
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
                    @endforeach
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-simpan-nilai-modal-t2" data-group="{{ $group }}">
                    <i class="bi bi-save me-1"></i>Simpan Nilai
                </button>
            </div>
        </div>
    </div>
</div>
@endif
