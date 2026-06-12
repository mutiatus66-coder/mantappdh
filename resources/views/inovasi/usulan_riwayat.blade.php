@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    {{-- ── HEADER ── --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="ec-title">Data Riwayat Usulan</h3>
            <p class="ec-subtitle">
                {{ $eventNama ?? '' }}
                @if(!empty($eventNama) && !empty($subEvent)) &mdash; @endif
                {{ $subEvent ?? '' }}
            </p>
        </div>
        <a href="{{ url('/inovasi/riwayat') }}" class="btn btn-dark">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ── SEARCH ── --}}
    <div class="sub-event-stats mb-3">
        <div class="search-box">
            <input type="text" id="searchUsulan" placeholder="Cari nama inovasi atau inovator...">
        </div>
    </div>

    {{-- ── TABLE ── --}}
    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Status</th>
                    <th>Inovator</th>
                    <th>Nama Inovasi</th>
                    <th>Nama Tim</th>
                    <th>Nama Ketua</th>
                    <th>Email Ketua</th>
                    <th>No WA Ketua</th>
                </tr>
            </thead>
            <tbody id="usulanBody">
                @forelse($usulan as $u)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge-kategori">{{ $u['status'] ?? '-' }}</span></td>
                    <td>{{ $u['inovator'] ?? '-' }}</td>
                    <td>{{ $u['nama_inovasi'] ?? '-' }}</td>
                    <td>{{ $u['nama_tim'] ?? '-' }}</td>
                    <td>{{ $u['ketua_nama'] ?? '-' }}</td>
                    <td>{{ $u['ketua_email'] ?? '-' }}</td>
                    <td>{{ $u['ketua_wa'] ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
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
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchUsulan');
    const rows        = document.querySelectorAll('#usulanBody tr');

    searchInput.addEventListener('keyup', function () {
        const kw = this.value.toLowerCase().trim();
        rows.forEach(r => {
            if (r.querySelector('.empty-row')) return;
            r.style.display = r.textContent.toLowerCase().includes(kw) ? '' : 'none';
        });
    });
});
</script>
@endpush