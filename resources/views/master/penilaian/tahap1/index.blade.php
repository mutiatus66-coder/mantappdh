@extends('index')

@section('content')

{{-- Flash Message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(245,158,11,0.10); border:1px solid rgba(245,158,11,0.3); color:#92400e; margin: 0 20px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="t1-container">

    {{-- ── HEADER ── --}}
    <div class="t1-header d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="t1-title">Penilaian Tahap 1</h3>
            <p class="t1-subtitle">Rekap verifikasi dan nilai penilai per sub event</p>
        </div>
    </div>

    {{-- ── CARD GRID ── --}}
    <div class="row g-4">
        @foreach($subEvents as $se)
        @php
            $seId       = $se['id'];
            $nominasiSe = $nominasiData[$seId] ?? [];
            $total      = count($nominasiSe);
            $dinilai    = collect($nominasiSe)->filter(fn($n) => ($n['total_nilai'] ?? 0) > 0)->count();
            $pct        = $total > 0 ? round($dinilai / $total * 100) : 0;
        @endphp

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="t1-card h-100">
                <div class="t1-card-body">
                    <div class="t1-card-top d-flex justify-content-between align-items-start mb-3">
                        <span class="t1-badge-tahun">{{ $se['tahun'] }}</span>
                        <i class="bi bi-clipboard2-check t1-card-icon"></i>
                    </div>

                    <h5 class="t1-card-title">{{ $se['sub_event'] }}</h5>
                    <p class="t1-card-sub-event text-truncate" title="{{ $se['sub_event'] }}">{{ $se['sub_event'] }}</p>

                    <div class="t1-progress-wrap mt-3 mb-1">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="t1-progress-label">Progress Verifikasi</span>
                            <span class="t1-progress-pct">{{ $pct }}%</span>
                        </div>
                        <div class="t1-progress-bar-bg">
                            <div class="t1-progress-bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="t1-progress-label">{{ $dinilai }} / {{ $total }} dinilai</span>
                        </div>
                    </div>

                    <a href="{{ route('penilaian.tahap1.show', $seId) }}"
                        class="btn btn-lihat-verifikasi w-100 mt-3">
                        <i class="bi bi-search me-2"></i>Lihat Nilai Verifikasi
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection


@push('styles')
<style>
/* ── Container ── */
.t1-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.10);
    padding: 28px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}

/* ── Header ── */
.t1-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: var(--ri-text-primary);
}
.t1-subtitle {
    margin: 4px 0 0;
    color: var(--ri-text-muted);
    font-size: 0.875rem;
}

/* ── Card ── */
.t1-card {
    background: var(--ri-card-bg);
    border: 1px solid var(--ri-border);
    border-radius: 10px;
    overflow: hidden;
    transition: box-shadow 0.18s, transform 0.18s, background 0.2s;
}
.t1-card:hover {
    box-shadow: 0 6px 20px rgba(12,76,138,0.13);
    transform: translateY(-2px);
}
.t1-card-body { padding: 20px 22px 22px; }

.t1-badge-tahun {
    background: #fef3c7;
    color: #92400e;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid rgba(245,158,11,0.25);
    transition: background 0.2s, color 0.2s;
}
[data-bs-theme="dark"] .t1-badge-tahun {
    background: rgba(245,158,11,0.15);
    color: #fbbf24;
    border-color: rgba(245,158,11,0.30);
}
.t1-card-icon {
    font-size: 1.6rem;
    color: var(--ri-text-muted);
    opacity: 0.4;
}

.t1-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ri-text-primary);
    margin: 0 0 4px;
    line-height: 1.4;
}
.t1-card-event {
    font-size: 0.78rem;
    color: var(--ri-text-muted);
    margin: 0;
}

/* ── Progress ── */
.t1-progress-label {
    font-size: 0.75rem;
    color: var(--ri-text-muted);
}
.t1-progress-pct {
    font-size: 0.75rem;
    font-weight: 700;
    color: #0C4C8A;
}
[data-bs-theme="dark"] .t1-progress-pct { color: #378ADD; }

.t1-progress-bar-bg {
    width: 100%;
    height: 7px;
    background: var(--ri-border);
    border-radius: 99px;
    overflow: hidden;
}
.t1-progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #0C4C8A, #185FA5);
    border-radius: 99px;
    transition: width 0.5s ease;
}

/* ── Button ── */
.btn-lihat-verifikasi {
    background: var(--ri-table-head-bg);
    color: var(--ri-text-primary);
    border: 1px solid var(--ri-border);
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 8px;
    padding: 9px 14px;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-lihat-verifikasi:hover {
    background: #0C4C8A;
    color: #fff !important;
    border-color: #0C4C8A;
}
[data-bs-theme="dark"] .btn-lihat-verifikasi:hover {
    background: #185FA5;
    border-color: #185FA5;
    color: #fff !important;
}
</style>
@endpush