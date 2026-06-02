@extends('index', ['dummy' => true])

@section('content')
<div class="riwayat-container">
    <div class="riwayat-header">
        <div class="riwayat-title">
            <h3>Riwayat Inovasi</h3>
            <p>Daftar sub event dan usulan yang diajukan</p>
        </div>
    </div>

    <div class="card-grid">
        @foreach($subEvents as $event)
        <div class="card-event">
            <div class="event-header">
                <div class="event-title">{{ $event['nama'] }}</div>
                <div class="event-year">{{ $event['tahun'] ?? '' }}</div>
            </div>
            <div class="event-desc">{{ $event['nama'] }}</div>
            <a href="{{ url('/inovasi/usulan-riwayat/'.$event['id']) }}" class="btn-lihat">
                Lihat Usulan <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
