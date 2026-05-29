@extends('index', ['dummy' => true])

@section('content')
<div class="rekap-container">
    <div class="rekap-header">
        <div class="rekap-title">
            <h3>Rekap Nilai Inovasi</h3>
            <p>Progress penilaian per sub event</p>
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
            <a href="{{ url('/inovasi/usulan-nilai/'.$event['id']) }}" class="btn-lihat">
                Lihat Nilai <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
