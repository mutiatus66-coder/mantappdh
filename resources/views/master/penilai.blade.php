@extends('index')

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Master Penilai</h3>
            <p>Pilih sub event untuk mengatur penilai</p>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="total-badge">Total Sub Event: <span>{{ $subEvents->count() }}</span></div>
        <div class="search-box">
            <input type="text" id="searchSubEvent" class="form-control" placeholder="Cari sub event...">
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Tahun</th>
                    <th>Event</th>
                    <th>Sub Event</th>
                    <th style="text-align:center;">Jml Penilai</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelBody">
                @forelse($subEvents as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->tahun }}</td>
                    <td>{{ $item->event->nama_event ?? '-' }}</td>
                    <td>{{ $item->sub_event }}</td>
                    <td style="text-align:center;">
                        <span class="badge-kategori">{{ $item->penilai->count() }}</span>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('penilai.detail', $item->id) }}"
                           class="btn btn-primary btn-aksi">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada sub event
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchSubEvent').addEventListener('input', function () {
    const kw = this.value.toLowerCase();
    document.querySelectorAll('#tabelBody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(kw) ? '' : 'none';
    });
});
</script>
@endpush