@extends('index', ['dummy' => true])

@section('content')

<style>
/* ══════════════════════════════════════════════════
   USER PAGE — Theme-aware card + gold buttons
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

/* ── Danger button (Hapus) ── */
.btn-hapus {
    border-radius: .375rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    font-weight: 600;
}

/* ── Login As button ── */
.btn-login-as {
    background: linear-gradient(135deg, #16a34a 0%, #22c55e 50%, #15803d 100%);
    color: #fff !important;
    font-weight: 700;
    border: none;
    border-radius: .375rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    box-shadow: 0 2px 8px rgba(34,197,94,.25);
    transition: opacity .18s, box-shadow .18s;
}
.btn-login-as:hover {
    opacity: .88;
    box-shadow: 0 4px 14px rgba(34,197,94,.35);
    color: #fff !important;
}

/* ── Tambah User button ── */
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
            data-bs-target="#modalTambahData">
            <i class="bi bi-plus-lg me-1"></i> Tambah User
          </button>
        </div>

        <!-- CARD TABLE -->
        <div class="card sub-card">

          <div class="card-header d-flex justify-content-between align-items-center py-4 px-5">
            <h3 class="card-title fw-bold m-0">
              Data User
            </h3>
          </div>

          <div class="card-body px-4 pb-4">
            <div class="table-responsive">
              <table class="table se-table align-middle gs-0 gy-0 mb-0">
                <thead>
                  <tr>
                    <th class="px-4 py-3 w-16 text-center border-r border-gray-300">No</th>
                    <th class="px-4 py-3 text-center border-r border-gray-300">Nama</th>
                    <th class="px-4 py-3 text-center border-r border-gray-300">Email</th>
                    <th class="px-4 py-3 text-center border-r border-gray-300">Hak Akses</th>
                    <th class="px-4 py-3 text-center border-r border-gray-300">Status</th>
                    <th class="px-4 py-3 w-40 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="px-4 py-3 text-center">1</td>
                    <td class="px-4 py-3">akwardblublu</td>
                    <td class="px-4 py-3">akwardblublu@example.com</td>
                    <td class="px-4 py-3 text-center">admin</td>
                    <td class="px-4 py-3 text-center">aktif</td>
                    <td class="px-4 py-3 text-center">
                      <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                        <button 
                        class="btn btn-danger btn-hapus btn-sm"
                        <i class="bi bi-trash3 me-1"></i>Hapus
                        </button>
                        <button class="btn btn-login-as btn-sm">
                          <i class="bi bi-key me-1"></i>Login As
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="px-4 py-3 text-center">2</td>
                    <td class="px-4 py-3">syududu</td>
                    <td class="px-4 py-3">syududu@example.com</td>
                    <td class="px-4 py-3 text-center">user</td>
                    <td class="px-4 py-3 text-center">aktif</td>
                    <td class="px-4 py-3 text-center">
                      <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                        <button 
                        class="btn btn-danger btn-hapus btn-sm"
                        <i class="bi bi-trash3 me-1"></i>Hapus
                        </button>
                        <button class="btn btn-login-as btn-sm">
                          <i class="bi bi-key me-1"></i>Login As
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="px-4 py-3 text-center">3</td>
                    <td class="px-4 py-3">cihuyyy</td>
                    <td class="px-4 py-3">cihuyyy@example.com</td>
                    <td class="px-4 py-3 text-center">user</td>
                    <td class="px-4 py-3 text-center">aktif</td>
                    <td class="px-4 py-3 text-center">
                      <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                        <button 
                        class="btn btn-danger btn-hapus btn-sm"
                        <i class="bi bi-trash3 me-1"></i>Hapus
                        </button>
                        <button class="btn btn-login-as btn-sm">
                          <i class="bi bi-key me-1"></i>Login As
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="px-4 py-3 text-center">4</td>
                    <td class="px-4 py-3">wutwut</td>
                    <td class="px-4 py-3">wutwut@example.com</td>
                    <td class="px-4 py-3 text-center">user</td>
                    <td class="px-4 py-3 text-center">aktif</td>
                    <td class="px-4 py-3 text-center">
                      <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-gold btn-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </button>
                        <button 
                        class="btn btn-danger btn-hapus btn-sm"
                        <i class="bi bi-trash3 me-1"></i>Hapus
                        </button>
                        <button class="btn btn-login-as btn-sm">
                          <i class="bi bi-key me-1"></i>Login As
                        </button>
                      </div>
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
     MODAL — Tambah User
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTambahData" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">

      <form action="{{ route('user.store') }}" method="POST">
        @csrf

        <div class="modal-header px-5 py-4">
          <h5 class="modal-title fw-semibold">
            Tambah User
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

            <!-- Nama -->
            <div class="col-md-12 mb-4">
              <label class="form-label fw-semibold required">Nama</label>
              <input
                type="text"
                name="nama"
                class="form-control"
                placeholder="Masukkan nama..."
                required>
            </div>

            <!-- Email -->
            <div class="col-md-12 mb-4">
              <label class="form-label fw-semibold required">Email</label>
              <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Masukkan email..."
                required>
            </div>

            <!-- Hak Akses -->
            <div class="col-md-12 mb-4">
              <label class="form-label fw-semibold required">Hak Akses</label>
              <select name="hak_akses" class="form-select" required>
                <option value="" disabled selected>-- Pilih Hak Akses --</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
              </select>
            </div>

            <!-- Status -->
            <div class="col-md-12 mb-4">
              <label class="form-label fw-semibold">Status</label>
              <div class="d-flex gap-4 mt-1">
                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                  <input type="radio" name="status" value="aktif" checked> Aktif
                </label>
                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                  <input type="radio" name="status" value="nonaktif"> Nonaktif
                </label>
              </div>
            </div>

            <!-- Password -->
            <div class="col-md-12 mb-2">
              <label class="form-label fw-semibold required">Password</label>
              <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Masukkan password..."
                required>
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

{{-- ══════════════════════════════════════════════════
     MODAL — Konfirmasi Hapus
══════════════════════════════════════════════════ --}}

<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

      <div class="d-flex justify-content-center mb-3">
        <div style="width:56px;height:56px;border-radius:50%;background:#FCEBEB;display:flex;align-items:center;justify-content:center;">
          <i class="bi bi-trash3" style="font-size:1.6rem;color:#A32D2D;"></i>
        </div>
      </div>

      <h5 class="fw-semibold mb-1">Hapus Data Ini?</h5>
      <p class="text-muted mb-4" style="font-size:.875rem;line-height:1.6;">
        Tindakan ini tidak dapat dibatalkan. Data user
        <strong id="namaUserHapus" class="text-body"></strong>
        akan dihapus secara permanen.
      </p>

      <div class="d-flex gap-2 justify-content-center">
        <button
          type="button"
          class="btn btn-secondary btn-sm px-4"
          data-bs-dismiss="modal">
          Batal
        </button>
        <form id="formHapusUser" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm px-4">
            <i class="bi bi-trash3 me-1"></i>Ya, Hapus
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
function bukaPegingatanHapus(nama, tombol) {
    document.getElementById('namaUserHapus').textContent = nama;

    // Jika pakai route Laravel, ambil dari data attribute tombol:
    const url = tombol.dataset.url;
    document.getElementById('formHapusUser').action = url;

    new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus')).show();
}
</script>

@endsection