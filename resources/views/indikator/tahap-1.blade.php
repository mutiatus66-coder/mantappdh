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
.btn-detail-indikator {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .18s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-detail-indikator:hover { opacity: .88; color: white !important; }

.btn-detail-formulasi {
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .18s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-detail-formulasi:hover { opacity: .88; color: white !important; }

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
                    <td style="text-align:center;">{{ $item['sub_event'] }}</td>
                    <td style="text-align:center;">
                      <a href="{{ route('indikator.tahap1.indikator', $item['id']) }}"
                         class="btn-detail-indikator">
                        <i class="bi bi-search"></i> Detail
                      </a>
                    </td>
                    <td style="text-align:center;">
                      <a href="{{ route('indikator.tahap1.formulasi', $item['id']) }}"
                         class="btn-detail-formulasi">
                        <i class="bi bi-search"></i> Detail
                      </a>
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

@endsection