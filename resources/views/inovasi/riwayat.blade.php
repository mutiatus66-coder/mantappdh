@extends('index', ['dummy' => true])

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
            <h3 class="ec-title">Riwayat Inovasi</h3>
            <p class="ec-subtitle">Daftar sub event dan usulan yang diajukan</p>
        </div>
    </div>

    {{-- ── CARD GRID ── --}}
    <div class="row g-4">
        @forelse($subEvents as $se)
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="ec-card h-100">
                <div class="ec-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="ec-badge-tahun">{{ $se->tahun }}</span>
                        <i class="bi bi-journal-text ec-card-icon"></i>
                    </div>
                    {{-- Nama Event parent --}}
                    <p class="ec-card-parent-label mb-0">{{ $se->event->nama_event ?? '-' }}</p>
                    {{-- Nama Sub Event — bold tebal seperti penilaian --}}
                    <h5 class="ec-card-title">{{ $se->sub_event }}</h5>
                    <div class="mt-3 mb-3">
                        <span class="ec-progress-label">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $se->mulai }} &ndash; {{ $se->berakhir }}
                        </span>
                    </div>
                    <a href="{{ url('/inovasi/usulan/' . $se->id) }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right me-1"></i> Kelola Usulan
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