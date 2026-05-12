@extends('index')

@section('content')

{{-- Flash Message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(101,166,5,0.1); border:1px solid rgba(101,166,5,0.3); color:#3a5a02; margin: 0 20px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="penilaian-container">

    {{-- ── HEADER ── --}}
    <div class="penilaian-header d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="penilaian-title">Penilaian Tahap 2</h3>
            <p class="penilaian-subtitle">Rekap nominasi dan nilai penilai per sub event</p>
        </div>
    </div>

    {{-- ── CARD GRID — satu card per sub event ── --}}
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
            <div class="penilaian-card h-100">
                <div class="penilaian-card-body">
                    <div class="penilaian-card-top d-flex justify-content-between align-items-start mb-3">
                        <span class="penilaian-badge-tahun">{{ $se['tahun'] }}</span>
                        <i class="bi bi-clipboard2-check penilaian-card-icon"></i>
                    </div>

                    <h5 class="penilaian-card-title">{{ $se['sub_event'] }}</h5>
                    <p class="penilaian-card-event text-truncate" title="{{ $se['event'] }}">{{ $se['event'] }}</p>

                    <div class="penilaian-progress-wrap mt-3 mb-1">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="penilaian-progress-label">Progress Penilaian</span>
                            <span class="penilaian-progress-pct">{{ $pct }}%</span>
                        </div>
                        <div class="penilaian-progress-bar-bg">
                            <div class="penilaian-progress-bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="penilaian-progress-label">{{ $dinilai }} / {{ $total }} dinilai</span>
                        </div>
                    </div>

                    <a href="{{ route('penilaian.tahap.2.show', $seId) }}"
                       class="btn btn-lihat-nilai w-100 mt-3">
                        <i class="bi bi-search me-2"></i>Lihat Nilai Nominasi
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
.penilaian-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.10);
    padding: 28px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}

/* ── Header ── */
.penilaian-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: var(--ri-text-primary);
}
.penilaian-subtitle {
    margin: 4px 0 0;
    color: var(--ri-text-muted);
    font-size: 0.875rem;
}

/* ── Card ── */
.penilaian-card {
    background: var(--ri-card-bg);
    border: 1px solid var(--ri-border);
    border-radius: 10px;
    overflow: hidden;
    transition: box-shadow 0.18s, transform 0.18s, background 0.2s;
}
.penilaian-card:hover {
    box-shadow: 0 6px 20px rgba(60,103,142,0.13);
    transform: translateY(-2px);
}
.penilaian-card-body { padding: 20px 22px 22px; }

.penilaian-card-top { }
.penilaian-badge-tahun {
    background: rgba(60,103,142,0.10);
    color: #3C678E;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid rgba(60,103,142,0.20);
    transition: background 0.2s, color 0.2s;
}
[data-bs-theme="dark"] .penilaian-badge-tahun {
    background: rgba(109,173,216,0.15);
    color: #6DADD8;
    border-color: rgba(109,173,216,0.25);
}
.penilaian-card-icon {
    font-size: 1.6rem;
    color: var(--ri-text-muted);
    opacity: 0.45;
}

.penilaian-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--ri-text-primary);
    margin: 0 0 4px;
    line-height: 1.4;
}
.penilaian-card-event {
    font-size: 0.78rem;
    color: var(--ri-text-muted);
    margin: 0;
}

/* ── Progress ── */
.penilaian-progress-wrap { }
.penilaian-progress-label {
    font-size: 0.75rem;
    color: var(--ri-text-muted);
}
.penilaian-progress-pct {
    font-size: 0.75rem;
    font-weight: 700;
    color: #3C678E;
}
[data-bs-theme="dark"] .penilaian-progress-pct { color: #6DADD8; }

.penilaian-progress-bar-bg {
    width: 100%;
    height: 7px;
    background: var(--ri-border);
    border-radius: 99px;
    overflow: hidden;
}
.penilaian-progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #3C678E, #6DADD8);
    border-radius: 99px;
    transition: width 0.5s ease;
}

/* ── Button Lihat Nilai ── */
.btn-lihat-nilai {
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
.btn-lihat-nilai:hover {
    background: #3C678E;
    color: #fff !important;
    border-color: #3C678E;
}
[data-bs-theme="dark"] .btn-lihat-nilai:hover {
    background: #6DADD8;
    border-color: #6DADD8;
    color: #1C2333 !important;
}
</style>
@endpush