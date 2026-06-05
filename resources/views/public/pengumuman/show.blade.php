@extends('layouts.public')
@section('title', $buletin->judul)
@section('content')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <article class="bg-white p-4 p-lg-5 shadow-sm rounded-0">
                <div class="mb-4 pb-2 border-bottom">
                    <h1 class="display-6 fw-bold">{{ $buletin->judul }}</h1>
                    <div class="text-muted mt-2">
                        {{ $buletin->created_at->translatedFormat('d F Y') }}
                    </div>
                </div>

                <div class="content" style="font-size: 1.05rem; line-height: 1.7;">
                    {!! $buletin->deskripsi !!}
                </div>

                {{-- Tampilkan file jika ada --}}
                @if($buletin->file_path)
                <div class="mt-4">
                    @php
                        $ext = strtolower(pathinfo($buletin->file_path, PATHINFO_EXTENSION));
                        $url = asset('storage/' . $buletin->file_path);
                    @endphp

                    {{-- Gambar --}}
                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                        <div class="mb-3">
                            <img src="{{ $url }}" 
                            alt="Lampiran" 
                            class="img-fluid rounded shadow-sm"
                            style="max-height: 500px; object-fit: contain;">
                        </div>
                        <a href="{{ $url }}" 
                        download 
                        class="btn btn-outline-primary btn-aksi">
                            Unduh Gambar
                        </a>

                    {{-- PDF --}}
                    @elseif($ext === 'pdf')
                        <div class="mb-3">
                            <iframe src="{{ $url }}" 
                            width="100%" 
                            height="600px" 
                            class="border rounded shadow-sm">
                            </iframe>
                        </div>
                        <a href="{{ $url }}" 
                        target="_blank" 
                        class="btn btn-sm btn-outline-danger rounded-0">
                        <i class="ki-outline ki-file-pdf"></i> 
                        Buka PDF
                        </a>

                    {{-- Dokumen Word --}}
                    @elseif(in_array($ext, ['doc', 'docx']))
                        <div class="alert alert-light border d-flex align-items-center gap-3">
                            <i class="ki-outline ki-file fs-2 text-primary"></i>
                            <div>
                            <div class="fw-bold">Lampiran Dokumen</div>
                            <small class="text-muted">{{ strtoupper($ext) }} File</small>
                            </div>
                            <a href="{{ $url }}" 
                            download 
                            class="btn btn-sm btn-primary rounded-0 ms-auto">
                            <i class="ki-outline ki-download"></i> 
                            Unduh
                            </a>
                        </div>
                    @endif
                </div>
                @endif

                <div class="mt-5">
                    <a href="{{ route('public.pengumuman.index') }}" 
                    class="btn btn-outline-secondary rounded-0">
                    Kembali
                    </a>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection