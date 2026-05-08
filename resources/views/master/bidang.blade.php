@extends('index', ['dummy' => true])

@section('content')

{{-- ══════════════════════════════════════════════════
     Flash messages
══════════════════════════════════════════════════ --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.35);color:#7A5A1E;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════════════
     Page header
══════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0 bidang-page-title">Master Bidang</h4>
        <small class="bidang-page-subtitle">Kelola bidang untuk setiap sub event</small>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     Accordion — one panel per sub-event
══════════════════════════════════════════════════ --}}
<div class="accordion" id="accordionBidang">

    @foreach($subEvents as $index => $se)
    @php
        $seId     = $se['id'];
        $rows     = $bidangData[$seId] ?? [];
        $aktif    = collect($rows)->where('status', 'aktif')->count();
        $nonaktif = collect($rows)->where('status', 'tidak_aktif')->count();
        $isOpen   = request('open') == $seId || ($index === 0 && !request()->has('open'));
    @endphp

    <div class="accordion-item ri-accordion-item mb-3" id="panel-{{ $seId }}">

        {{-- ─── Accordion header ─── --}}
        <h2 class="accordion-header" id="heading-{{ $seId }}">
            <button
                class="accordion-button ri-accordion-btn fw-semibold {{ $isOpen ? '' : 'collapsed' }} px-4"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse-{{ $seId }}"
                aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                aria-controls="collapse-{{ $seId }}"
            >
                <span class="ri-acc-label me-2 small">Sub Event:</span>
                <span class="ri-acc-value">{{ $se['sub_event'] }}</span>
            </button>
        </h2>

        {{-- ─── Accordion body ─── --}}
        <div
            id="collapse-{{ $seId }}"
            class="accordion-collapse {{ $isOpen ? 'show' : '' }}"
            aria-labelledby="heading-{{ $seId }}"
            data-bs-parent="#accordionBidang"
        >
            <div class="accordion-body px-4 pb-4 pt-3">

                {{-- Stats + Add button --}}
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <span class="badge-aktif rounded-pill px-3 py-2 fs-6">
                            Bidang Aktif &nbsp;<strong>{{ $aktif }}</strong>
                        </span>
                        <span class="badge-nonaktif rounded-pill px-3 py-2 fs-6">
                            Tidak Aktif &nbsp;<strong>{{ $nonaktif }}</strong>
                        </span>
                    </div>

                    <button
                        type="button"
                        class="btn btn-tambah-bidang"
                        data-sub-event-id="{{ $seId }}"
                        data-sub-event-nama="{{ $se['sub_event'] }}"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahBidang"
                    >
                        <i class="bi bi-plus-lg me-1"></i> Tambah Bidang
                    </button>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table ri-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60"  class="text-center ps-3">No</th>
                                <th>Bidang</th>
                                <th width="160" class="text-center">Status</th>
                                <th width="200" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $i => $bidang)
                            <tr>
                                <td class="text-center ps-3">{{ $i + 1 }}</td>
                                <td>{{ ucfirst($bidang['nama']) }}</td>
                                <td class="text-center">
                                    @if($bidang['status'] === 'aktif')
                                        <span class="badge-aktif px-3 py-2" style="border-radius:30px;">Aktif</span>
                                    @else
                                        <span class="badge-nonaktif px-3 py-2" style="border-radius:30px;">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <button
                                        type="button"
                                        class="btn btn-gold btn-sm btn-edit-bidang me-1"
                                        data-id="{{ $bidang['id'] }}"
                                        data-sub-event-nama="{{ $se['sub_event'] }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditBidang"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-hapus btn-sm btn-hapus-bidang"
                                        data-id="{{ $bidang['id'] }}"
                                        data-nama="{{ $bidang['nama'] }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapusBidang"
                                    >
                                        <i class="bi bi-trash3 me-1"></i>Hapus
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 se-empty">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
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


