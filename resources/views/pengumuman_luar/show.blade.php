@extends('layouts.pengumuman_luar')

@section('title', $pengumuman->judul)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <article class="bg-white p-4 p-lg-5 shadow-lg rounded-4 border-0">
                <div class="mb-4 pb-2 border-bottom">
                    <div class="text-uppercase small text-primary fw-bold mb-2">
                        BAPERIDA KABUPATEN MAGETAN
                    </div>
                    <h1 class="display-6 fw-bold">{{ $pengumuman->judul }}</h1>
                    <div class="text-muted mt-2">
                        <i class="ki-outline ki-calendar"></i> {{ $pengumuman->created_at->translatedFormat('l, d F Y') }}
                    </div>
                </div>
                <div class="content" style="font-size: 1.05rem; line-height: 1.8; color: #334155;">
                    {!! nl2br(e($pengumuman->deskripsi)) !!}
                </div>
                @if($pengumuman->file_path)
                <div class="mt-5 p-4 bg-light rounded-3">
                    <div class="d-flex align-items-center">
                        <i class="ki-outline ki-file fs-1 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Lampiran Pengumuman</h6>
                            <small class="text-muted">Klik tombol untuk mengunduh</small>
                        </div>
                        <a href="{{ Storage::url($pengumuman->file_path) }}" class="btn btn-primary rounded-pill ms-auto" download>
                            <i class="ki-outline ki-download"></i> Unduh
                        </a>
                    </div>
                </div>
                @endif
                <div class="mt-5">
                    <a href="{{ route('pengumuman.luar.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        ← Kembali ke Daftar
                    </a>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
