@extends('index', ['dummy' => true])

@section('content')
<style>
.rekap-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin: 20px;
}
.rekap-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.rekap-title h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}
.rekap-title p {
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
    transition: 0.2s;
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
}
.btn-lihat-usulan {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
}
.btn-lihat-usulan:hover { opacity: 0.85; }
.empty-row { text-align: center; padding: 40px; color: var(--ri-text-muted); }
</style>

<div class="rekap-container">
    <div class="rekap-header">
        <div class="rekap-title">
            <h3>Rekap Nilai Inovasi</h3>
            <p>Pilih event untuk melihat nilai tahap 1, tahap 2, dan total</p>
        </div>
        <a href="/inovasi/riwayat" class="btn-kembali">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="event-grid">
        @forelse($subEvents as $event)
        <div class="event-card">
            <div class="event-title">{{ $event['nama'] }}</div>
            <a href="/inovasi/usulan-nilai/{{ $event['id'] }}" class="btn-lihat-usulan">
                Lihat Usulan
            </a>
        </div>
        @empty
        <div class="empty-row">Belum ada event</div>
        @endforelse
    </div>
</div>
@endsection
