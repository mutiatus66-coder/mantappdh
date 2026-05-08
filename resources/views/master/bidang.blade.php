@extends('index', ['dummy' => true])

@section('content')

{{-- Flash Message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#92400e;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="bidang-container">

    <div class="bidang-header">
        <div class="bidang-title">
            <h3>Master Bidang</h3>
            <p>Kelola bidang untuk setiap sub event</p>
        </div>
    </div>

    <div class="accordion" id="accordionBidang">

        @foreach($subEvents as $index => $se)
        @php
            $seId     = $se['id'];
            $rows     = $bidangData[$seId] ?? [];
            $aktif    = collect($rows)->where('status', 'aktif')->count();
            $nonaktif = collect($rows)->where('status', 'tidak_aktif')->count();
        @endphp

        <div class="accordion-item bidang-accordion-item mb-3">

            <h2 class="accordion-header" id="heading-{{ $seId }}">
                <button class="accordion-button bidang-accordion-btn fw-semibold collapsed px-4"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $seId }}"
                        aria-expanded="false"
                        aria-controls="collapse-{{ $seId }}">
                    <span class="me-2 small">Sub Event:</span>
                    <span class="fw-bold">{{ $se['sub_event'] }}</span>
                </button>
            </h2>

            <div id="collapse-{{ $seId }}"
                 class="accordion-collapse collapse"
                 aria-labelledby="heading-{{ $seId }}"
                 data-bs-parent="#accordionBidang">

                <div class="accordion-body p-4">

                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="d-flex gap-2">
                            <span class="badge-aktif rounded-pill px-3 py-2">Aktif <strong>{{ $aktif }}</strong></span>
                            <span class="badge-nonaktif rounded-pill px-3 py-2">Tidak Aktif <strong>{{ $nonaktif }}</strong></span>
                        </div>

                        <button class="btn btn-tambah-bidang"
                                data-sub-event-id="{{ $seId }}"
                                data-sub-event-nama="{{ $se['sub_event'] }}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTambahBidang">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Bidang
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table bidang-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="60" class="text-center">No</th>
                                    <th>Bidang</th>
                                    <th width="160" class="text-center">Status</th>
                                    <th width="200" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $i => $bidang)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ ucfirst($bidang['nama']) }}</td>
                                    <td class="text-center">
                                        @if($bidang['status'] === 'aktif')
                                            <span class="badge-aktif px-3 py-2">Aktif</span>
                                        @else
                                            <span class="badge-nonaktif px-3 py-2">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-gold btn-sm btn-edit-bidang me-2"
                                                data-id="{{ $bidang['id'] }}"
                                                data-sub-event-nama="{{ $se['sub_event'] }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditBidang">
                                            Ubah
                                        </button>
                                        <button class="btn btn-hapus btn-sm btn-hapus-bidang"
                                                data-id="{{ $bidang['id'] }}"
                                                data-nama="{{ $bidang['nama'] }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapusBidang">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 empty-row">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        Belum ada bidang untuk sub event ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

{{-- Modal tetap sama (Tambah, Edit, Hapus) --}}
{{-- ... masukkan modal-modal kamu di sini ... --}}

@endsection

@push('styles')
<style>
.bidang-container {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
}

.bidang-header {
    margin-bottom: 24px;
}

.bidang-title h3 {
    font-size: 1.6rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}

.bidang-title p {
    margin: 0;
    color: var(--ri-text-muted);
}

/* Accordion Style */
.bidang-accordion-item {
    background: var(--ri-card-bg) !important;
    border: 1px solid var(--ri-border) !important;
    border-radius: 8px !important;
    overflow: hidden;
    transition: background 0.2s;
}

.bidang-accordion-btn {
    background: var(--ri-accordion-head-bg) !important;
    color: var(--ri-text-primary) !important;
    font-weight: 600;
    padding: 16px 20px !important;
    transition: background 0.2s, color 0.2s;
}

.bidang-accordion-btn:not(.collapsed) {
    background: var(--ri-accordion-head-active-bg) !important;
    color: var(--ri-accordion-head-active-color) !important;
}

.accordion-body {
    background: var(--ri-card-bg);
    transition: background 0.2s;
}

/* Table */
.bidang-table {
    border: 2px solid var(--ri-table-border-outer) !important;
    border-radius: 8px;
    overflow: hidden;
}

.bidang-table th {
    background: var(--ri-table-head-bg) !important;
    padding: 14px 12px;
    border-bottom: 2.5px solid var(--ri-table-border-header) !important;
    font-weight: 600;
    color: var(--ri-text-muted) !important;
    transition: background 0.2s, color 0.2s;
}

.bidang-table td {
    padding: 14px 12px;
    border-bottom: 1.5px solid var(--ri-table-border-row) !important;
    color: var(--ri-text-primary) !important;
    background: var(--ri-table-row-bg) !important;
    transition: background 0.2s, color 0.2s;
}

.bidang-table tr:hover td {
    background: var(--ri-table-row-hover) !important;
}

.badge-aktif {
    background: #d1fae5;
    color: #166534;
    padding: 6px 14px;
    border-radius: 9999px;
    font-weight: 600;
    display: inline-block;
}

.badge-nonaktif {
    background: var(--ri-badge-inactive-bg);
    color: var(--ri-badge-inactive-color);
    padding: 6px 14px;
    border-radius: 9999px;
    font-weight: 600;
    display: inline-block;
    transition: background 0.2s, color 0.2s;
}

.btn-tambah-bidang, .btn-gold {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    color: white !important;
    border: none;
    font-weight: 600;
}

.btn-hapus {
    background: #A32D2D !important;
    color: #ffffff !important;
    border: none;
    font-weight: 600;
}

.btn-hapus:hover {
    background: #8b2424 !important;
    color: #ffffff !important;
}

.empty-row {
    color: var(--ri-text-muted) !important;
    background: var(--ri-table-row-bg) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Paksa semua accordion tertutup setelah halaman load
    setTimeout(() => {
        const collapses = document.querySelectorAll('#accordionBidang .accordion-collapse');
        collapses.forEach(collapse => {
            if (collapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(collapse, { toggle: false });
                bsCollapse.hide();
            }
        });
    }, 100);

    document.querySelectorAll('.btn-tambah-bidang').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('tambah-sub-event-id').value = this.dataset.subEventId;
            document.getElementById('tambah-sub-event-nama').value = this.dataset.subEventNama;
        });
    });

    // ... tambahkan script edit & hapus kamu di sini ...
});
</script>
@endpush