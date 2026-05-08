@extends('index', ['dummy' => true])

@section('content')

<style>
/* ══════════════════════════════════════════════════
   SUB-EVENT PAGE — Theme-aware card + gold buttons
══════════════════════════════════════════════════ */

/* ── Card shell ── */
.sub-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.10);
}

/* ── Card header: follows [data-bs-theme] ── */
[data-bs-theme="light"] .sub-card .card-header {
    background: #E5DFC5;                          /* warm white-bone */
    border-bottom: 1px solid rgba(201,168,76,.25);
}
[data-bs-theme="dark"] .sub-card .card-header {
    background: #2A2C2B;
    border-bottom: 1px solid rgba(201,168,76,.20);
}

/* ── Card body ── */
[data-bs-theme="light"] .sub-card .card-body {
    background: #E3E3E3;
}
[data-bs-theme="dark"] .sub-card .card-body {
    background: #374140;
}

/* ── Card title ── */
[data-bs-theme="light"] .sub-card .card-title {
    color: #2A2C2B !important;   /* rich gold-brown */
}
[data-bs-theme="dark"] .sub-card .card-title {
    color: #E3E3E3 !important;
}

/* ── Table head ── */
[data-bs-theme="light"] .se-table thead th {
    background: rgba(201,168,76,.10);
    color: #2A2C2B;
    border-bottom: 2px solid rgba(201,168,76,.30);
}
[data-bs-theme="dark"] .se-table thead th {
    background: rgba(201,168,76,.08);
    color: #E3E3E3;
    border-bottom: 1px solid rgba(201,168,76,.22);
}

/* ── Table body rows ── */
[data-bs-theme="light"] .se-table tbody td {
    color: #374140;
    border-bottom: 1px solid rgba(201,168,76,.12);
    background: transparent;
}
[data-bs-theme="dark"] .se-table tbody td {
    color: rgba(245,240,232,.82);
    border-bottom: 1px solid rgba(201,168,76,.08);
    background: transparent;
}

[data-bs-theme="light"] .se-table tbody tr:hover td {
    background: rgba(201,168,76,.06);
}
[data-bs-theme="dark"] .se-table tbody tr:hover td {
    background: rgba(201,168,76,.05);
}

/* empty row */
[data-bs-theme="light"] .se-table .se-empty {
    color: #999;
}
[data-bs-theme="dark"] .se-table .se-empty {
    color: rgba(245,240,232,.35);
}

/* ── Kategori badge ── */
[data-bs-theme="light"] .badge-kategori {
    background: rgba(201,168,76,.15);
    color: #374140;
    border: 1px solid rgba(201,168,76,.35);
    font-weight: 600;
    font-size: .75rem;
    padding: .35em .75em;
    border-radius: 30px;
    white-space: nowrap;
}
[data-bs-theme="dark"] .badge-kategori {
    background: rgba(201,168,76,.14);
    color: #E3E3E3;
    border: 1px solid rgba(201,168,76,.30);
    font-weight: 600;
    font-size: .75rem;
    padding: .35em .75em;
    border-radius: 30px;
    white-space: nowrap;
}

