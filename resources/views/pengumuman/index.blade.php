@extends('layouts.public')   // atau langsung pakai layout dashboard yang sudah ada

@section('title', 'Pengumuman - Rumah Inovasi')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 fw-bold">Pengumuman</h2>
        </div>
    </div>
    <div class="row">
        @forelse($buletin as $item)
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm rounded-0">
                <div class="card-body">
                    <div class="text-muted small mb-2">{{ $item->publish_date->format('d M Y') }}</div>
                    <h5 class="card-title fw-bold">{{ $item->title }}</h5>
                    <p class="card-text text-muted">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                    <a href="{{ route('public.buletin.show', $item->slug) }}" class="btn btn-link ps-0 text-primary fw-semibold">
                        Read More →
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">Belum ada pengumuman.</div>
        @endforelse
    </div>
    {{ $buletin->links() }}
</div>
@endsection
