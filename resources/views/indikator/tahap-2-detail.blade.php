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
/* .btn-tambah {
    background: #1d4ed8;
    color: white !important;
    padding: 8px 18px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .875rem;
    text-decoration: none;
} */
.btn-tambah:hover { opacity: 0.88; color: white !important; }
/* .btn-kembali {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
    padding: 8px 18px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .875rem;
    text-decoration: none;
} */
.btn-kembali:hover { opacity: 0.88; color: white !important; }
/* .btn-edit {
    background: #f59e0b;
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .18s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
} */
.btn-edit:hover { opacity: .88; color: white !important; }
/* .btn-hapus {
    background: #A32D2D;
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
} */
.btn-hapus:hover { background: #8b2424; color: white !important; }
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
    padding: 32px 20px;
    color: var(--ri-text-muted);
}
.group-header td {
    background: #fef9ec !important;
    color: #d97706;
    font-weight: 700;
    text-align: center;
    font-size: 0.9rem;
    padding: 10px 16px;
    border-bottom: 1px solid #fde68a;
}
[data-bs-theme="dark"] .group-header td {
    background: rgba(245,158,11,0.12) !important;
    color: #fbbf24;
    border-bottom-color: rgba(245,158,11,0.2);
}
.sub-event-label {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1d4ed8;
    margin-bottom: 16px;
}
.hapus-icon-circle {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: #FCEBEB;
    display: flex; align-items: center; justify-content: center;
}
[data-bs-theme="dark"] .hapus-icon-circle { background: rgba(163,45,45,0.20); }
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

        {{-- Tombol atas --}}
        <div class="d-flex gap-2 mb-4">
          <button class="btn btn-primary" id="btnTambahIndikator">
            Tambah Indikator
          </button>
          <a href="{{ route('indikator.tahap2') }}" class="btn btn-secondary">
            Kembali
          </a>
        </div>

        <div class="sub-card">
          <div class="sub-event-label">
            Sub Event : {{ $subEvent->sub_event }}
          </div>

          <div style="overflow-x: auto;">
            <table class="se-table">
              <thead>
                <tr>
                  <th width="50">No</th>
                  <th>Indikator</th>
                  <th>Keterangan</th>
                  <th width="120">Nilai Minimal</th>
                  <th width="120">Nilai Maksimal</th>
                  <th width="160" style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $jenisList = ['Subtansi Inovasi', 'Peragaan'];
                  $no = 1;
                @endphp

                @foreach($jenisList as $jenis)
                  @php
                    $indikatorsByJenis = $indikators->where('jenis', $jenis);
                  @endphp

                  <tr class="group-header">
                    <td colspan="6">Indikator {{ $jenis }}</td>
                  </tr>

                  @forelse($indikatorsByJenis as $ind)
                    @forelse($ind->keterangans as $ket)
                      <tr>
                        <td style="text-align:center;">{{ $no++ }}</td>
                        <td>{{ $ind->nama_indikator }}</td>
                        <td>{{ $ket->keterangan }}</td>
                        <td style="text-align:center;">{{ $ket->nilai_minimal }}</td>
                        <td style="text-align:center;">{{ $ket->nilai_maksimal }}</td>
                        <td style="text-align:center;">
                          <div class="d-flex gap-1 justify-content-center">
                            <button class="btn btn-warning"
                                    data-id="{{ $ket->id }}"
                                    data-indikator-id="{{ $ind->id }}"
                                    data-nama-indikator="{{ $ind->nama_indikator }}"
                                    data-jenis="{{ $ind->jenis }}"
                                    data-keterangan="{{ $ket->keterangan }}"
                                    data-nilai-minimal="{{ $ket->nilai_minimal }}"
                                    data-nilai-maksimal="{{ $ket->nilai_maksimal }}">
                              Edit
                            </button>
                            <button class="btn btn-danger btn-hapus-indikator"
                                    data-id="{{ $ket->id }}"
                                    data-url="{{ route('indikator.tahap2.indikator.destroy', [$subEvent->id, $ket->id]) }}">
                             Hapus
                            </button>
                          </div>
                        </td>
                      </tr>
                    @empty
                      {{-- indikator ada tapi belum punya keterangan --}}
                      <tr>
                        <td style="text-align:center;">{{ $no++ }}</td>
                        <td>{{ $ind->nama_indikator }}</td>
                        <td colspan="3" style="color:var(--ri-text-muted); font-style:italic;">Belum ada keterangan</td>
                        <td style="text-align:center;">
                          <div class="d-flex gap-1 justify-content-center">
                            <button class="btn-edit btn-edit-indikator"
                                    data-id=""
                                    data-indikator-id="{{ $ind->id }}"
                                    data-nama-indikator="{{ $ind->nama_indikator }}"
                                    data-jenis="{{ $ind->jenis }}"
                                    data-keterangan=""
                                    data-nilai-minimal=""
                                    data-nilai-maksimal="">
                              <i class="bi bi-pencil-square"></i> Edit
                            </button>
                          </div>
                        </td>
                      </tr>
                    @endforelse
                  @empty
                    <tr>
                      <td colspan="6" class="empty-row">Belum ada indikator {{ $jenis }}</td>
                    </tr>
                  @endforelse
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