/* ── Gold gradient button (Edit) ── */
.btn-gold {
    background: linear-gradient(135deg, #2B5987 100%);
    color: #E3E3E3 !important;
    font-weight: 700;
    border: none;
    border-radius: .375rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    letter-spacing: .02em;
    box-shadow: 0 2px 8px rgba(201,168,76,.30);
    transition: opacity .18s, box-shadow .18s;
}
.btn-gold:hover {
    opacity: .88;
    box-shadow: 0 4px 14px rgba(201,168,76,.40);
    color: #0D1B3E !important;
}

/* ── Danger button (Hapus) — unchanged, just keep consistent size ── */
.btn-hapus {
    border-radius: .375rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    font-weight: 600;
}

/* ── Tambah Sub Event button ── */
.btn-tambah-se {
    background: linear-gradient(135deg, #2B5987 100%);
    color: #E3E3E3 !important;
    font-weight: 700;
    border: none;
    border-radius: .5rem;
    padding: .55rem 1.25rem;
    font-size: .9rem;
    box-shadow: 0 3px 12px rgba(201,168,76,.30);
    transition: opacity .18s, box-shadow .18s;
}
.btn-tambah-se:hover {
    opacity: .88;
    box-shadow: 0 5px 18px rgba(201,168,76,.42);
    color: #2B5987 !important;
}

/* ── Modal backdrop ── */
.modal-backdrop.show { opacity: .35; }

/* ── Modal content follows theme ── */
[data-bs-theme="light"] .modal-content {
    background: #FDFAF3;
}
[data-bs-theme="dark"] .modal-content {
    background: #E3E3E3;
    border: 1px solid rgba(201,168,76,.25);
}
[data-bs-theme="dark"] .modal-header,
[data-bs-theme="dark"] .modal-footer {
    border-color: rgba(201,168,76,.15) !important;
}
[data-bs-theme="dark"] .modal-title {
    color: #E8C96B;
}
[data-bs-theme="dark"] .form-label {
    color: rgba(245,240,232,.75);
}
[data-bs-theme="dark"] .form-control,
[data-bs-theme="dark"] .form-select {
    background: rgba(10,21,48,.80) !important;
    border: 1px solid rgba(201,168,76,.25) !important;
    color: #F5F0E8 !important;
}
[data-bs-theme="dark"] .form-control:focus,
[data-bs-theme="dark"] .form-select:focus {
    background: rgba(10,21,48,.95) !important;
    border-color: rgba(201,168,76,.55) !important;
    box-shadow: 0 0 0 3px rgba(201,168,76,.12) !important;
    color: #F5F0E8 !important;
}
[data-bs-theme="dark"] .form-control::placeholder {
    color: rgba(245,240,232,.30) !important;
}
[data-bs-theme="dark"] .form-select option {
    background: #132146;
    color: #F5F0E8;
}
/* close button on dark */
[data-bs-theme="dark"] .btn-active-light-primary {
    color: rgba(245,240,232,.70);
}
[data-bs-theme="dark"] .btn-active-light-primary:hover {
    background: rgba(201,168,76,.12);
    color: #E8C96B;
}
/* Batal button on dark */
[data-bs-theme="dark"] .btn-modal-cancel {
    background: rgba(255,255,255,.07);
    color: rgba(245,240,232,.70);
    border: 1px solid rgba(255,255,255,.15);
}
[data-bs-theme="dark"] .btn-modal-cancel:hover {
    background: rgba(255,255,255,.12);
    color: #F5F0E8;
}
/* Modal save button */
.btn-modal-save {
    background: linear-gradient(135deg,#C9A84C,#A0782A);
    color: #0D1B3E !important;
    font-weight: 700;
    border: none;
    border-radius: .375rem;
    transition: opacity .18s;
}
.btn-modal-save:hover { opacity: .88; color: #0D1B3E !important; }
</style>

<div class="row">
    <div class="col-12">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-4">
            <button class="btn btn-tambah-se" data-bs-toggle="modal" data-bs-target="#modalSubEvent">
                <i class="bi bi-plus-lg me-1"></i> Tambah Sub Event
            </button>
        </div>

        <div class="card sub-card">

            <div class="card-header d-flex justify-content-between align-items-center py-4 px-5">
                <h3 class="card-title fw-bold m-0">
                    Data Sub Event
                </h3>
            </div>

            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table se-table align-middle gs-0 gy-0 mb-0">
                        <thead>
                            <tr>
                                <th width="50"  class="ps-3">No</th>
                                <th>Tahun</th>
                                <th>Event</th>
                                <th>Sub Event</th>
                                <th>Kategori</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Berakhir</th>
                                <th width="180" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($subEvents as $item)
                                <tr>
                                    <td class="ps-3">{{ $loop->iteration }}</td>
                                    <td>{{ $item['tahun'] }}</td>
                                    <td>{{ $item['event'] }}</td>
                                    <td>{{ $item['sub_event'] }}</td>
                                    <td>
                                        <span class="badge-kategori">
                                            {{ $item['kategori'] ?: '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $item['mulai'] }}</td>
                                    <td>{{ $item['berakhir'] }}</td>
                                    <td class="text-center pe-3">

                                        <button
                                            class="btn btn-gold btn-sm btn-edit me-1"
                                            data-id="{{ $item['id'] }}">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </button>

                                        <form
                                            action="{{ route('admin.sub-event.destroy', $item['id']) }}"
                                            method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                onclick="return confirm('Hapus data ini?')"
                                                class="btn btn-danger btn-hapus btn-sm">
                                                <i class="bi bi-trash3 me-1"></i>Hapus
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center se-empty py-10">
                                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                        Belum ada data
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL — Tambah / Edit Sub Event
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalSubEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">

            <form id="formSubEvent" method="POST" action="{{ route('admin.sub-event.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalTitle">
                        Tambah Sub Event
                    </h5>
                    <button
                        type="button"
                        class="btn btn-sm btn-icon btn-active-light-primary"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">
                    <div class="row">

                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold required">Tahun</label>
                            <input
                                type="number"
                                name="tahun"
                                id="tahun"
                                class="form-control"
                                placeholder="cth. 2025"
                                required>
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
                            <input
                                type="text"
                                name="sub_event"
                                id="sub_event"
                                class="form-control"
                                placeholder="Nama sub event"
                                required>
                        </div>

                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Kategori</label>
                            <input
                                type="text"
                                name="kategori"
                                id="kategori"
                                class="form-control"
                                placeholder="Opsional">
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
                    <button
                        type="button"
                        class="btn btn-modal-cancel"
                        data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-modal-save px-4">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = new bootstrap.Modal(document.getElementById('modalSubEvent'));

    /* ── Edit buttons ── */
    document.querySelectorAll('.btn-edit').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const id = this.dataset.id;
            const response = await fetch(`/sub-event/${id}/edit`);
            const data     = await response.json();

            document.getElementById('modalTitle').innerText = 'Edit Sub Event';
            document.getElementById('tahun').value          = data.tahun;
            document.getElementById('event').value          = data.event;
            document.getElementById('sub_event').value      = data.sub_event;
            document.getElementById('kategori').value       = data.kategori;
            document.getElementById('mulai').value          = data.mulai;
            document.getElementById('berakhir').value       = data.berakhir;
            document.getElementById('formMethod').value     = 'PUT';
            document.getElementById('formSubEvent').action  = `/sub-event/${id}`;

            modal.show();
        });
    });

    /* ── Tambah button — reset form ── */
    document.querySelector('[data-bs-target="#modalSubEvent"]')
        .addEventListener('click', function () {
            document.getElementById('modalTitle').innerText  = 'Tambah Sub Event';
            document.getElementById('formSubEvent').action   = `{{ route('admin.sub-event.store') }}`;
            document.getElementById('formMethod').value      = 'POST';
            document.getElementById('formSubEvent').reset();
        });

});
</script>

@endsection