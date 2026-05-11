@extends('index', ['dummy' => true])

@section('content')

<style>
  .btn-warning {
    background: #65A605 !important;
    border-color: #65A605 !important;
    color: #fff !important;
}
.btn-warning:hover {
    background: #538a04 !important;
    border-color: #538a04 !important;
}
.sub-card {
    background: var(--ri-card-bg);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
    margin: 20px;
    transition: background 0.2s, color 0.2s;
    border: none;
     overflow: hidden;
}
.btn-tambah-se {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .9rem;
    box-shadow: 0 3px 12px rgba(245,158,11,.30);
}
.btn-tambah-se:hover {
    opacity: 0.9;
    box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    color: white !important;
}
.btn-gold {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .18s;
}
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
.se-table tr:hover td { background: var(--ri-table-row-hover); }
.se-table tr:last-child td { border-bottom: none; }
.empty-row {
    text-align: center;
    padding: 40px 20px;
    color: var(--ri-text-muted);
    background: var(--ri-table-row-bg);
}

/* ── Hapus modal icon ── */
.hapus-icon-circle {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: #FCEBEB;
    display: flex; align-items: center; justify-content: center;
}
[data-bs-theme="dark"] .hapus-icon-circle { background: rgba(163,45,45,0.20); }
[data-bs-theme="dark"] .hapus-teks-muted  { color: rgba(245,240,232,.55) !important; }
[data-bs-theme="dark"] .hapus-nama-strong { color: #F5F0E8 !important; }
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
              <p class="m-0" style="color:var(--ri-text-muted); font-size:0.875rem;">Kelola semua event yang terdaftar</p>
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
                  <th>Nama Event</th>
                  <th style="text-align:center;">Jenis</th>
                  <th width="180" style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($events ?? [] as $item)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_event }}</td>
                    <td style="text-align:center;">{{ $item->jenis }}</td>
                    <td style="text-align:center;">
                      <div class="d-flex align-items-center justify-content-center gap-1">

                        <button class="btn-gold btn-sm btn-edit-event"
                                data-id="{{ $item->id }}"
                                data-nama-event="{{ $item->nama_event }}"
                                data-jenis="{{ $item->jenis }}">
                          <i class="bi bi-pencil-square me-1"></i>Ubah
                        </button>

                        <button class="btn-hapus btn-sm btn-hapus-event"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->nama_event }}"
                                data-url="{{ route('event.destroy', $item->id) }}">
                          <i class="bi bi-trash3 me-1"></i>Hapus
                        </button>

                      </div>
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
          </div>

        </div>
      </div>
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL — Tambah / Edit Event
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

            <div class="col-md-12 mb-4">
              <label class="form-label fw-semibold required">Jenis</label>
              <select name="jenis" id="inputJenis" class="form-select" required>
                <option value="" disabled selected>-- Pilih Jenis --</option>
                <option value="INOTEK">INOTEK</option>
                <option value="INODA">INODA</option>
              </select>
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
        Tindakan ini tidak dapat dibatalkan. Data event
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
        document.getElementById('inputJenis').value = '';
    });

    // ── Tombol Ubah ──
    document.querySelectorAll('.btn-edit-event').forEach(btn => {
        btn.addEventListener('click', function () {
            const id        = this.dataset.id;
            const namaEvent = this.dataset.namaEvent;
            const jenis     = this.dataset.jenis;

            document.getElementById('modalEventTitle').textContent = 'Ubah Event';
            document.getElementById('formEvent').action = `/event/${id}`;
            document.getElementById('formEventMethod').value = 'PUT';

            document.getElementById('inputNamaEvent').value = namaEvent;
            document.getElementById('inputJenis').value = jenis;

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