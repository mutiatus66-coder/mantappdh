@extends('index', ['dummy' => true])

@section('content')

{{-- ══════════════════════════════════════════════════
     Flash messages
══════════════════════════════════════════════════ --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.35);color:#E8C96B;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════════════
     Page header
══════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#F5F0E8;">Master Bidang</h4>
        <small style="color:rgba(201,168,76,0.65);">Kelola bidang untuk setiap sub event</small>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     Accordion — one panel per sub-event
══════════════════════════════════════════════════ --}}
<div class="accordion" id="accordionBidang">

    @foreach($subEvents as $index => $se)
    @php
        $seId    = $se['id'];
        $rows    = $bidangData[$seId] ?? [];
        $aktif   = collect($rows)->where('status', 'aktif')->count();
        $nonaktif= collect($rows)->where('status', 'tidak_aktif')->count();
        $isOpen  = request('open') == $seId || ($index === 0 && !request()->has('open'));
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
                <span style="color:rgba(201,168,76,0.60);font-weight:400;" class="me-2 small">Sub Event:</span>
                <span style="color:#E8C96B;">{{ $se['sub_event'] }}</span>
            </button>
        </h2>

        {{-- ─── Accordion body ─── --}}
        <div
            id="collapse-{{ $seId }}"
            class="accordion-collapse show"
            aria-labelledby="heading-{{ $seId }}"
            data-bs-parent="#accordionBidang"
        >
            <div class="accordion-body px-4 pb-4 pt-3">

                {{-- Stats + Add button --}}
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <span class="badge rounded-pill px-3 py-2 fs-6"
                              style="background:rgba(20,180,100,0.18);color:#4AE09A;border:1px solid rgba(20,180,100,0.30);">
                            Bidang Aktif &nbsp;<strong>{{ $aktif }}</strong>
                        </span>
                        <span class="badge rounded-pill px-3 py-2 fs-6"
                              style="background:rgba(255,255,255,0.08);color:rgba(245,240,232,0.60);border:1px solid rgba(255,255,255,0.15);">
                            Bidang Tidak Aktif &nbsp;<strong>{{ $nonaktif }}</strong>
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
                                        <span class="badge px-3 py-2"
                                              style="background:rgba(20,180,100,0.18);color:#4AE09A;border:1px solid rgba(20,180,100,0.30);">Aktif</span>
                                    @else
                                        <span class="badge px-3 py-2"
                                              style="background:rgba(255,255,255,0.08);color:rgba(245,240,232,0.55);border:1px solid rgba(255,255,255,0.12);">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-ri-warning btn-sm btn-edit-bidang me-1"
                                        data-id="{{ $bidang['id'] }}"
                                        data-sub-event-nama="{{ $se['sub_event'] }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditBidang"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-ri-danger btn-sm btn-hapus-bidang"
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
                                <td colspan="4" class="text-center py-4" style="color:rgba(245,240,232,0.40);">
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


{{-- MODAL — Tambah Bidang --}}
<div class="modal fade" id="modalTambahBidang" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ri-modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="modalTambahLabel" style="color:#E8C96B;">Tambah Bidang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.bidang.store') }}">
                @csrf
                <div class="modal-body pt-3">
                    <input type="hidden" name="sub_event_id" id="tambah-sub-event-id">
                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-end ri-label">Nama Sub Event</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input-readonly" id="tambah-sub-event-nama" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row align-items-center">
                        <label for="tambah-nama" class="col-sm-4 col-form-label text-sm-end ri-label">
                            Nama Bidang <span style="color:#ff6b6b;">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input" id="tambah-nama" name="nama" placeholder="Masukkan nama bidang" required>
                        </div>
                    </div>
                    <div class="mb-1 row align-items-center">
                        <label for="tambah-status" class="col-sm-4 col-form-label text-sm-end ri-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-select ri-input" id="tambah-status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-ri-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-ri-gold px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL — Edit Bidang --}}
<div class="modal fade" id="modalEditBidang" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ri-modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="modalEditLabel" style="color:#E8C96B;">Edit Bidang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formEditBidang">
                @csrf
                @method('PUT')
                <div class="modal-body pt-3">
                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-end ri-label">Nama Sub Event</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input-readonly" id="edit-sub-event-nama" readonly>
                        </div>
                    </div>
                    <div class="mb-3 row align-items-center">
                        <label for="edit-nama" class="col-sm-4 col-form-label text-sm-end ri-label">
                            Nama Bidang <span style="color:#ff6b6b;">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control ri-input" id="edit-nama" name="nama" required>
                        </div>
                    </div>
                    <div class="mb-1 row align-items-center">
                        <label for="edit-status" class="col-sm-4 col-form-label text-sm-end ri-label">Status</label>
                        <div class="col-sm-8">
                            <select class="form-select ri-input" id="edit-status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-ri-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-ri-gold px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL — Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapusBidang" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content ri-modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="modalHapusLabel" style="color:#ff6b6b;">
                    <i class="bi bi-exclamation-triangle me-1"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" style="color:rgba(245,240,232,0.80);">
                    Yakin ingin menghapus bidang
                    <strong id="hapus-nama-preview" style="color:#ff6b6b;"></strong>?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-ri-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <form method="POST" id="formHapusBidang">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ri-danger btn-sm px-3">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@push('styles')