{{-- ══════════════════════════════════════════════════
     MODAL — Tambah Bidang
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahBidang" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold bidang-modal-title" id="modalTambahLabel">Tambah Bidang</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.bidang.store') }}">
                @csrf
                <div class="modal-body px-5 py-4">
                    <input type="hidden" name="sub_event_id" id="tambah-sub-event-id">
                    <div class="mb-4 row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-end fw-semibold ri-label">Nama Sub Event</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input-readonly" id="tambah-sub-event-nama" readonly>
                        </div>
                    </div>
                    <div class="mb-4 row align-items-center">
                        <label for="tambah-nama" class="col-sm-4 col-form-label text-sm-end fw-semibold ri-label">
                            Nama Bidang <span style="color:#ff6b6b;">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input" id="tambah-nama" name="nama"
                                   placeholder="Masukkan nama bidang" required>
                        </div>
                    </div>
                    <div class="mb-1 row align-items-center">
                        <label for="tambah-status" class="col-sm-4 col-form-label text-sm-end fw-semibold ri-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-select ri-input" id="tambah-status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modal-save px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL — Edit Bidang
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditBidang" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold bidang-modal-title" id="modalEditLabel">Edit Bidang</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <form method="POST" id="formEditBidang">
                @csrf
                @method('PUT')
                <div class="modal-body px-5 py-4">
                    <div class="mb-4 row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-end fw-semibold ri-label">Nama Sub Event</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input-readonly" id="edit-sub-event-nama" readonly>
                        </div>
                    </div>
                    <div class="mb-4 row align-items-center">
                        <label for="edit-nama" class="col-sm-4 col-form-label text-sm-end fw-semibold ri-label">
                            Nama Bidang <span style="color:#ff6b6b;">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input" id="edit-nama" name="nama" required>
                        </div>
                    </div>
                    <div class="mb-1 row align-items-center">
                        <label for="edit-status" class="col-sm-4 col-form-label text-sm-end fw-semibold ri-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-select ri-input" id="edit-status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modal-save px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL — Konfirmasi Hapus
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalHapusBidang" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" style="color:#ff6b6b;" id="modalHapusLabel">
                    <i class="bi bi-exclamation-triangle me-1"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <div class="modal-body px-5">
                <p class="mb-0 hapus-body-text">
                    Yakin ingin menghapus bidang
                    <strong id="hapus-nama-preview" style="color:#ff6b6b;"></strong>?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-modal-cancel btn-sm" data-bs-dismiss="modal">Batal</button>
                <form method="POST" id="formHapusBidang">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-hapus btn-sm px-3">
                        <i class="bi bi-trash3 me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@push('styles')
<style>
/* ══════════════════════════════════════════════════
   BIDANG PAGE — Aligned with Sub Event color scheme
══════════════════════════════════════════════════ */

/* ── Page title ── */
[data-bs-theme="light"] .bidang-page-title {
    color: #2A2C2B !important;
    text-shadow: none;
    font-size: 1.75rem !important;
}
[data-bs-theme="dark"] .bidang-page-title {
    color: #F5F0E8 !important;
    text-shadow: 0 1px 4px rgba(0,0,0,.35);
    font-size: 1.75rem !important;
}
[data-bs-theme="light"] .bidang-page-subtitle {
    color: rgba(42,44,43,.55) !important;
    font-size: 1rem !important;
}
[data-bs-theme="dark"] .bidang-page-subtitle {
    color: rgba(201,168,76,.75) !important;
    font-size: 1rem !important;
}

/* ── Accordion shell ── */
[data-bs-theme="light"] .ri-accordion-item {
    background: #E3E3E3;
    border: none !important;
    border-radius: 14px !important;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.10);
}
[data-bs-theme="dark"] .ri-accordion-item {
    background: #374140;
    border: none !important;
    border-radius: 14px !important;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.18);
}

/* ── Accordion button (mirrors .card-header) ── */
[data-bs-theme="light"] .ri-accordion-btn {
    background: #E5DFC5 !important;
    color: #2A2C2B !important;
    border-bottom: 1px solid rgba(201,168,76,.25) !important;
    box-shadow: none !important;
}
[data-bs-theme="light"] .ri-accordion-btn.collapsed {
    border-bottom: none !important;
}
[data-bs-theme="dark"] .ri-accordion-btn {
    background: #2A2C2B !important;
    color: #E3E3E3 !important;
    border-bottom: 1px solid rgba(201,168,76,.20) !important;
    box-shadow: none !important;
}
[data-bs-theme="dark"] .ri-accordion-btn.collapsed {
    border-bottom: none !important;
}

/* chevron icon tint */
[data-bs-theme="light"] .ri-accordion-btn::after { filter: none; }
[data-bs-theme="dark"]  .ri-accordion-btn::after { filter: invert(1) sepia(1) saturate(2) hue-rotate(5deg); }

