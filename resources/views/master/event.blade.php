@extends('index', ['dummy' => true])

@section('content')

<style>
<<<<<<< Updated upstream
=======
/* ══════════════════════════════════════════════════
   EVENT PAGE — Theme-aware card + gold buttons
══════════════════════════════════════════════════ */

/* ══════════════════════════════════════════════════
   EVENT PAGE — Light clean style (sesuai gambar)
══════════════════════════════════════════════════ */

/* ── Card shell ── */
>>>>>>> Stashed changes
.sub-card {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
    border: none;
    overflow: hidden;
<<<<<<< Updated upstream
}

.btn-tambah-se {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
    padding: 10px 20px;
    border-radius: 8px;
=======
    box-shadow: 0 1px 8px rgba(0,0,0,.08);
}

/* ── Card header ── */
[data-bs-theme="light"] .sub-card .card-header,
[data-bs-theme="dark"] .sub-card .card-header {
    background: #ffffff;
    border-bottom: 1px solid #f0f0f0;
}

/* ── Card body ── */
[data-bs-theme="light"] .sub-card .card-body,
[data-bs-theme="dark"] .sub-card .card-body {
    background: #ffffff;
}

/* ── Card title ── */
[data-bs-theme="light"] .sub-card .card-title,
[data-bs-theme="dark"] .sub-card .card-title {
    color: #1a1a1a !important;
    font-weight: 700;
}

/* ── Table head ── */
[data-bs-theme="light"] .se-table thead th,
[data-bs-theme="dark"] .se-table thead th {
    background: transparent;
    color: #aaaaaa;
    border-bottom: 1px solid #eeeeee;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
}

/* ── Table body rows ── */
[data-bs-theme="light"] .se-table tbody td,
[data-bs-theme="dark"] .se-table tbody td {
    color: #333333;
    border-bottom: 1px solid #f0f0f0;
    background: transparent;
}

[data-bs-theme="light"] .se-table tbody tr:hover td,
[data-bs-theme="dark"] .se-table tbody tr:hover td {
    background: #fafafa;
}

/* empty row */
[data-bs-theme="light"] .se-table .se-empty,
[data-bs-theme="dark"] .se-table .se-empty {
    color: #aaaaaa;
}

/* ── Gold gradient button (Edit) ── */
.btn-gold {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff !important;
    font-weight: 700;
    border: none;
    border-radius: .375rem;
    padding: .35rem .85rem;
    font-size: .8rem;
    letter-spacing: .02em;
    box-shadow: 0 2px 8px rgba(245,158,11,.30);
    transition: .18s ease-in-out;
    cursor: pointer;
}

.btn-gold:hover {
    background: linear-gradient(135deg, #e69009 0%, #c96d05 100%);
    box-shadow: 0 4px 14px rgba(245,158,11,.45);
    color: #ffffff !important;
    transform: translateY(-1px);
}


/* ── Tambah Event button ── */
.btn-tambah-se {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #ffffff !important;
    font-weight: 700;
>>>>>>> Stashed changes
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .9rem;
<<<<<<< Updated upstream
    box-shadow: 0 3px 12px rgba(245,158,11,.30);
=======
    box-shadow: 0 2px 8px rgba(245,158,11,.30);
    transition: .18s ease-in-out;
    cursor: pointer;
>>>>>>> Stashed changes
}

.btn-tambah-se:hover {
<<<<<<< Updated upstream
    opacity: 0.9;
    box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    color: white !important;
}

.btn-gold {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
=======
    background: linear-gradient(135deg, #e69009 0%, #c96d05 100%);
    box-shadow: 0 4px 14px rgba(245,158,11,.45);
    color: #ffffff !important;
    transform: translateY(-1px);
}

/* ── Modal backdrop ── */
.modal-backdrop.show { opacity: .35; }

/* ── Modal content ── */
[data-bs-theme="light"] .modal-content,
[data-bs-theme="dark"] .modal-content {
    background: #ffffff;
    border: none;
}
[data-bs-theme="light"] .modal-header,
[data-bs-theme="dark"] .modal-header,
[data-bs-theme="light"] .modal-footer,
[data-bs-theme="dark"] .modal-footer {
    border-color: #f0f0f0 !important;
}
[data-bs-theme="light"] .modal-title,
[data-bs-theme="dark"] .modal-title {
    color: #1a1a1a;
    font-weight: 700;
}
[data-bs-theme="light"] .form-label,
[data-bs-theme="dark"] .form-label {
    color: #555555;
}
[data-bs-theme="light"] .form-control,
[data-bs-theme="dark"] .form-control,
[data-bs-theme="light"] .form-select,
[data-bs-theme="dark"] .form-select {
    background: #ffffff !important;
    border: 1px solid #dddddd !important;
    color: #333333 !important;
}
[data-bs-theme="light"] .form-control:focus,
[data-bs-theme="dark"] .form-control:focus,
[data-bs-theme="light"] .form-select:focus,
[data-bs-theme="dark"] .form-select:focus {
    border-color: #F5A623 !important;
    box-shadow: 0 0 0 3px rgba(245,166,35,.12) !important;
    color: #333333 !important;
}
[data-bs-theme="light"] .form-control::placeholder,
[data-bs-theme="dark"] .form-control::placeholder {
    color: #aaaaaa !important;
}
[data-bs-theme="light"] .btn-active-light-primary,
[data-bs-theme="dark"] .btn-active-light-primary {
    color: #555555;
}
[data-bs-theme="light"] .btn-active-light-primary:hover,
[data-bs-theme="dark"] .btn-active-light-primary:hover {
    background: #f5f5f5;
    color: #1a1a1a;
}


/* ── Batal button ── */
[data-bs-theme="light"] .btn-modal-cancel,
[data-bs-theme="dark"] .btn-modal-cancel {
    background: #f5f5f5;
    color: #555555;
    border: 1px solid #e0e0e0;
}
[data-bs-theme="light"] .btn-modal-cancel:hover,
[data-bs-theme="dark"] .btn-modal-cancel:hover {
    background: #eeeeee;
    color: #333333;
}

.edit-text {
    color: red;
    cursor: pointer;
}

.edit-text:hover {
    text-decoration: underline;
}

.edit-text {
    color: red;
    cursor: pointer;
}

.edit-text:hover {
    text-decoration: underline;
}

/* Modal save button */
.btn-modal-save {
    background: #F5A623;
    color: #ffffff !important;
    font-weight: 700;
>>>>>>> Stashed changes
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .18s;
}
<<<<<<< Updated upstream
.btn-gold:hover { opacity: .88; color: white !important; }

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
.btn-hapus:hover { background: #8b2424; color: #ffffff !important; }

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
.se-table tr:hover td { background: var(--ri-table-row-hover); }
.se-table tr:last-child td { border-bottom: none; }
.empty-row {
    text-align: center;
    padding: 40px 20px;
    color: var(--ri-text-muted);
    background: var(--ri-table-row-bg);
}

/* ── Modal ── */
.modal-backdrop.show { opacity: .35; }

/* Hapus modal icon circle */
.hapus-icon-circle {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: var(--ri-hapus-icon-bg, #FCEBEB);
    display: flex; align-items: center; justify-content: center;
}
[data-bs-theme="dark"] .hapus-icon-circle {
    background: rgba(163,45,45,0.20);
}
[data-bs-theme="dark"] .hapus-teks-muted {
    color: rgba(245,240,232,.55) !important;
}
[data-bs-theme="dark"] .hapus-nama-strong {
    color: #F5F0E8 !important;
}
=======

.btn-modal-save:hover { opacity: .88; color: #ffffff !important; }
>>>>>>> Stashed changes
</style>

<div id="kt_content" class="content d-flex flex-column flex-column-fluid">
  <div class="p-6">
    <div class="row">
      <div class="col-12">

        @if(session('success'))
          <div class="alert alert-dismissible fade show mb-4" role="alert"
               style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#92400e;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <div class="sub-card">

          <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
              <h3 class="fw-bold m-0" style="font-size:1.5rem; color:var(--ri-text-primary);">Data Event</h3>
              <p class="m-0" style="color:var(--ri-text-muted); font-size:0.875rem;">Kelola semua event yang tersedia</p>
            </div>
            <button class="btn-tambah-se" data-bs-toggle="modal" data-bs-target="#modalEvent">
              <i class="bi bi-plus-lg"></i> Tambah Event
            </button>
          </div>

          <div style="overflow-x: auto;">
            <table class="se-table">
              <thead>
                <tr>
                  <th width="50">No</th>
                  <th>Event</th>
                  <th>Jenis</th>
                  <th width="180" style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody id="tabelEventBody">
                @forelse($events ?? [] as $item)
                  <tr>
<<<<<<< Updated upstream
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['nama_event'] }}</td>
                    <td>{{ $item['jenis'] }}</td>
                    <td style="text-align:center;">
                      <button class="btn-gold btn-sm btn-edit-event me-2"
                              data-id="{{ $item['id'] }}"
                              data-nama="{{ $item['nama_event'] }}"
                              data-jenis="{{ $item['jenis'] }}">
                        Ubah
                      </button>
                      <button class="btn-hapus btn-sm btn-hapus-event"
                              data-id="{{ $item['id'] }}"
                              data-nama="{{ $item['nama_event'] }}"
                              data-url="{{ route('event.destroy', $item['id']) }}">
                        Hapus
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="empty-row">
                      <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                      Belum ada data event
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
=======
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
                        <span class="edit-text">✏️ Edit</span>
                      </a>
                    </td>
                  </tr>
                  <tr class="border-t">
                    <td class="px-4 py-3 text-center border border-gray-300">2</td>
                    <td class="px-4 py-3 border border-gray-300">LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)</td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                      <a href="#" style="text-red-500 hover:underline"> 
                      <span class="edit-text">✏️ Edit</span>                   
                      </a>
                    </td>
                  </tr>
                  <tr class="border-t">
                    <td class="px-4 py-3 text-center border border-gray-300">3</td>
                    <td class="px-4 py-3 border border-gray-300">PAMERAN</td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                      <a href="#">
                        <span class="edit-text">✏️ Edit</span>
                        </button>
                      </a>
                    </td>
                  </tr>
                  <tr class="border-t">
                    <td class="px-4 py-3 text-center border border-gray-300">4</td>
                    <td class="px-4 py-3 border border-gray-300">INOTEK AWARD</td>
                    <td class="px-4 py-3 text-center border border-gray-300">
                      <a href="#">                        
                          <span class="edit-text">✏️ Edit</span>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
>>>>>>> Stashed changes
          </div>

        </div>
      </div>
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL — Tambah / Edit Event (reuse satu modal)
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEvent" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">

      <form id="formEvent" method="POST" action="{{ route('event.store') }}">
        @csrf
        <input type="hidden" name="_method" id="formEventMethod" value="POST">

        <div class="modal-header px-5 py-4">
          <h5 class="modal-title fw-semibold" id="modalEventTitle">Tambah Event</h5>
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                  data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg fs-5"></i>
          </button>
        </div>

        <div class="modal-body px-5 py-4">
          <div class="row">

            <div class="col-md-12 mb-4">
              <label class="form-label fw-semibold required">Nama Event</label>
              <input type="text" name="nama_event" id="inputNamaEvent"
                     class="form-control" placeholder="Masukkan nama event..." required>
            </div>

            <div class="col-md-12 mb-2">
              <label class="form-label fw-semibold">Jenis</label>
              <div class="d-flex gap-4 mt-1">
                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                  <input type="radio" name="jenis" id="jenisInotek" value="INOTEK" checked> INOTEK
                </label>
                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                  <input type="radio" name="jenis" id="jenisInoda" value="INODA"> INODA
                </label>
              </div>
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


{{-- ══════════════════════════════════════════════════
     MODAL — Konfirmasi Hapus Event
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalHapusEvent" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

      <div class="d-flex justify-content-center mb-3">
        <div class="hapus-icon-circle">
          <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
        </div>
      </div>

      <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
      <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
        Tindakan ini tidak dapat dibatalkan. Event
        <strong id="namaEventHapus" class="hapus-nama-strong"></strong>
        akan dihapus secara permanen.
      </p>

      <div class="d-flex gap-2 justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
        <form id="formHapusEvent" method="POST">
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
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl = "{{ route('event.store') }}";

    // ── Reset modal ke mode Tambah saat ditutup ──
    document.getElementById('modalEvent').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formEvent').action = storeUrl;
        document.getElementById('formEventMethod').value = 'POST';
        document.getElementById('modalEventTitle').textContent = 'Tambah Event';
        document.getElementById('inputNamaEvent').value = '';
        document.getElementById('jenisInotek').checked = true;
    });

    // ── Tombol Ubah ──
    document.querySelectorAll('.btn-edit-event').forEach(btn => {
        btn.addEventListener('click', function () {
            const id    = this.dataset.id;
            const nama  = this.dataset.nama;
            const jenis = this.dataset.jenis;

            document.getElementById('modalEventTitle').textContent = 'Ubah Event';
            document.getElementById('formEvent').action = `/event/${id}`;
            document.getElementById('formEventMethod').value = 'PUT';
            document.getElementById('inputNamaEvent').value = nama;

            if (jenis === 'INODA') {
                document.getElementById('jenisInoda').checked = true;
            } else {
                document.getElementById('jenisInotek').checked = true;
            }

            new bootstrap.Modal(document.getElementById('modalEvent')).show();
        });
    });

    // ── Tombol Hapus ──
    document.querySelectorAll('.btn-hapus-event').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaEventHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusEvent').action = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusEvent')).show();
        });
    });

});
</script>

@endsection