@extends('layouts.public')

@section('title', 'Pengumuman - Rumah Inovasi')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 fw-bold">Pengumuman</h2>
        </div>
    </div>
    <div class="row">
        @forelse($pengumuman as $item)
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        {{ $item->created_at->format('d M Y') }}
                    </div>
                    <h5 class="card-title fw-bold">{{ $item->judul }}</h5>
                    <p class="card-text text-muted">
                        {{ Str::limit(strip_tags($item->deskripsi), 100) }}
                    </p>
                    <a href="{{ route('public.pengumuman.show', $item->id) }}"
                       class="btn btn-link ps-0 text-primary fw-semibold">
                        Selengkapnya →
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-muted">Belum ada pengumuman.</div>
        @endforelse
    </div>
    {{ $pengumuman->links() }}
</div>
@endsection