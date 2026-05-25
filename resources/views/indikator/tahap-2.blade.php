@extends('index', ['dummy' => true])

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('template.demo6/demo6/assets/css/indikator.css') }}">
@endpush


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

        @if($errors->has('total'))
          <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('total') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <div class="sub-card">
          <div class="mb-4">
            <h3 class="fw-bold m-0" style="font-size:1.3rem; color:var(--ri-text-primary);">
              Setting Indikator Penilaian Tahap 2
            </h3>
          </div>

          <div style="overflow-x: auto;">
            <table class="se-table">
              <thead>
                <tr>
                  <th width="60">No</th>
                  <th>Sub Event</th>
                  <th width="180">Detail Indikator</th>
                  <th width="180">Detail Formulasi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($subEvents ?? [] as $item)
                  <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                    <td>{{ $item['sub_event'] }}</td>
                    <td style="text-align:center;">
                      @if($detailValid[$item['id']] ?? false)
                       <a href="{{ route('indikator.tahap2.indikator', $item['id']) }}"class="btn-detail-indikator">
                          <i></i> Detail
                       </a>
                      @else
                    <button class="btn-detail-indikator"
                      style="background:#9ca3af; cursor:not-allowed; opacity:0.7;"
                      title="Isi formulasi hingga 100% terlebih dahulu"disabled>
                        <i></i> Detail
                    </button>
                     @endif
                    </td>
                    <td style="text-align:center;">
                      @if(in_array($item['id'], $formulasis ?? []))
                        {{-- Sudah ada formulasi — tombol Detail hijau --}}
                        <button class="btn-detail-formulasi btn-open-formulasi"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['sub_event'] }}">
                          <i></i> Detail
                        </button>
                      @else
                        {{-- Belum ada — tombol Tambah Formulasi --}}
                        <button class="btn-tambah-formulasi btn-open-formulasi"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['sub_event'] }}">
                          <i></i> Tambah Formulasi
                        </button>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="empty-row">
                      <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                      Belum ada data sub event
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
     MODAL — Tambah / Edit Formulasi Nilai
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalFormulasi" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">
      <form id="formFormulasi" method="POST" action="">
        @csrf

        <div class="modal-header px-5 py-4" style="border-bottom: 1px solid #e5e7eb;">
          <h5 class="modal-title fw-semibold" id="modalFormulasiTitle">Tambah Formulasi Nilai</h5>
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                  data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg fs-5"></i>
          </button>
        </div>

        <div class="modal-body px-5 py-4">

          {{-- Sub Event --}}
          <div class="mb-4">
            <label class="form-label fw-semibold">Sub Event</label>
            <div class="input-icon-group">
              <input type="text" id="inputSubEventName" class="form-control" disabled>
              <span class="icon-badge" style="background:#2563eb;">
                <i class="bi bi-info-lg"></i>
              </span>
            </div>
          </div>

          {{-- Nilai Inovasi --}}
          <div class="mb-4">
            <label class="form-label fw-semibold required">Nilai Inovasi</label>
            <div class="input-icon-group">
              <input type="number" name="nilai_inovasi" id="inputNilaiInovasi"
                     class="form-control" placeholder="0" min="1" max="100"
                     required oninput="hitungTotal()">
              <span class="icon-badge" style="background:#16a34a;">%</span>
            </div>
          </div>

          {{-- Nilai Peragaan --}}
          <div class="mb-4">
            <label class="form-label fw-semibold required">Nilai Peragaan</label>
            <div class="input-icon-group">
              <input type="number" name="nilai_peragaan" id="inputNilaiPeragaan"
                     class="form-control" placeholder="0" min="1" max="100"
                     required oninput="hitungTotal()">
              <span class="icon-badge" style="background:#16a34a;">%</span>
            </div>
          </div>

          {{-- Preview Total --}}
          <div id="totalPreview" class="total-preview" style="display:none;">
            Total: <span id="totalAngka">0</span>%
            <span id="totalStatus"></span>
          </div>

          {{-- Catatan --}}
          <p class="mt-3 mb-0" style="font-size:0.82rem; color:#dc2626; font-weight:500;">
            <i class="bi bi-exclamation-circle me-1"></i>
            Catatan: Nilai Inovasi dan Nilai Peragaan jika ditotal harus menjadi 100%.
          </p>

        </div>

        <div class="modal-footer px-5 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnSimpan" class="btn btn-primary px-4" disabled>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// ── Hitung total realtime ──────────────────────────────
function hitungTotal() {
    const inovasi  = parseInt(document.getElementById('inputNilaiInovasi').value)  || 0;
    const peragaan = parseInt(document.getElementById('inputNilaiPeragaan').value) || 0;
    const total    = inovasi + peragaan;
    const preview  = document.getElementById('totalPreview');
    const angka    = document.getElementById('totalAngka');
    const status   = document.getElementById('totalStatus');
    const btnSimpan = document.getElementById('btnSimpan');

    preview.style.display = 'block';
    angka.textContent = total;

    if (total === 100) {
        preview.className = 'total-preview total-ok';
        status.textContent = ' ✓ Valid';
        btnSimpan.disabled = false;
    } else {
        preview.className = 'total-preview total-warn';
        status.textContent = total < 100
            ? ` (kurang ${100 - total}%)`
            : ` (lebih ${total - 100}%)`;
        btnSimpan.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', function () {

    // ── Buka modal saat tombol diklik ──
    document.querySelectorAll('.btn-open-formulasi').forEach(btn => {
        btn.addEventListener('click', function () {
            const subEventId   = this.dataset.id;
            const subEventNama = this.dataset.nama;

            document.getElementById('modalFormulasiTitle').textContent =
                this.classList.contains('btn-tambah-formulasi')
                    ? 'Tambah Formulasi Nilai'
                    : 'Detail Formulasi Nilai';

            document.getElementById('inputSubEventName').value = subEventNama;
            document.getElementById('formFormulasi').action =
                `/indikator/tahap-2/${subEventId}/formulasi`;

            // Reset field
            document.getElementById('inputNilaiInovasi').value  = '';
            document.getElementById('inputNilaiPeragaan').value = '';
            document.getElementById('totalPreview').style.display = 'none';
            document.getElementById('btnSimpan').disabled = true;

            // Jika sudah ada data, fetch dan isi
            @foreach($subEvents as $item)
            @if(in_array($item['id'], $formulasis ?? []))
            if (subEventId == '{{ $item['id'] }}') {
                // Ambil data formulasi via fetch
                fetch(`/indikator/tahap-2/{{ $item['id'] }}/formulasi/get`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('inputNilaiInovasi').value  = data.nilai_inovasi;
                        document.getElementById('inputNilaiPeragaan').value = data.nilai_peragaan;
                        hitungTotal();
                    });
            }
            @endif
            @endforeach

            new bootstrap.Modal(document.getElementById('modalFormulasi')).show();
        });
    });

});
</script>

@endsection