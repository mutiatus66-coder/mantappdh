@extends('index', ['dummy' => true])

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}">
@endpush

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
              <a href="{{ route('indikator.tahap1.inovasi', $subEventId) }}" class="btn btn-dark">
                ← Kembali
              </a>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKeterangan">
              Tambah Keterangan
              </button>
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
                    <td>{{ $item['keterangan'] }}</td>
                    <td style="text-align:center;">{{ $item['nilai_minimal'] }}</td>
                    <td style="text-align:center;">{{ $item['nilai_maksimal'] }}</td>
                    <td style="text-align:center;">
                      <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-warning btn-edit-keterangan"
                                data-id="{{ $item['id'] }}"
                                data-keterangan="{{ $item['keterangan'] }}"
                                data-nilai-minimal="{{ $item['nilai_minimal'] }}"
                                data-nilai-maksimal="{{ $item['nilai_maksimal'] }}">
                          Edit 
                        </button>
                        <button class="btn btn-danger btn-hapus-keterangan"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['keterangan'] }}"
                                data-url="{{ route('indikator.tahap1.detail.destroy', [$subEventId, $indikatorId, $item['id']]) }}">
                          Hapus 
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
          <button type="button" class="btn btn-dark" style="min-width:120px; height:42px;" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success px-4">Simpan</button>
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
        <button type="button" class="btn btn-dark" style="min-width:120px; height:42px;" data-bs-dismiss="modal">Batal</button>
        <form id="formHapusKeterangan" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger" style="min-width:120px; height:42px;">
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