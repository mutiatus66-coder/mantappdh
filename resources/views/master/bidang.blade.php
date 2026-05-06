@extends('index', ['dummy' => true])

@section('content')

{{-- ══════════════════════════════════════════════════
     Flash messages
══════════════════════════════════════════════════ --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════════════
     Page header
══════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">Master Bidang</h4>
        <small class="text-muted">Kelola bidang untuk setiap sub event</small>
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

    <div class="accordion-item border mb-3 rounded shadow-sm" id="panel-{{ $seId }}">

        {{-- ─── Accordion header ─── --}}
        <h2 class="accordion-header" id="heading-{{ $seId }}">
            <button
                class="accordion-button fw-semibold {{ $isOpen ? '' : 'collapsed' }} px-4"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse-{{ $seId }}"
                aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                aria-controls="collapse-{{ $seId }}"
            >
                <span class="text-muted me-2 fw-normal small">Sub Event:</span>
                <span class="text-primary">{{ $se['sub_event'] }}</span>
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
                              style="background:#20c997;color:#fff;">
                            Bidang Aktif &nbsp;<strong>{{ $aktif }}</strong>
                        </span>
                        <span class="badge rounded-pill px-3 py-2 fs-6"
                              style="background:#6c757d;color:#fff;">
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
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                        <span class="badge bg-success px-3 py-2">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Edit --}}
                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm btn-edit-bidang me-1"
                                        data-id="{{ $bidang['id'] }}"
                                        data-sub-event-nama="{{ $se['sub_event'] }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditBidang"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </button>

                                    {{-- Hapus --}}
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm btn-hapus-bidang"
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
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                    Belum ada bidang untuk sub event ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div><!-- /table-responsive -->

            </div><!-- /accordion-body -->
        </div><!-- /accordion-collapse -->
    </div><!-- /accordion-item -->
    @endforeach

</div><!-- /accordion -->


{{-- ══════════════════════════════════════════════════
     MODAL — Tambah Bidang
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahBidang" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="modalTambahLabel">Tambah Bidang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('admin.bidang.store') }}">
                @csrf
                <div class="modal-body pt-3">
                    <input type="hidden" name="sub_event_id" id="tambah-sub-event-id">

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-end">Nama Sub Event</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="form-control bg-light"
                                id="tambah-sub-event-nama"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label for="tambah-nama" class="col-sm-4 col-form-label text-sm-end">
                            Nama Bidang <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="form-control"
                                id="tambah-nama"
                                name="nama"
                                placeholder="Masukkan nama bidang"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-1 row align-items-center">
                        <label for="tambah-status" class="col-sm-4 col-form-label text-sm-end">Status</label>
                        <div class="col-sm-8">
                            <select class="form-select" id="tambah-status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
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
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="modalEditLabel">Edit Bidang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="formEditBidang">
                @csrf
                @method('PUT')
                <div class="modal-body pt-3">

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-4 col-form-label text-sm-end">Nama Sub Event</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="form-control bg-light"
                                id="edit-sub-event-nama"
                                readonly
                            >
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label for="edit-nama" class="col-sm-4 col-form-label text-sm-end">
                            Nama Bidang <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="form-control"
                                id="edit-nama"
                                name="nama"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-1 row align-items-center">
                        <label for="edit-status" class="col-sm-4 col-form-label text-sm-end">Status</label>
                        <div class="col-sm-8">
                            <select class="form-select" id="edit-status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
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
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold text-danger" id="modalHapusLabel">
                    <i class="bi bi-exclamation-triangle me-1"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    Yakin ingin menghapus bidang
                    <strong id="hapus-nama-preview" class="text-danger"></strong>?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <form method="POST" id="formHapusBidang">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-3">Ya, Hapus</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection


@push('styles')
<style>
    /* Accordion chrome */
    .accordion-button:not(.collapsed) {
        color: #0d6efd;
        background-color: #f0f6ff;
        box-shadow: none;
    }
    .accordion-button:focus {
        box-shadow: none;
    }
    .accordion-item {
        border-radius: .5rem !important;
        overflow: hidden;
    }

    /* Tambah Bidang button */
    .btn-tambah-bidang {
        background-color: #0d9488;
        color: #fff;
        border: none;
        border-radius: .375rem;
        padding: .45rem 1rem;
        font-size: .875rem;
        transition: background .2s;
    }
    .btn-tambah-bidang:hover {
        background-color: #0f766e;
        color: #fff;
    }

    /* Table tweaks */
    .table thead th {
        font-size: .8125rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #495057;
    }
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Tambah Bidang: populate sub_event fields ── */
    document.querySelectorAll('.btn-tambah-bidang').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('tambah-sub-event-id').value   = this.dataset.subEventId;
            document.getElementById('tambah-sub-event-nama').value = this.dataset.subEventNama;
            document.getElementById('tambah-nama').value           = '';
            document.getElementById('tambah-status').value         = 'aktif';
        });
    });

    /* ── Edit Bidang: fetch data then populate modal ── */
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

    /* ── Hapus Bidang: set form action & preview name ── */
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