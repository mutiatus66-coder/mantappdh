@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
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

    {{-- Search --}}
    <div class="sub-event-stats mb-3">
        <div class="search-box">
            <input type="text" id="searchUsulan" placeholder="Cari nama inovasi, ketua, atau inovator...">
        </div>
    </div>

    {{-- Tabel --}}
    <div style="overflow-x:auto">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="45">No</th>
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
            <tbody id="tBody">
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
                    <td colspan="9" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada usulan untuk sub event ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const input = document.getElementById('searchUsulan');
    const rows  = document.querySelectorAll('#tBody tr');
    input.addEventListener('keyup', function () {
        const kw = this.value.toLowerCase().trim();
        rows.forEach(r => {
            if (r.querySelector('.empty-row')) return;
            r.style.display = r.textContent.toLowerCase().includes(kw) ? '' : 'none';
        });
    });
})();
</script>
@endpush