{{-- ══ MODAL — Tambah / Edit Indikator ══ --}}
<div class="modal fade" id="modalIndikator" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow-lg">
      <form id="formIndikator" method="POST" action="">
        @csrf
        <input type="hidden" name="_method" id="formIndikatorMethod" value="POST">
        <input type="hidden" name="keterangan_id" id="hiddenKetId" value="">

        <div class="modal-header px-5 py-4">
          <h5 class="modal-title fw-semibold" id="modalIndikatorTitle">Tambah Indikator Nominator</h5>
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                  data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg fs-5"></i>
          </button>
        </div>

        <div class="modal-body px-5 py-4">
          <div class="mb-4">
            <label class="form-label fw-semibold">Sub Event</label>
            <input type="text" class="form-control" value="{{ $subEvent->sub_event }}" disabled>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold required">Indikator</label>
            <input type="text" name="nama_indikator" id="inputNamaIndikator"
                   class="form-control" placeholder="Nama indikator..." required>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold required">Jenis Indikator</label>
            <select name="jenis" id="inputJenis" class="form-select" required>
              <option value="Subtansi Inovasi">Subtansi Inovasi</option>
              <option value="Peragaan">Peragaan</option>
            </select>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold required">Keterangan</label>
            <input type="text" name="keterangan" id="inputKeterangan"
                   class="form-control" placeholder="Keterangan..." required>
          </div>

          <div class="row">
            <div class="col-6 mb-4">
              <label class="form-label fw-semibold required">Nilai Minimal</label>
              <input type="number" name="nilai_minimal" id="inputNilaiMinimal"
                     class="form-control" placeholder="0" min="0" required>
            </div>
            <div class="col-6 mb-4">
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


{{-- ══ MODAL — Konfirmasi Hapus ══ --}}
<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">
      <div class="d-flex justify-content-center mb-3">
        <div class="hapus-icon-circle">
          <i class="bi bi-trash3" style="font-size:1.6rem; color:#A32D2D;"></i>
        </div>
      </div>
      <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
      <p class="mb-4" style="font-size:.875rem; color:#6b7280;">
        Tindakan ini tidak dapat dibatalkan.
      </p>
      <div class="d-flex gap-2 justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
        <form id="formHapus" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm px-4">
            <i class="bi bi-trash3 me-1"></i> Ya, Hapus
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl = "{{ route('indikator.tahap2.indikator.store', $subEvent->id) }}";

    // ── Reset modal ──
    document.getElementById('modalIndikator').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formIndikator').action = storeUrl;
        document.getElementById('formIndikatorMethod').value = 'POST';
        document.getElementById('modalIndikatorTitle').textContent = 'Tambah Indikator Nominator';
        document.getElementById('inputNamaIndikator').value = '';
        document.getElementById('inputJenis').value = 'Subtansi Inovasi';
        document.getElementById('inputKeterangan').value = '';
        document.getElementById('inputNilaiMinimal').value = '';
        document.getElementById('inputNilaiMaksimal').value = '';
        document.getElementById('hiddenKetId').value = '';
    });

    // ── Tombol Tambah ──
    document.getElementById('btnTambahIndikator').addEventListener('click', function () {
        new bootstrap.Modal(document.getElementById('modalIndikator')).show();
    });

    // ── Tombol Edit ──
    document.querySelectorAll('.btn-edit-indikator').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalIndikatorTitle').textContent = 'Edit Indikator Nominator';
            document.getElementById('formIndikatorMethod').value = 'PUT';

            const ketId = this.dataset.id;
            const indId = this.dataset.indikatorId;

            document.getElementById('hiddenKetId').value = ketId;
            document.getElementById('formIndikator').action =
                `/indikator/tahap-2/{{ $subEvent->id }}/indikator/${ketId}`;

            document.getElementById('inputNamaIndikator').value = this.dataset.namaIndikator;
            document.getElementById('inputJenis').value         = this.dataset.jenis;
            document.getElementById('inputKeterangan').value    = this.dataset.keterangan;
            document.getElementById('inputNilaiMinimal').value  = this.dataset.nilaiMinimal;
            document.getElementById('inputNilaiMaksimal').value = this.dataset.nilaiMaksimal;

            new bootstrap.Modal(document.getElementById('modalIndikator')).show();
        });
    });

    // ── Tombol Hapus ──
    document.querySelectorAll('.btn-hapus-indikator').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('formHapus').action = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapus')).show();
        });
    });

});
</script>

@endsection