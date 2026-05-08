@extends('index', ['dummy' => true])

@section('content')

<style>
/* ════════════════════════════════
   SUB-EVENT PAGE - Dark/Light Theme Aware
════════════════════════════════ */

.sub-event-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}

.sub-event-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}

.sub-event-title h3 {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}

.sub-event-title p {
    margin: 0;
    color: var(--ri-text-muted);
    font-size: 0.875rem;
}

.btn-tambah-se {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-tambah-se:hover {
    opacity: 0.9;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.sub-event-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}

.total-badge {
    background: #fef3c7;
    color: #92400e;
    padding: 6px 16px;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.search-box input {
    padding: 8px 12px 8px 36px;
    border: 1px solid var(--ri-border);
    border-radius: 8px;
    font-size: 0.875rem;
    width: 260px;
    background: var(--ri-input-bg);
    color: var(--ri-text-primary);
    transition: background 0.2s, color 0.2s, border-color 0.2s;
}

/* ================== TABEL DENGAN GARIS TEBAL ================== */
.se-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid var(--ri-table-border-outer);
    border-radius: 8px;
    overflow: hidden;
}

.se-table th {
    background: var(--ri-table-head-bg);
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ri-text-muted);
    border-bottom: 2px solid var(--ri-table-border-header);
    transition: background 0.2s, color 0.2s;
}

.se-table td {
    padding: 14px 12px;
    border-bottom: 1.5px solid var(--ri-table-border-row);
    color: var(--ri-text-primary);
    font-size: 0.875rem;
    background: var(--ri-table-row-bg);
    transition: background 0.2s, color 0.2s;
}

.se-table tr:hover td {
    background: var(--ri-table-row-hover);
}

.se-table tr:last-child td {
    border-bottom: none;
}

/* Badges & Buttons */
.badge-kategori {
    font-weight: 600;
    font-size: 0.75rem;
    padding: .35em .75em;
    border-radius: 9999px;
    white-space: nowrap;
    background: #fef3c7;
    color: #92400e;
}

.btn-gold, .btn-edit {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-hapus {
    background: #A32D2D;
    color: #ffffff !important;
    border: none;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: background 0.15s;
}

.btn-hapus:hover {
    background: #8b2424;
    color: #ffffff !important;
}

.empty-row {
    text-align: center;
    padding: 40px 20px;
    color: var(--ri-text-muted);
    background: var(--ri-table-row-bg);
}
</style>

<div class="sub-event-container">
    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data Sub Event</h3>
            <p>Kelola semua sub event yang tersedia</p>
        </div>
        <button class="btn-tambah-se" data-bs-toggle="modal" data-bs-target="#modalSubEvent">
            <i class="bi bi-plus-lg"></i> Tambah Sub Event
        </button>
    </div>

    <div class="sub-event-stats">
        <div class="total-badge">
            Total Sub Event: <span id="totalSubEvent">{{ count($subEvents ?? []) }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchSubEvent" placeholder="Cari sub event...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Tahun</th>
                    <th>Event</th>
                    <th>Sub Event</th>
                    <th>Kategori</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Berakhir</th>
                    <th width="180" style="text-align: center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelSubEventBody">
                @forelse($subEvents as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['tahun'] }}</td>
                        <td>{{ $item['event'] }}</td>
                        <td>{{ $item['sub_event'] }}</td>
                        <td><span class="badge-kategori">{{ $item['kategori'] ?: '-' }}</span></td>
                        <td>{{ $item['mulai'] }}</td>
                        <td>{{ $item['berakhir'] }}</td>
                        <td style="text-align: center">
                            <button class="btn-gold btn-sm btn-edit me-2"
                                    data-id="{{ $item['id'] }}">
                                ✏️ Edit
                            </button>

                            <form action="{{ route('admin.sub-event.destroy', $item['id']) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-hapus"
                                        onclick="return confirm('Hapus data ini?')">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-row">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Belum ada data sub event
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="modalSubEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formSubEvent" method="POST" action="{{ route('admin.sub-event.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalTitle">Tambah Sub Event</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Tahun</label>
                            <input type="number" name="tahun" id="tahun" class="form-control" placeholder="cth. 2025" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Event</label>
                            <select name="event" id="event" class="form-select" required>
                                <option value="">-- Pilih Event --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event }}">{{ $event }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Sub Event</label>
                            <input type="text" name="sub_event" id="sub_event" class="form-control" placeholder="Nama sub event" required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Kategori</label>
                            <input type="text" name="kategori" id="kategori" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Tanggal Mulai</label>
                            <input type="date" name="mulai" id="mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Tanggal Berakhir</label>
                            <input type="date" name="berakhir" id="berakhir" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchSubEvent');
    const rows = document.querySelectorAll('#tabelSubEventBody tr');
    const totalSpan = document.getElementById('totalSubEvent');

    searchInput.addEventListener('keyup', function() {
        let keyword = this.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.classList.contains('empty-row')) return;
            const text = row.textContent.toLowerCase();
            if (text.includes(keyword)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        totalSpan.textContent = visibleCount;
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', async function () {
            const id = this.dataset.id;
            // ... (kode fetch edit)
        });
    });
});
</script>

@endsection