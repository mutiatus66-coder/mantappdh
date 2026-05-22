@extends('index', ['dummy' => true])

@section('content')

{{-- Flash Message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(0,172,193,0.10); border:1px solid rgba(0,172,193,0.3); color:#006064; margin: 0 20px;">
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

                        <button class="btn btn-primary"
                                data-sub-event-id="{{ $seId }}"
                                data-sub-event-nama="{{ $se['sub_event'] }}">
                            Tambah Bidang
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
                                        <button class="btn btn-warning"
                                                data-id="{{ $bidang['id'] }}"
                                                data-nama="{{ $bidang['nama'] }}"
                                                data-status="{{ $bidang['status'] }}"
                                                data-sub-event-id="{{ $seId }}"
                                                data-sub-event-nama="{{ $se['sub_event'] }}">
                                            Ubah
                                        </button>
                                        <button class="btn btn-danger"
                                                data-id="{{ $bidang['id'] }}"
                                                data-nama="{{ $bidang['nama'] }}"
                                                data-url="{{ route('bidang.destroy', $bidang['id']) }}">
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


{{-- ══ MODAL — Tambah / Ubah Bidang ══ --}}
<div class="modal fade" id="modalBidang" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formBidang" method="POST" action="{{ route('bidang.store') }}">
                @csrf
                <input type="hidden" name="_method"      id="formBidangMethod"   value="POST">
                <input type="hidden" name="sub_event_id" id="bidangSubEventId">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalBidangTitle">Tambah Bidang</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">

                    <p class="mb-3" style="font-size:0.85rem; color:var(--ri-text-muted);">
                        Sub Event: <strong id="bidangSubEventNama" style="color:var(--ri-text-primary);"></strong>
                    </p>

                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Nama Bidang</label>
                        <input type="text" name="nama" id="bidangNama"
                               class="form-control" placeholder="Masukkan nama bidang..." required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Status</label>
                        <div class="d-flex gap-4 mt-1">
                            <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="status" id="statusAktifBidang" value="aktif" checked> Aktif
                            </label>
                            <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="status" id="statusNonaktifBidang" value="tidak_aktif"> Tidak Aktif
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Bidang ══ --}}
<div class="modal fade" id="modalHapusBidang" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Bidang
                <strong id="namaBidangHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusBidang" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>


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
.bidang-header { margin-bottom: 24px; }
.bidang-title h3 {
    font-size: 1.6rem;
    font-weight: bold;
    margin: 0;
    color: var(--ri-text-primary);
}
.bidang-title p { margin: 0; color: var(--ri-text-muted); }

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
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
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
.bidang-table tr:hover td { background: var(--ri-table-row-hover) !important; }
.badge-aktif {
    background: #d1fae5; color: #166534;
    padding: 6px 14px; border-radius: 9999px;
    font-weight: 600; display: inline-block;
}
.badge-nonaktif {
    background: var(--ri-badge-inactive-bg);
    color: var(--ri-badge-inactive-color);
    padding: 6px 14px; border-radius: 9999px;
    font-weight: 600; display: inline-block;
    transition: background 0.2s, color 0.2s;
}

/* Hapus modal */
.hapus-icon-circle {
    width: 56px; height: 56px; border-radius: 50%;
    background: #FCEBEB;
    display: flex; align-items: center; justify-content: center;
}
[data-bs-theme="dark"] .hapus-icon-circle  { background: rgba(163,45,45,0.20); }
[data-bs-theme="dark"] .hapus-teks-muted   { color: rgba(245,240,232,.55) !important; }
[data-bs-theme="dark"] .hapus-nama-strong  { color: #F5F0E8 !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl = "{{ route('bidang.store') }}";

    // Paksa accordion tertutup
    setTimeout(() => {
        document.querySelectorAll('#accordionBidang .accordion-collapse').forEach(c => {
            if (c.classList.contains('show')) new bootstrap.Collapse(c, { toggle: false }).hide();
        });
    }, 100);

    // ── Reset modal ──
    document.getElementById('modalBidang').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formBidang').action      = storeUrl;
        document.getElementById('formBidangMethod').value = 'POST';
        document.getElementById('modalBidangTitle').textContent = 'Tambah Bidang';
        document.getElementById('bidangNama').value        = '';
        document.getElementById('bidangSubEventId').value  = '';
        document.getElementById('bidangSubEventNama').textContent = '';
        document.getElementById('statusAktifBidang').checked = true;
    });

    // ── Tambah ──
    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('bidangSubEventId').value           = this.dataset.subEventId;
            document.getElementById('bidangSubEventNama').textContent   = this.dataset.subEventNama;
            document.getElementById('modalBidangTitle').textContent     = 'Tambah Bidang';
            document.getElementById('formBidang').action                = storeUrl;
            document.getElementById('formBidangMethod').value           = 'POST';
            document.getElementById('bidangNama').value                 = '';
            document.getElementById('statusAktifBidang').checked        = true;
            new bootstrap.Modal(document.getElementById('modalBidang')).show();
        });
    });

    // ── Ubah ──
    document.querySelectorAll('.btn-warning').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;

            document.getElementById('modalBidangTitle').textContent     = 'Ubah Bidang';
            document.getElementById('formBidang').action                = `/bidang/${id}`;
            document.getElementById('formBidangMethod').value           = 'PUT';
            document.getElementById('bidangSubEventId').value           = this.dataset.subEventId;
            document.getElementById('bidangSubEventNama').textContent   = this.dataset.subEventNama;
            document.getElementById('bidangNama').value                 = this.dataset.nama;

            if (this.dataset.status === 'tidak_aktif') {
                document.getElementById('statusNonaktifBidang').checked = true;
            } else {
                document.getElementById('statusAktifBidang').checked = true;
            }

            new bootstrap.Modal(document.getElementById('modalBidang')).show();
        });
    });

    // ── Hapus ──
    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaBidangHapus').textContent  = this.dataset.nama;
            document.getElementById('formHapusBidang').action       = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusBidang')).show();
        });
    });

});
</script>
@endpush