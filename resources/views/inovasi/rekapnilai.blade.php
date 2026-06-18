@extends('index')
@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush
@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(245,158,11,0.10); border:1px solid rgba(245,158,11,0.3); color:#92400e; margin: 0 20px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<div class="page-container">
    {{-- ── HEADER ── --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="ec-title">Rekap Nilai Inovasi</h3>
            <p class="ec-subtitle">Progress penilaian per sub event</p>
        </div>
    </div>
    {{-- ── CARD GRID ── --}}
    <div class="row g-4">
        @forelse($subEvents as $se)
        @php
            $total   = $se->inovasi_count  ?? 0;
            $dinilai = $se->dinilai_count  ?? 0;
            $pct     = $total > 0 ? round($dinilai / $total * 100) : 0;

            // Admin bapperida & penilai → rekap semua pendaftar
            // Peserta → nilai usulan milik sendiri
            $isAdmin = in_array(Auth::user()->hak_akses, ['admin_bapperida', 'penilai']);
            $linkNilai = $isAdmin
                ? url('/inovasi/rekap-pendaftar/' . $se->id)
                : url('/inovasi/usulan-nilai/' . $se->id);
        @endphp
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="ec-card h-100">
                <div class="ec-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="ec-badge-tahun">{{ $se->tahun }}</span>
                        <i class="bi bi-bar-chart-line ec-card-icon"></i>
                    </div>
                    {{-- Nama Event parent --}}
                    <p class="ec-card-parent-label mb-0">{{ $se->event->nama_event ?? '-' }}</p>
                    {{-- Nama Sub Event --}}
                    <h5 class="ec-card-title">{{ $se->sub_event }}</h5>
                    <div class="mt-3 mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="ec-progress-label">Progress Penilaian</span>
                            <span class="ec-progress-pct">{{ $pct }}%</span>
                        </div>
                        <div class="ec-progress-bar-bg">
                            <div class="ec-progress-bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="mt-1">
                            <span class="ec-progress-label">{{ $dinilai }} / {{ $total }} dinilai</span>
                        </div>
                    </div>
                    <a href="{{ $linkNilai }}" class="btn btn-info">
                        <i class="bi bi-bar-chart me-1"></i> Lihat Nilai
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted text-center py-5">Belum ada sub event tersedia.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection