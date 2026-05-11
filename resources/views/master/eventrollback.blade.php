@extends('index', ['dummy' => true])

@section('content')

<style>
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