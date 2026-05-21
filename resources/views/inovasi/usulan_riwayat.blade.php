@extends('index', ['dummy' => true])

@section('content')
<style>
    .usulan-container {
        background: var(--ri-card-bg);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin: 20px;
    }
    .usulan-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .usulan-title h3 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
        color: var(--ri-text-primary);
    }
    .usulan-title p {
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
    .search-box {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }
    .search-box input {
        padding: 6px 12px;
        border: 1px solid var(--ri-border);
        border-radius: 8px;
        width: 250px;
        background: var(--ri-input-bg);
        color: var(--ri-text-primary);
    }
    .usulan-table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid var(--ri-table-border-outer);
        border-radius: 8px;
        overflow: hidden;
    }
    .usulan-table th {
        background: var(--ri-table-head-bg);
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--ri-text-muted);
        border-bottom: 2px solid var(--ri-table-border-header);
    }
    .usulan-table td {
        padding: 12px;
        border-bottom: 1px solid var(--ri-table-border-row);
        color: var(--ri-text-primary);
        font-size: 0.875rem;
        background: var(--ri-table-row-bg);
    }
    .usulan-table tr:hover td {
        background: var(--ri-table-row-hover);
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #fef3c7;
        color: #92400e;
    }
    .empty-row {
        text-align: center;
        padding: 30px;
        color: var(--ri-text-muted);
    }
</style>

<div class="usulan-container">
    <div class="usulan-header">
        <div class="usulan-title">
            <h3>DATA RIWAYAT</h3>
            <p>Sub Event: <strong>{{ $subEventNama }}</strong></p>
        </div>
        <a href="/inovasi/riwayat" class="btn-kembali">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="search-box">
        <input type="text" id="searchUsulan" placeholder="Cari nama inovasi atau inovator...">
    </div>

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
                    <td colspan="8" class="empty-row">Belum ada usulan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('searchUsulan').addEventListener('keyup', function() {
        let keyword = this.value.toLowerCase();
        let rows = document.querySelectorAll('#usulanBody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
</script>
@endsection
