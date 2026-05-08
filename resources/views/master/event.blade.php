@extends('index', ['dummy' => true])

@section('content')

<style>
/* ══════════════════════════════════════════════════
   EVENT PAGE — Theme-aware card + gold buttons
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
    background: #FDFAF3;
    border-bottom: 1px solid rgba(201,168,76,.25);
}
[data-bs-theme="dark"] .sub-card .card-header {
    background: #132146;
    border-bottom: 1px solid rgba(201,168,76,.20);
}

/* ── Card body ── */
[data-bs-theme="light"] .sub-card .card-body {
    background: #FDFAF3;
}
[data-bs-theme="dark"] .sub-card .card-body {
    background: #0F1F45;
}

/* ── Card title ── */
[data-bs-theme="light"] .sub-card .card-title {
    color: #7A5A1E !important;
}
[data-bs-theme="dark"] .sub-card .card-title {
    color: #E8C96B !important;
}

/* ── Table head ── */
[data-bs-theme="light"] .se-table thead th {
    background: rgba(201,168,76,.10);
    color: #8A6A20;
    border-bottom: 2px solid rgba(201,168,76,.30);
}
[data-bs-theme="dark"] .se-table thead th {
    background: rgba(201,168,76,.08);
    color: rgba(201,168,76,.75);
    border-bottom: 1px solid rgba(201,168,76,.22);
}

/* ── Table body rows ── */
[data-bs-theme="light"] .se-table tbody td {
    color: #2C2C2C;
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

/* ── Gold gradient button (Edit) ── */
.btn-gold {
    background: linear-gradient(135deg, #C9A84C 0%, #E8C96B 50%, #A0782A 100%);
    color: #0D1B3E !important;
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

/* ── Tambah Event button ── */
.btn-tambah-se {
    background: linear-gradient(135deg, #C9A84C 0%, #E8C96B 50%, #A0782A 100%);
    color: #0D1B3E !important;
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
    color: #0D1B3E !important;
}

/* ── Modal backdrop ── */
.modal-backdrop.show { opacity: .35; }

/* ── Modal content follows theme ── */
[data-bs-theme="light"] .modal-content {
    background: #FDFAF3;
}
[data-bs-theme="dark"] .modal-content {
    background: #132146;
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
    color: rgba(135, 95, 31, 0.75);
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

<div id="kt_content" class="content d-flex flex-column flex-column-fluid">
  <div class="p-6">

    <div class="row">
      <div class="col-12">

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <!-- TOP ACTION -->
        <div class="mb-4">
          <button
            class="btn btn-tambah-se"
            data-bs-toggle="modal"
            data-bs-target="#modalTambahEvent">
            <i class="bi bi-plus-lg me-1"></i> Tambah Event
          </button>
        </div>

        <!-- CARD TABLE -->
        <div class="card sub-card">

          <div class="card-header d-flex justify-content-between align-items-center py-4 px-5">
            <h3 class="card-title fw-bold m-0">
              Data Event
            </h3>
          </div>

          <div class="card-body px-4 pb-4">
            <div class="table-responsive">
              <table class="table se-table align-middle gs-0 gy-0 mb-0 w-full border border-gray-300 border-collapse">
                <thead>
                  <tr>
                    <th class="px-4 py-3 w-16 text-center border border-gray-300">No</th>
                    <th class="px-4 py-3 text-left border border-gray-300">Event</th>
                    <th class="px-4 py-3 w-40 text-center border border-gray-300">Aksi</th>
                  </tr>
                </thead>
                <tbody class="text-gray-700">
                  <tr class="border-t">
                    <td class="px-4 py-3 text-center border border-gray-300">1</td>
                    <td class="px-4 py-3 border border-gray-300">INOVASI DAERAH KAB. MAGETAN</td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                      <a href="#">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                      </a>
                    </td>
                  </tr>
                  <tr class="border-t">
                    <td class="px-4 py-3 text-center border border-gray-300">2</td>
                    <td class="px-4 py-3 border border-gray-300">LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)</td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                      <a href="#">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                      </a>
                    </td>
                  </tr>
                  <tr class="border-t">
                    <td class="px-4 py-3 text-center border border-gray-300">3</td>
                    <td class="px-4 py-3 border border-gray-300">PAMERAN</td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                      <a href="#">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                      </a>
                    </td>
                  </tr>
                  <tr class="border-t">
                    <td class="px-4 py-3 text-center border border-gray-300">4</td>
                    <td class="px-4 py-3 border border-gray-300">INOTEK AWARD</td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                      <a href="#">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
  <div id="kt_content_container" class="container-fluid"></div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL — Tambah Event
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahEvent" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">

      <form action="{{ route('event.store') }}" method="POST">
        @csrf

        <div class="modal-header px-5 py-4">
          <h5 class="modal-title fw-semibold" id="modalTitle">
            Tambah Event
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

            <!-- Nama Event -->
            <div class="col-md-12 mb-4">
              <label class="form-label fw-semibold required">Nama Event</label>
              <input
                type="text"
                name="nama_event"
                class="form-control"
                placeholder="Masukkan nama event..."
                required>
            </div>

            <!-- Jenis -->
            <div class="col-md-12 mb-2">
              <label class="form-label fw-semibold">Jenis</label>
              <div class="d-flex gap-4 mt-1">
                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                  <input type="radio" name="jenis" value="INOTEK" checked> INOTEK
                </label>
                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                  <input type="radio" name="jenis" value="INODA"> INODA
                </label>
              </div>
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

@endsection