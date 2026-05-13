@extends('index', ['dummy' => true])

@section('content')
<style>
    /* Gaya sama persis dengan penilai, ditambah grid card */
    .riwayat-container {
        background: var(--ri-card-bg);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
        transition: background 0.2s, color 0.2s;
    }
    .riwayat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .riwayat-title h3 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
        color: var(--ri-text-primary);
    }
    .riwayat-title p {
        margin: 0;
        color: var(--ri-text-muted);
        font-size: 0.875rem;
    }
    .btn-kembali {
        background: #6c757d;
        color: white;
        padding: 6px 14px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
    }
    .event-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .event-card {
        background: var(--ri-card-bg);
        border: 1px solid var(--ri-border);
        border-radius: 12px;
        padding: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .event-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--ri-text-primary);
        margin-bottom: 12px;
        line-height: 1.4;
    }
    .btn-lihat-usulan {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: opacity 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-lihat-usulan:hover {
        opacity: 0.85;
    }
    .empty-row {
        text-align: center;
        padding: 40px;
        color: var(--ri-text-muted);
        background: var(--ri-table-row-bg);
        border-radius: 12px;
    }
    @media (max-width: 640px) {
        .riwayat-container { margin: 10px; padding: 12px; }
        .event-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="riwayat-container">
    <div class="riwayat-header">
        <div class="riwayat-title">
            <h3>Riwayat Inovasi</h3>
            <p>Pilih event untuk melihat usulan inovasi</p>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="btn-kembali">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="event-grid">
        @forelse($subEvents as $event)
        <div class="event-card">
            <div class="event-title">{{ $event['nama'] }}</div>
            <a href="/inovasi/usulan-riwayat/{{ $event['id'] }}" class="btn-lihat-usulan">
                Lihat Usulan
            </a>
        </div>
        @empty
        <div class="empty-row">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Belum ada event / sub event.
        </div>
        @endforelse
    </div>
</div>
@endsection
