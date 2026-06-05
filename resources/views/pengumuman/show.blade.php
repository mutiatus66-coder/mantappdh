@extends('layouts.public')

@section('title', $buletin->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <article class="bg-white p-4 p-lg-5 shadow-sm rounded-0">
                <div class="mb-4 pb-2 border-bottom">
                    <div class="text-uppercase small text-primary fw-bold mb-2">
                        BAPPERIDA KABUPATEN MAGETAN
                    </div>
                    <h1 class="display-6 fw-bold">{{ $buletin->title }}</h1>
                    <div class="text-muted mt-2">
                        {{ $buletin->publish_date->translatedFormat('d F Y') }}
                    </div>
                </div>
                <div class="content" style="font-size: 1.05rem; line-height: 1.7;">
                    {!! $buletin->content !!}
                </div>
                <div class="mt-5">
                    <a href="{{ route('public.buletin.index') }}" class="btn btn-outline-secondary rounded-0">
                        ← Kembali
                    </a>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
