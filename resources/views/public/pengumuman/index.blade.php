@extends('layouts.public')
@section('title', 'Pengumuman')
@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 fw-bold">Pengumuman</h2>
            <a href="{{ route('landing') }}" class="btn btn-outline-secondary rounded-0 fw-semibold">
                ← Kembali
            </a>
        </div>
    </div>
    <div class="row">
        @forelse($buletin as $item)
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm rounded-0">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        {{-- created_at, langsung bisa format --}}
                        <i class="ki-outline ki-calendar"></i> {{ $item->created_at->format('d M Y') }}
                    </div>
                    <h5 class="card-title fw-bold">{{ $item->judul }}</h5>
                    <p class="card-text text-muted">{{ Str::limit(strip_tags($item->deskripsi), 100) }}</p>
                    {{-- Pakai id karena tidak ada slug --}}
                    <a href="{{ route('public.pengumuman.show', $item->id) }}"
                    class="btn btn-link ps-0 text-primary fw-semibold text-decoration-none">
                        Selengkapnya →
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-light">Belum ada pengumuman.</div>
        </div>
        @endforelse
    </div>
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $buletin->links() }}
        </div>
    </div>
</div>
@endsection
