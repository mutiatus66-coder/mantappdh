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
        <div class="alert alert-dismissible fade show mb-4 custom-alert-success" role="alert">
         <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
          <button type="button"
            class="btn-close custom-close-btn"
            data-bs-dismiss="alert"
            aria-label="Close">
           </button>
         </div>
        @endif

        <div class="sub-card">
          <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
              <h3 class="fw-bold m-0" style="font-size:1.5rem; color:var(--ri-text-primary);">Data Detail Inovasi</h3>
              <p class="m-0 mt-1" style="font-size:1rem; font-weight:700; color:#2563eb;">
                Sub Event : {{ $subEventName ?? 'N/A' }}
              </p>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('indikator.tahap1') }}" class="btn btn-dark">
                 ← Kembali
              </a>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIndikator">
                Tambah Indikator
              </button>
            </div>
          </div>

          <div style="overflow-x: auto;">
            <table class="se-table">
              <thead>
                <tr>
                  <th width="60">No</th>
                  <th>Indikator</th>
                  <th width="160">Detail Indikator</th>
                  <th width="220">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($indikators ?? [] as $item)
                  <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                    <td>{{ $item['nama_indikator'] ?? '-' }}</td>
                    <td style="text-align:center;">
                      <a href="{{ route('indikator.tahap1.detail', [$subEventId, $item['id']]) }}"
                         class="btn btn-warning btn-detail">
                        <i></i> Detail
                      </a>
                    </td>
                    <td style="text-align:center;">
                      <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-warning btn-edit-indikator"
                                data-id="{{ $item['id'] }}"
                                data-indikator="{{ $item['nama_indikator'] ?? '-' }}">
                           Ubah
                        </button>
                        <button class="btn btn-danger btn-hapus-indikator"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['nama_indikator'] ?? '-' }}"
                                data-url="{{ route('indikator.tahap1.inovasi.destroy', [$subEventId, $item['id']]) }}">
                          Hapus
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="empty-row">
                      <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                      Belum ada data indikator
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
     MODAL — Tambah / Edit Indikator
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalIndikator" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">
      <form id="formIndikator" method="POST" action="{{ route('indikator.tahap1.inovasi.store', $subEventId) }}">
        @csrf
        <input type="hidden" name="_method" id="formIndikatorMethod" value="POST">

        <div class="modal-header px-5 py-4">
          <h5 class="modal-title fw-semibold" id="modalIndikatorTitle">Tambah Indikator Inovasi</h5>
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                  data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg fs-5"></i>
          </button>
        </div>

        <div class="modal-body px-5 py-4">
          <div class="mb-4">
            <label class="form-label fw-semibold">Sub Event</label>
            <input type="text" class="form-control" value="{{ $subEventName ?? '' }}" disabled>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold required">Indikator</label>
            <input type="text" name="nama_indikator" id="inputNamaIndikator"
                   class="form-control" placeholder="Masukkan nama indikator..." required>
          </div>
        </div>

        <div class="modal-footer px-5 py-3">
          <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success px-4">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL — Konfirmasi Hapus
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalHapusIndikator" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

      <div class="d-flex justify-content-center mb-3">
        <div class="hapus-icon-circle">
          <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
        </div>
      </div>

      <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
      <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
        Tindakan ini tidak dapat dibatalkan. Indikator
        <strong id="namaIndikatorHapus" class="hapus-nama-strong"></strong>
        akan dihapus secara permanen.
      </p>

      <div class="d-flex gap-2 justify-content-center">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
        <form id="formHapusIndikator" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash3 me-1"></i>Ya, Hapus
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl  = "{{ route('indikator.tahap1.inovasi.store', $subEventId) }}";
    const subEventId = "{{ $subEventId }}";

    // ── Reset modal ke mode Tambah saat ditutup ──
    document.getElementById('modalIndikator').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formIndikator').action = storeUrl;
        document.getElementById('formIndikatorMethod').value = 'POST';
        document.getElementById('modalIndikatorTitle').textContent = 'Tambah Indikator Inovasi';
        document.getElementById('inputNamaIndikator').value = '';
    });

    // ── Tombol Ubah ──
    document.querySelectorAll('.btn-edit-indikator').forEach(btn => {
        btn.addEventListener('click', function () {
            const id        = this.dataset.id;
            const indikator = this.dataset.indikator;

            document.getElementById('modalIndikatorTitle').textContent = 'Ubah Indikator';
            document.getElementById('formIndikator').action = `/indikator/tahap-1/${subEventId}/inovasi/${id}`;
            document.getElementById('formIndikatorMethod').value = 'PUT';
            document.getElementById('inputNamaIndikator').value = indikator;

            new bootstrap.Modal(document.getElementById('modalIndikator')).show();
        });
    });

    // ── Tombol Hapus ──
    document.querySelectorAll('.btn-hapus-indikator').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaIndikatorHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusIndikator').action = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusIndikator')).show();
        });
    });

});
</script>

@endsection