<style>
    /* ── Accordion ── */
    .ri-accordion-item {
        background: #132146;
        border: 1px solid rgba(201,168,76,0.20) !important;
        border-radius: .6rem !important;
        overflow: hidden;
    }
    .ri-accordion-btn {
        background: #132146 !important;
        color: #F5F0E8 !important;
        box-shadow: none !important;
    }
    .ri-accordion-btn:not(.collapsed) {
        background: rgba(201,168,76,0.08) !important;
        border-bottom: 1px solid rgba(201,168,76,0.20);
    }
    .ri-accordion-btn::after {
        filter: invert(1) sepia(1) saturate(2) hue-rotate(5deg);
    }
    .accordion-body {
        background: #0F1F45;
    }

    /* ── Table ── */
    .ri-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .ri-table thead th {
        background: rgba(201,168,76,0.08);
        color: rgba(201,168,76,0.70);
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        border-bottom: 1px solid rgba(201,168,76,0.20);
        padding: 10px 12px;
    }
    .ri-table tbody td {
        color: rgba(245,240,232,0.80);
        border-bottom: 1px solid rgba(201,168,76,0.08);
        padding: 10px 12px;
        background: transparent;
    }
    .ri-table tbody tr:hover td {
        background: rgba(201,168,76,0.05);
    }

    /* ── Buttons ── */
    .btn-tambah-bidang {
        background: linear-gradient(135deg,#C9A84C,#A0782A);
        color: #0D1B3E;
        font-weight: 600;
        border: none;
        border-radius: .375rem;
        padding: .45rem 1rem;
        font-size: .875rem;
        transition: opacity .2s;
    }
    .btn-tambah-bidang:hover { opacity: .88; color: #0D1B3E; }

    .btn-ri-warning {
        background: rgba(201,168,76,0.15);
        color: #E8C96B;
        border: 1px solid rgba(201,168,76,0.35);
        border-radius: .3rem;
        font-size: .8rem;
        padding: .3rem .65rem;
        transition: background .15s;
    }
    .btn-ri-warning:hover { background: rgba(201,168,76,0.28); color: #E8C96B; }

    .btn-ri-danger {
        background: rgba(220,53,69,0.15);
        color: #ff7b8a;
        border: 1px solid rgba(220,53,69,0.35);
        border-radius: .3rem;
        font-size: .8rem;
        padding: .3rem .65rem;
        transition: background .15s;
    }
    .btn-ri-danger:hover { background: rgba(220,53,69,0.28); color: #ff7b8a; }

    .btn-ri-gold {
        background: linear-gradient(135deg,#C9A84C,#A0782A);
        color: #0D1B3E;
        font-weight: 600;
        border: none;
        border-radius: .375rem;
        transition: opacity .2s;
    }
    .btn-ri-gold:hover { opacity: .88; color: #0D1B3E; }

    .btn-ri-secondary {
        background: rgba(255,255,255,0.07);
        color: rgba(245,240,232,0.70);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: .375rem;
        transition: background .15s;
    }
    .btn-ri-secondary:hover { background: rgba(255,255,255,0.12); color: #F5F0E8; }

    /* ── Modal ── */
    .ri-modal-content {
        background: #132146;
        border: 1px solid rgba(201,168,76,0.25);
        border-radius: .75rem;
    }
    .ri-label { color: rgba(245,240,232,0.70); }
    .ri-input {
        background: rgba(10,21,48,0.80) !important;
        border: 1px solid rgba(201,168,76,0.25) !important;
        color: #F5F0E8 !important;
        border-radius: .375rem;
    }
    .ri-input:focus {
        background: rgba(10,21,48,0.95) !important;
        border-color: rgba(201,168,76,0.55) !important;
        box-shadow: 0 0 0 3px rgba(201,168,76,0.12) !important;
        color: #F5F0E8 !important;
    }
    .ri-input option { background: #132146; color: #F5F0E8; }
    .ri-input-readonly {
        background: rgba(255,255,255,0.04) !important;
        border: 1px solid rgba(255,255,255,0.10) !important;
        color: rgba(245,240,232,0.50) !important;
        border-radius: .375rem;
    }
    .ri-input::placeholder { color: rgba(245,240,232,0.30) !important; }
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-tambah-bidang').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('tambah-sub-event-id').value   = this.dataset.subEventId;
            document.getElementById('tambah-sub-event-nama').value = this.dataset.subEventNama;
            document.getElementById('tambah-nama').value           = '';
            document.getElementById('tambah-status').value         = 'aktif';
        });
    });

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