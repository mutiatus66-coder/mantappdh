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
   BIDANG PAGE — Full theme-aware styles
══════════════════════════════════════════════════ */

/* ── Page title — always visible regardless of theme ── */
.bidang-page-title {
    color: #F5F0E8 !important;   /* always warm white, readable on dark page bg */
    text-shadow: 0 1px 4px rgba(0,0,0,.35);
}
.bidang-page-subtitle {
    color: rgba(201,168,76,.75) !important;   /* always gold-tinted */
}

/* ── Accordion shell ── */
[data-bs-theme="light"] .ri-accordion-item {
    background: #FDFAF3;
    border: 1px solid rgba(201,168,76,.28) !important;
    border-radius: .75rem !important;
    overflow: hidden;
}
[data-bs-theme="dark"] .ri-accordion-item {
    background: #132146;
    border: 1px solid rgba(201,168,76,.20) !important;
    border-radius: .75rem !important;
    overflow: hidden;
}

/* ── Accordion button ── */
[data-bs-theme="light"] .ri-accordion-btn {
    background: #FDFAF3 !important;
    color: #2C2C2C !important;
    box-shadow: none !important;
}
[data-bs-theme="light"] .ri-accordion-btn:not(.collapsed) {
    background: rgba(201,168,76,.10) !important;
    border-bottom: 1px solid rgba(201,168,76,.28);
}
[data-bs-theme="dark"] .ri-accordion-btn {
    background: #132146 !important;
    color: #F5F0E8 !important;
    box-shadow: none !important;
}
[data-bs-theme="dark"] .ri-accordion-btn:not(.collapsed) {
    background: rgba(201,168,76,.08) !important;
    border-bottom: 1px solid rgba(201,168,76,.20);
}
.ri-accordion-btn::after {
    filter: invert(0);
}
[data-bs-theme="dark"] .ri-accordion-btn::after {
    filter: invert(1) sepia(1) saturate(2) hue-rotate(5deg);
}

/* ── Sub Event label inside button ── */
[data-bs-theme="light"] .ri-acc-label { color: rgba(122,90,30,.60); font-weight: 400; }
[data-bs-theme="dark"]  .ri-acc-label { color: rgba(201,168,76,.60); font-weight: 400; }
[data-bs-theme="light"] .ri-acc-value { color: #7A5A1E; font-weight: 600; }
[data-bs-theme="dark"]  .ri-acc-value { color: #E8C96B; font-weight: 600; }

/* ── Accordion body ── */
[data-bs-theme="light"] .accordion-body { background: #FDFAF3; }
[data-bs-theme="dark"]  .accordion-body { background: #0F1F45; }

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

/* ── Tambah Bidang button ── */
.btn-tambah-bidang {
    background: linear-gradient(135deg, #C9A84C 0%, #E8C96B 50%, #A0782A 100%);
    color: #0D1B3E !important;
    font-weight: 700;
    border: none;
    border-radius: .4rem;
    padding: .45rem 1.1rem;
    font-size: .875rem;
    box-shadow: 0 2px 8px rgba(201,168,76,.28);
    transition: opacity .18s, box-shadow .18s;
}
.btn-tambah-bidang:hover {
    opacity: .88;
    box-shadow: 0 4px 14px rgba(201,168,76,.40);
    color: #0D1B3E !important;
}

/* ── Gold gradient Edit button ── */
.btn-gold {
    background: linear-gradient(135deg, #C9A84C 0%, #E8C96B 50%, #A0782A 100%);
    color: #0D1B3E !important;
    font-weight: 700;
    border: none;
    border-radius: .35rem;
    padding: .32rem .75rem;
    font-size: .8rem;
    letter-spacing: .02em;
    box-shadow: 0 2px 8px rgba(201,168,76,.28);
    transition: opacity .18s, box-shadow .18s;
}
.btn-gold:hover {
    opacity: .88;
    box-shadow: 0 4px 14px rgba(201,168,76,.40);
    color: #0D1B3E !important;
}

/* ── Hapus button ── */
.btn-hapus {
    border-radius: .35rem;
    padding: .32rem .75rem;
    font-size: .8rem;
    font-weight: 600;
}

/* ── Table ── */
.ri-table { border-collapse: separate; border-spacing: 0; }

[data-bs-theme="light"] .ri-table thead th {
    background: rgba(201,168,76,.10);
    color: #8A6A20;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 2px solid rgba(201,168,76,.30);
    padding: 10px 12px;
}
[data-bs-theme="dark"] .ri-table thead th {
    background: rgba(201,168,76,.08);
    color: rgba(201,168,76,.75);
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 1px solid rgba(201,168,76,.22);
    padding: 10px 12px;
}

[data-bs-theme="light"] .ri-table tbody td {
    color: #2C2C2C;
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
    border: 1px solid rgba(201,168,76,.20);
}
[data-bs-theme="dark"] .modal-content {
    background: #132146;
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
[data-bs-theme="light"] .bidang-modal-title { color: #7A5A1E; }
[data-bs-theme="dark"]  .bidang-modal-title { color: #E8C96B; }

/* modal close icon */
[data-bs-theme="light"] .btn-active-light-primary       { color: #666; }
[data-bs-theme="light"] .btn-active-light-primary:hover { background: rgba(201,168,76,.12); color: #7A5A1E; }
[data-bs-theme="dark"]  .btn-active-light-primary       { color: rgba(245,240,232,.70); }
[data-bs-theme="dark"]  .btn-active-light-primary:hover { background: rgba(201,168,76,.12); color: #E8C96B; }

/* form labels */
[data-bs-theme="light"] .ri-label { color: #4A4A4A; }
[data-bs-theme="dark"]  .ri-label { color: rgba(245,240,232,.70); }

/* form inputs */
[data-bs-theme="light"] .ri-input {
    background: #fff !important;
    border: 1px solid rgba(201,168,76,.35) !important;
    color: #2C2C2C !important;
    border-radius: .375rem;
}
[data-bs-theme="light"] .ri-input:focus {
    border-color: rgba(201,168,76,.65) !important;
    box-shadow: 0 0 0 3px rgba(201,168,76,.12) !important;
    color: #2C2C2C !important;
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
[data-bs-theme="dark"] .ri-input option   { background: #132146; color: #F5F0E8; }
[data-bs-theme="dark"] .ri-input::placeholder { color: rgba(245,240,232,.30) !important; }
[data-bs-theme="light"] .ri-input::placeholder { color: rgba(0,0,0,.30) !important; }

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

/* modal buttons */
.btn-modal-save {
    background: linear-gradient(135deg,#C9A84C,#A0782A);
    color: #0D1B3E !important;
    font-weight: 700;
    border: none;
    border-radius: .375rem;
    transition: opacity .18s;
}
.btn-modal-save:hover { opacity: .88; color: #0D1B3E !important; }

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
[data-bs-theme="light"] .hapus-body-text { color: #3A3A3A; }
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