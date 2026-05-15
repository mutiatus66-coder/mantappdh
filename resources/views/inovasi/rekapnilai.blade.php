@extends('index', ['dummy' => true])

@section('content')
<style>
    .rekap-container {
        padding: 28px 24px;
        margin: 20px;
    }
    .rekap-title h3 {
        font-size: 1.8rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--ri-text-primary);
        margin-bottom: 0.5rem;
    }
    .rekap-title p {
        color: var(--ri-text-muted);
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 28px;
    }
    .card-event {
        background: var(--ri-card-bg);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 32px;
        padding: 28px 24px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    [data-bs-theme="dark"] .card-event {
        border-color: rgba(255,255,255,0.06);
    }
    .card-event:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 30px -12px rgba(0,0,0,0.1);
        border-color: rgba(59,130,246,0.2);
    }
    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }
    .event-title {
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1.4;
        color: var(--ri-text-primary);
        flex: 1;
    }
    .event-year {
        background: rgba(59,130,246,0.12);
        color: #3b82f6;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 40px;
        font-size: 0.7rem;
        white-space: nowrap;
    }
    .btn-lihat {
        background: linear-gradient(105deg, #2563eb, #1e40af);
        color: white;
        padding: 10px 0;
        border-radius: 60px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(37,99,235,0.2);
    }
    .btn-lihat:hover {
        background: linear-gradient(105deg, #1d4ed8, #1e3a8a);
        transform: scale(1.02);
        box-shadow: 0 6px 12px rgba(37,99,235,0.25);
    }
    .btn-lihat i {
        font-size: 0.9rem;
        transition: transform 0.2s;
    }
    .btn-lihat:hover i {
        transform: translateX(3px);
    }
    @media (max-width: 640px) {
        .rekap-container { padding: 16px; margin: 10px; }
        .card-event { padding: 20px; }
        .event-title { font-size: 1rem; }
    }
</style>

<div class="rekap-container">
    <div class="rekap-title">
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
