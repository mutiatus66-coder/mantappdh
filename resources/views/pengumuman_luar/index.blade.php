@extends('layouts.pengumuman_luar')

@section('title', 'Pengumuman')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">📢 Pengumuman Resmi</h2>
            <p class="text-muted">Informasi terbaru dari BAPERIDA Kabupaten Magetan</p>
        </div>
    </div>
    <div class="row g-4">
        @forelse($pengumuman as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-scale">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        <i class="ki-outline ki-calendar"></i> {{ $item->created_at->format('d M Y') }}
                    </div>
                    <h5 class="card-title fw-bold">{{ $item->judul }}</h5>
                    <p class="card-text text-muted">{{ Str::limit(strip_tags($item->deskripsi), 100) }}</p>
                    @if($item->file_path)
                        <span class="badge bg-light text-dark"><i class="ki-outline ki-file"></i> Lampiran</span>
                    @endif
                </div>
                <div class="card-footer bg-white border-0 pb-4 pt-0">
                    <a href="{{ route('pengumuman.luar.show', $item->id) }}" class="btn btn-link text-primary p-0 fw-semibold">
                        Baca Selengkapnya <i class="ki-outline ki-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="opacity-50 mb-3">
            <h5 class="text-muted">Belum ada pengumuman.</h5>
        </div>
        @endforelse
    </div>
    <div class="mt-5 d-flex justify-content-center">
        {{ $pengumuman->links() }}
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-scale:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 2rem rgba(0,0,0,0.1) !important;
    }
    .btn-link i {
        transition: transform 0.2s;
    }
    .btn-link:hover i {
        transform: translateX(4px);
    }
</style>
@endsection