/* ── Sub Event label inside button ── */
[data-bs-theme="light"] .ri-acc-label { color: rgba(42,44,43,.50); font-weight: 400; }
[data-bs-theme="dark"]  .ri-acc-label { color: rgba(201,168,76,.55); font-weight: 400; }
[data-bs-theme="light"] .ri-acc-value { color: #2A2C2B; font-weight: 600; }
[data-bs-theme="dark"]  .ri-acc-value { color: #E3E3E3; font-weight: 600; }

/* ── Accordion body (mirrors .card-body) ── */
[data-bs-theme="light"] .accordion-body { background: #E3E3E3; }
[data-bs-theme="dark"]  .accordion-body { background: #374140; }

/* ── Status badges ── */
.badge-aktif {
    display: inline-block;
    font-size: .78rem;
    font-weight: 600;
}
[data-bs-theme="light"] .badge-aktif {
    background: rgba(20,180,100,.12);
    color: #0E7A4A;
    border: 1px solid rgba(20,180,100,.30);
}
[data-bs-theme="dark"] .badge-aktif {
    background: rgba(20,180,100,.18);
    color: #4AE09A;
    border: 1px solid rgba(20,180,100,.30);
}
.badge-nonaktif {
    display: inline-block;
    font-size: .78rem;
    font-weight: 600;
}
[data-bs-theme="light"] .badge-nonaktif {
    background: rgba(0,0,0,.06);
    color: #666;
    border: 1px solid rgba(0,0,0,.12);
}
[data-bs-theme="dark"] .badge-nonaktif {
    background: rgba(255,255,255,.08);
    color: rgba(245,240,232,.60);
    border: 1px solid rgba(255,255,255,.15);
}

/* ── Tambah Bidang button — matches .btn-tambah-se ── */
.btn-tambah-bidang {
    background: linear-gradient(135deg, #2B5987 100%);
    color: #E3E3E3 !important;
    font-weight: 700;
    border: none;
    border-radius: .5rem;
    padding: .45rem 1.1rem;
    font-size: .875rem;
    box-shadow: 0 3px 12px rgba(43,89,135,.28);
    transition: opacity .18s, box-shadow .18s;
}
.btn-tambah-bidang:hover {
    opacity: .88;
    box-shadow: 0 5px 18px rgba(43,89,135,.40);
    color: #2B5987 !important;
}

/* ── Edit button — matches .btn-gold from sub-event ── */
.btn-gold {
    background: linear-gradient(135deg, #2B5987 100%);
    color: #E3E3E3 !important;
    font-weight: 700;
    border: none;
    border-radius: .375rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    letter-spacing: .02em;
    box-shadow: 0 2px 8px rgba(43,89,135,.28);
    transition: opacity .18s, box-shadow .18s;
}
.btn-gold:hover {
    opacity: .88;
    box-shadow: 0 4px 14px rgba(43,89,135,.40);
    color: #0D1B3E !important;
}

/* ── Hapus button ── */
.btn-hapus {
    border-radius: .375rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    font-weight: 600;
}

/* ── Table — mirrors .se-table ── */
.ri-table { border-collapse: separate; border-spacing: 0; }

[data-bs-theme="light"] .ri-table thead th {
    background: rgba(201,168,76,.10);
    color: #2A2C2B;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 2px solid rgba(201,168,76,.30);
    padding: 10px 12px;
}
[data-bs-theme="dark"] .ri-table thead th {
    background: rgba(201,168,76,.08);
    color: #E3E3E3;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 1px solid rgba(201,168,76,.22);
    padding: 10px 12px;
}

[data-bs-theme="light"] .ri-table tbody td {
    color: #374140;
    border-bottom: 1px solid rgba(201,168,76,.12);
    background: transparent;
    padding: 10px 12px;
}
[data-bs-theme="dark"] .ri-table tbody td {
    color: rgba(245,240,232,.82);
    border-bottom: 1px solid rgba(201,168,76,.08);
    background: transparent;
    padding: 10px 12px;
}

[data-bs-theme="light"] .ri-table tbody tr:hover td { background: rgba(201,168,76,.06); }
[data-bs-theme="dark"]  .ri-table tbody tr:hover td { background: rgba(201,168,76,.05); }

/* empty row */
[data-bs-theme="light"] .se-empty { color: #999; }
[data-bs-theme="dark"]  .se-empty { color: rgba(245,240,232,.35); }

/* ── Modal backdrop ── */
.modal-backdrop.show { opacity: .35; }

/* ── Modal content ── */
[data-bs-theme="light"] .modal-content {
    background: #FDFAF3;
    border: none;
}
[data-bs-theme="dark"] .modal-content {
    background: #E3E3E3;
    border: 1px solid rgba(201,168,76,.25);
}

/* modal header/footer border */
[data-bs-theme="light"] .modal-header,
[data-bs-theme="light"] .modal-footer {
    border-color: rgba(201,168,76,.20) !important;
}
[data-bs-theme="dark"] .modal-header,
[data-bs-theme="dark"] .modal-footer {
    border-color: rgba(201,168,76,.15) !important;
}

/* modal title */
[data-bs-theme="light"] .bidang-modal-title { color: #2A2C2B; }
[data-bs-theme="dark"]  .bidang-modal-title { color: #E8C96B; }

/* modal close icon */
[data-bs-theme="light"] .btn-active-light-primary       { color: #666; }
[data-bs-theme="light"] .btn-active-light-primary:hover { background: rgba(201,168,76,.12); color: #2A2C2B; }
[data-bs-theme="dark"]  .btn-active-light-primary       { color: rgba(245,240,232,.70); }
[data-bs-theme="dark"]  .btn-active-light-primary:hover { background: rgba(201,168,76,.12); color: #E8C96B; }

/* form labels */
[data-bs-theme="light"] .ri-label { color: #374140; }
[data-bs-theme="dark"]  .ri-label { color: rgba(245,240,232,.70); }

/* form inputs */
[data-bs-theme="light"] .ri-input {
    background: #fff !important;
    border: 1px solid rgba(201,168,76,.35) !important;
    color: #2A2C2B !important;
    border-radius: .375rem;
}
[data-bs-theme="light"] .ri-input:focus {
    border-color: rgba(201,168,76,.65) !important;
    box-shadow: 0 0 0 3px rgba(201,168,76,.12) !important;
    color: #2A2C2B !important;
}
[data-bs-theme="dark"] .ri-input {
    background: rgba(10,21,48,.80) !important;
    border: 1px solid rgba(201,168,76,.25) !important;
    color: #F5F0E8 !important;
    border-radius: .375rem;
}
[data-bs-theme="dark"] .ri-input:focus {
    background: rgba(10,21,48,.95) !important;
    border-color: rgba(201,168,76,.55) !important;
    box-shadow: 0 0 0 3px rgba(201,168,76,.12) !important;
    color: #F5F0E8 !important;
}
[data-bs-theme="dark"] .ri-input option         { background: #132146; color: #F5F0E8; }
[data-bs-theme="dark"] .ri-input::placeholder    { color: rgba(245,240,232,.30) !important; }
[data-bs-theme="light"] .ri-input::placeholder   { color: rgba(0,0,0,.30) !important; }

/* readonly input */
[data-bs-theme="light"] .ri-input-readonly {
    background: rgba(0,0,0,.04) !important;
    border: 1px solid rgba(0,0,0,.10) !important;
    color: #888 !important;
    border-radius: .375rem;
}
[data-bs-theme="dark"] .ri-input-readonly {
    background: rgba(255,255,255,.04) !important;
    border: 1px solid rgba(255,255,255,.10) !important;
    color: rgba(245,240,232,.50) !important;
    border-radius: .375rem;
}

/* modal save button */
.btn-modal-save {
    background: linear-gradient(135deg, #2B5987 100%);
    color: #E3E3E3 !important;
    font-weight: 700;
    border: none;
    border-radius: .375rem;
    transition: opacity .18s;
}
.btn-modal-save:hover {
    opacity: .88;
    color: #E3E3E3 !important;
}

/* modal cancel button */
[data-bs-theme="light"] .btn-modal-cancel {
    background: rgba(0,0,0,.05);
    color: #555;
    border: 1px solid rgba(0,0,0,.12);
    border-radius: .375rem;
    transition: background .15s;
}
[data-bs-theme="light"] .btn-modal-cancel:hover { background: rgba(0,0,0,.10); color: #222; }
[data-bs-theme="dark"]  .btn-modal-cancel {
    background: rgba(255,255,255,.07);
    color: rgba(245,240,232,.70);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: .375rem;
    transition: background .15s;
}
[data-bs-theme="dark"]  .btn-modal-cancel:hover { background: rgba(255,255,255,.12); color: #F5F0E8; }

/* delete confirm body text */
[data-bs-theme="light"] .hapus-body-text { color: #374140; }
[data-bs-theme="dark"]  .hapus-body-text { color: rgba(245,240,232,.80); }
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Tambah Bidang ── */
    document.querySelectorAll('.btn-tambah-bidang').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('tambah-sub-event-id').value   = this.dataset.subEventId;
            document.getElementById('tambah-sub-event-nama').value = this.dataset.subEventNama;
            document.getElementById('tambah-nama').value           = '';
            document.getElementById('tambah-status').value         = 'aktif';
        });
    });

    /* ── Edit Bidang ── */
    document.querySelectorAll('.btn-edit-bidang').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id           = this.dataset.id;
            const subEventNama = this.dataset.subEventNama;
            document.getElementById('edit-sub-event-nama').value = subEventNama;
            fetch(`/master/bidang/${id}/edit`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(function (data) {
                document.getElementById('edit-nama').value   = data.nama;
                document.getElementById('edit-status').value = data.status;
                document.getElementById('formEditBidang').action = `/master/bidang/${id}`;
            })
            .catch(function () {
                alert('Gagal memuat data bidang. Silakan coba lagi.');
            });
        });
    });

    /* ── Hapus Bidang ── */
    document.querySelectorAll('.btn-hapus-bidang').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const nama = this.dataset.nama;
            document.getElementById('hapus-nama-preview').textContent = `"${nama}"`;
            document.getElementById('formHapusBidang').action = `/master/bidang/${id}`;
        });
    });

});
</script>
@endpush