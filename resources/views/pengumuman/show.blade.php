@extends('layouts.public')

@section('title', $pengumuman->judul)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <article class="bg-white p-4 p-lg-5 shadow-sm">

                <div class="mb-4 pb-2 border-bottom">
                    <div class="text-uppercase small text-primary fw-bold mb-2">
                        BAPPERIDA KABUPATEN MAGETAN
                    </div>
                    <h1 class="display-6 fw-bold">{{ $pengumuman->judul }}</h1>
                    <div class="text-muted mt-2">
                        {{ $pengumuman->created_at->format('d F Y') }}
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-4" style="font-size: 1.05rem; line-height: 1.7;">
                    {!! nl2br(e($pengumuman->deskripsi)) !!}
                </div>

                {{-- Lampiran Gambar --}}
                @if($pengumuman->file_path)
                    <div class="mt-4 p-3 border rounded">
                        <p class="fw-semibold mb-2">
                            <i class="bi bi-paperclip me-1"></i> Lampiran
                        </p>

                        @php
                            $ext = strtolower(pathinfo($pengumuman->file_path, PATHINFO_EXTENSION));
                            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                        @endphp

                        @if(in_array($ext, $imageExts))
                            {{-- Tampilkan gambar langsung --}}
                            <img src="{{ asset('storage/' . $pengumuman->file_path) }}"
                                 alt="Lampiran {{ $pengumuman->judul }}"
                                 class="img-fluid rounded mb-3"
                                 style="max-height: 500px; object-fit: contain;">
                            <br>
                            <a href="{{ asset('storage/' . $pengumuman->file_path) }}"
                               download class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Unduh Gambar
                            </a>
                        @else
                            {{-- File bukan gambar (PDF, dll) --}}
                            <a href="{{ asset('storage/' . $pengumuman->file_path) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-arrow-down me-1"></i> Unduh Lampiran
                            </a>
                        @endif
                    </div>
                @endif

                <div class="mt-5">
                    <a href="{{ route('public.pengumuman.index') }}"
                       class="btn btn-outline-secondary">
                        ← Kembali
                    </a>
                </div>

            </article>
        </div>
    </div>
</div>
@endsection