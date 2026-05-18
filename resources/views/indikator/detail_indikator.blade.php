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
.btn-tambah-se:hover {opacity: 0.9;
    box-shadow: 0 4px 12px rgba(245,158,11,0.3);
    color: white !important;
}
.btn-kembali {
    background: linear-gradient(135deg, #6b7280, #4b5563);
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
    text-decoration: none;
}
.btn-kembali:hover { opacity: 0.9; color: white !important; }
.btn-gold {
    background: linear-gradient(135deg, #0C4C8A, #142D54);
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .18s;
}
.btn-gold {
    background: linear-gradient(135deg, #0C4C8A, #142D54);
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
    background: var(--ri-table-head-bg, #f3f4f6);
    color: var(--ri-text-primary);
    font-weight: 600;
    padding: 12px 16px;
    border-bottom: 2px solid var(--ri-table-border-outer);
    text-align: center;
}
.se-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--ri-table-border, #e5e7eb);
    color: var(--ri-text-primary);
    vertical-align: middle;
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
               style="background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.25); color:#1e40af;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <div class="sub-card">
          <div class="mb-4 d-flex justify-content-between align-items-center">
            <p class="m-0" style="font-size:1rem; font-weight:700; color:#2563eb;">
              Indikator : {{ $indikatorName ?? 'N/A' }}
            </p>
            <div class="d-flex gap-2">
              <button class="btn-tambah-se" data-bs-toggle="modal" data-bs-target="#modalKeterangan">
                <i></i> Tambah Keterangan
              </button>
              <a href="{{ route('indikator.tahap1.inovasi', $subEventId) }}" class="btn-kembali">
                <i></i> Kembali
              </a>
            </div>
          </div>

          <div style="overflow-x: auto;">
            <table class="se-table">
              <thead>
                <tr>
                  <th width="60">No</th>
                  <th>Keterangan</th>
                  <th width="140">Nilai Minimal</th>
                  <th width="140">Nilai Maksimal</th>
                  <th width="200">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($keterangans as $item)
                  <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td style="text-align:center;">{{ $item->nilai_minimal }}</td>
                    <td style="text-align:center;">{{ $item->nilai_maksimal }}</td>
                    <td style="text-align:center;">
                      <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn-gold btn-sm btn-edit-keterangan"
                                data-id="{{ $item->id }}"
                                data-keterangan="{{ $item->keterangan }}"
                                data-nilai-minimal="{{ $item->nilai_minimal }}"
                                data-nilai-maksimal="{{ $item->nilai_maksimal }}">
                          <i></i> Edit
                        </button>
                        <button class="btn-hapus btn-sm btn-hapus-keterangan"
                                data-id="{{ $item->id }}"
                                data-nama="{{ $item->keterangan }}"
                                data-url="{{ route('indikator.tahap1.detail.destroy', [$subEventId, $indikatorId, $item->id]) }}">
                          <i></i> Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="empty-row">
                      <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                      Belum ada data keterangan
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
     MODAL — Tambah / Edit Keterangan
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalKeterangan" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">
      <form id="formKeterangan" method="POST"
            action="{{ route('indikator.tahap1.detail.store', [$subEventId, $indikatorId]) }}">
        @csrf
        <input type="hidden" name="_method" id="formKeteranganMethod" value="POST">

        <div class="modal-header px-5 py-4">
          <h5 class="modal-title fw-semibold" id="modalKeteranganTitle">Tambah Keterangan</h5>
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                  data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg fs-5"></i>
          </button>
        </div>

        <div class="modal-body px-5 py-4">
          <div class="mb-4">
            <label class="form-label fw-semibold required">Keterangan</label>
            <input type="text" name="keterangan" id="inputKeterangan"
                   class="form-control" placeholder="Masukkan keterangan..." required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold required">Nilai Minimal</label>
              <input type="number" name="nilai_minimal" id="inputNilaiMinimal"
                     class="form-control" placeholder="0" min="0" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold required">Nilai Maksimal</label>
              <input type="number" name="nilai_maksimal" id="inputNilaiMaksimal"
                     class="form-control" placeholder="0" min="0" required>
            </div>
          </div>
        </div>

        <div class="modal-footer px-5 py-3">
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
<div class="modal fade" id="modalHapusKeterangan" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

      <div class="d-flex justify-content-center mb-3">
        <div class="hapus-icon-circle">
          <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
        </div>
      </div>

      <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
      <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
        Tindakan ini tidak dapat dibatalkan. Keterangan
        <strong id="namaKeteranganHapus" class="hapus-nama-strong"></strong>
        akan dihapus secara permanen.
      </p>

      <div class="d-flex gap-2 justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
        <form id="formHapusKeterangan" method="POST">
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

    const storeUrl    = "{{ route('indikator.tahap1.detail.store', [$subEventId, $indikatorId]) }}";
    const subEventId  = "{{ $subEventId }}";
    const indikatorId = "{{ $indikatorId }}";

    // ── Reset modal ke mode Tambah saat ditutup ──
    document.getElementById('modalKeterangan').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formKeterangan').action = storeUrl;
        document.getElementById('formKeteranganMethod').value = 'POST';
        document.getElementById('modalKeteranganTitle').textContent = 'Tambah Keterangan';
        document.getElementById('inputKeterangan').value = '';
        document.getElementById('inputNilaiMinimal').value = '';
        document.getElementById('inputNilaiMaksimal').value = '';
    });

    // ── Tombol Edit ──
    document.querySelectorAll('.btn-edit-keterangan').forEach(btn => {
        btn.addEventListener('click', function () {
            const id            = this.dataset.id;
            const keterangan    = this.dataset.keterangan;
            const nilaiMinimal  = this.dataset.nilaiMinimal;
            const nilaiMaksimal = this.dataset.nilaiMaksimal;

            document.getElementById('modalKeteranganTitle').textContent = 'Ubah Keterangan';
            document.getElementById('formKeterangan').action =
                `/indikator/tahap-1/${subEventId}/detail/${indikatorId}/${id}`;
            document.getElementById('formKeteranganMethod').value = 'PUT';
            document.getElementById('inputKeterangan').value = keterangan;
            document.getElementById('inputNilaiMinimal').value = nilaiMinimal;
            document.getElementById('inputNilaiMaksimal').value = nilaiMaksimal;

            new bootstrap.Modal(document.getElementById('modalKeterangan')).show();
        });
    });

    // ── Tombol Hapus ──
    document.querySelectorAll('.btn-hapus-keterangan').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaKeteranganHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusKeterangan').action = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusKeterangan')).show();
        });
    });

});
</script>

@endsection 