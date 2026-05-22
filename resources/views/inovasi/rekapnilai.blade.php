@extends('index', ['dummy' => true])

@section('content')
<link href="{{ asset('template.demo6/demo6/assets/css/setel.css') }}" rel="stylesheet">

<div class="all-container">
    <div class="all-title">
        <h3>Rekap Nilai Inovasi</h3>
        <p>Progress penilaian per sub event</p>
    </div>

    <div class="card-grid">
        @foreach($subEvents as $event)
        @php
            preg_match('/\b(19|20)\d{2}\b/', $event['nama'], $matches);
            $year = $matches[0] ?? '';
        @endphp
        <div class="card-event">
            <div class="event-header">
                <div class="event-title">{{ $event['nama'] }}</div>
                @if($year)
                <div class="event-year">{{ $year }}</div>
                @endif
            </div>
            <a href="/inovasi/usulan-nilai/{{ $event['id'] }}" class="btn-lihat">
                Lihat Nilai <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
