@extends('index', ['dummy' => true])

@section('content')
<div class="usulan-container">
    <div class="usulan-header">
        <div class="usulan-title">
            <h3>DATA RIWAYAT</h3>
            <p>Sub Event: <strong>{{ $subEventNama }}</strong></p>
        </div>
        <div>
            <a href="{{ url('/inovasi/riwayat') }}" class="btn-kembali">← Kembali</a>
        </div>
    </div>

    <!-- Search box -->
    <div class="search-box">
        <label>Cari:</label>
        <input type="text" id="searchUsulan" placeholder="Nama inovasi atau inovator...">
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="usulan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Status</th>
                    <th>Inovator</th>
                    <th>Nama Inovasi</th>
                    <th>Nama Tim</th>
                    <th>Nama Ketua</th>
                    <th>Email Ketua</th>
                    <th>No WA Ketua</th>
                </tr>
            </thead>
            <tbody id="usulanBody">
                @forelse($usulan as $index => $u)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="status-badge">{{ $u['status'] }}</span></td>
                    <td>{{ $u['inovator'] }}</td>
                    <td>{{ $u['nama_inovasi'] }}</td>
                    <td>{{ $u['nama_tim'] }}</td>
                    <td>{{ $u['ketua_nama'] }}</td>
                    <td>{{ $u['ketua_email'] }}</td>
                    <td>{{ $u['ketua_wa'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">Belum ada usulan untuk sub event ini</td
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchUsulan');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                let keyword = this.value.toLowerCase();
                let rows = document.querySelectorAll('#usulanBody tr');
                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
</script>
@endsection
