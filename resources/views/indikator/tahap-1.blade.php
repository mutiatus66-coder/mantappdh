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
              Setting Indikator Penilaian Tahap 1 &amp; Formulasi Nilai
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
                      @if($detailValid1[$item['id']] ?? false)
                        <a href="{{ route('indikator.tahap1.inovasi', $item['id']) }}"
                           class="btn-detail-indikator">
                          <i></i> Detail
                        </a>
                      @else
                        <button class="btn-detail-indikator"
                                style="background:#9ca3af; cursor:not-allowed; opacity:0.7;"
                                title="Isi formulasi hingga 100% terlebih dahulu" disabled>
                          <i></i> Detail
                        </button>
                      @endif
                    </td>
                    <td style="text-align:center;">
                      @if(in_array($item['id'], $formulasis1 ?? []))
                        <button class="btn-detail-formulasi btn-open-formulasi1"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['sub_event'] }}">
                          <i></i> Detail
                        </button>
                      @else
                        <button class="btn-tambah-formulasi btn-open-formulasi1"
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


{{-- ══ MODAL — Tambah / Edit Formulasi Nilai Tahap 1 ══ --}}
<div class="modal fade" id="modalFormulasi1" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">
      <form id="formFormulasi1" method="POST" action="">
        @csrf

        <div class="modal-header px-5 py-4" style="border-bottom: 1px solid #e5e7eb;">
          <h5 class="modal-title fw-semibold" id="modalFormulasi1Title">Tambah Formulasi Nilai</h5>
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                  data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg fs-5"></i>
          </button>
        </div>

        <div class="modal-body px-5 py-4">

          <div class="mb-4">
            <label class="form-label fw-semibold">Sub Event</label>
            <div class="input-icon-group">
              <input type="text" id="inputSubEventName1" class="form-control" disabled>
              <span class="icon-badge" style="background:#2563eb;">
                <i class="bi bi-info-lg"></i>
              </span>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold required">Nilai Makalah</label>
            <div class="input-icon-group">
              <input type="number" name="nilai_makalah" id="inputNilaiMakalah"
                     class="form-control" placeholder="0" min="1" max="100"
                     required oninput="hitungTotal1()">
              <span class="icon-badge" style="background:#dedede;">%</span>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold required">Nilai Substansi Inovasi</label>
            <div class="input-icon-group">
              <input type="number" name="nilai_substansi" id="inputNilaiSubstansi"
                     class="form-control" placeholder="0" min="1" max="100"
                     required oninput="hitungTotal1()">
              <span class="icon-badge" style="background:#dedede;">%</span>
            </div>
          </div>

          <div id="totalPreview1" class="total-preview" style="display:none;">
            Total: <span id="totalAngka1">0</span>%
            <span id="totalStatus1"></span>
          </div>

          <p class="mt-3 mb-0" style="font-size:0.82rem; color:#dc2626; font-weight:500;">
            <i class="bi bi-exclamation-circle me-1"></i>
            Catatan: Nilai makalah dan nilai substansi jika ditotal harus menjadi 100%.
          </p>

        </div>

        <div class="modal-footer px-5 py-3">
          <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnSimpan1" class="btn btn-success px-4" disabled>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function hitungTotal1() {
    const makalah   = parseInt(document.getElementById('inputNilaiMakalah').value)   || 0;
    const substansi = parseInt(document.getElementById('inputNilaiSubstansi').value) || 0;
    const total     = makalah + substansi;
    const preview   = document.getElementById('totalPreview1');
    const angka     = document.getElementById('totalAngka1');
    const status    = document.getElementById('totalStatus1');
    const btnSimpan = document.getElementById('btnSimpan1');

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

    document.getElementById('modalFormulasi1').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formFormulasi1').action = '';
        document.getElementById('modalFormulasi1Title').textContent = 'Tambah Formulasi Nilai';
        document.getElementById('inputNilaiMakalah').value   = '';
        document.getElementById('inputNilaiSubstansi').value = '';
        document.getElementById('totalPreview1').style.display = 'none';
        document.getElementById('btnSimpan1').disabled = true;
    });

    document.querySelectorAll('.btn-open-formulasi1').forEach(btn => {
        btn.addEventListener('click', function () {
            const subEventId   = this.dataset.id;
            const subEventNama = this.dataset.nama;

            document.getElementById('modalFormulasi1Title').textContent =
                this.classList.contains('btn-tambah-formulasi')
                    ? 'Tambah Formulasi Nilai'
                    : 'Edit Formulasi Nilai';

            document.getElementById('inputSubEventName1').value = subEventNama;
            document.getElementById('formFormulasi1').action =
                `/indikator/tahap-1/${subEventId}/formulasi`;

            document.getElementById('inputNilaiMakalah').value   = '';
            document.getElementById('inputNilaiSubstansi').value = '';
            document.getElementById('totalPreview1').style.display = 'none';
            document.getElementById('btnSimpan1').disabled = true;

            @foreach($subEvents as $item)
            @if(in_array($item['id'], $formulasis1 ?? []))
            if (subEventId == '{{ $item['id'] }}') {
                fetch(`/indikator/tahap-1/{{ $item['id'] }}/formulasi/get`)
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        document.getElementById('inputNilaiMakalah').value   = data.nilai_makalah;
                        document.getElementById('inputNilaiSubstansi').value = data.nilai_substansi;
                        hitungTotal1();
                    })
                    .catch(err => {
                        console.error('Gagal memuat data formulasi tahap 1:', err);
                        alert('Gagal memuat data formulasi. Silakan coba lagi.');
                    });
            }
            @endif
            @endforeach

            new bootstrap.Modal(document.getElementById('modalFormulasi1')).show();
        });
    });

});
</script>

@endsection