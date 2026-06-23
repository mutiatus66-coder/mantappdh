{{-- resources/views/master/penilaian/tahap2/show.blade.php --}}
@extends('index', ['dummy' => true])
@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush
@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(245,158,11,0.10); border:1px solid rgba(245,158,11,0.3); color:#92400e; margin:0 20px;">
    <i class="bi bi-check-circle-fill me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<div class="page-container">
    {{-- HEADER --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="ec-title">Penilaian Tahap 2 — {{ $subEvent['sub_event'] }}</h3>
            <p class="ec-subtitle">Tahun {{ $subEvent['tahun'] }}</p>
        </div>
        <a href="{{ route('penilaian.tahap2.index') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-umum"
                    type="button">
                Umum
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-pelajar"
                    type="button">
                Pelajar
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Panel UMUM --}}
        <div class="tab-pane fade show active" id="tab-umum" role="tabpanel">
            @include('master.penilaian.tahap2.panel', [
                'title'        => 'Nominasi Umum',
                'tableId'      => 'tbl-nominasi-umum',
                'filename'     => 'nominasi-umum-tahap2',
                'group'        => 'umum',
                'nominasi'     => $nominasiUmum,
                'penilai'      => $penilai,
                'penilaiLogin' => $penilaiLogin,
                'rankingLogin' => $rankingLogin,
            ])
        </div>
        {{-- Panel PELAJAR --}}
        <div class="tab-pane fade" id="tab-pelajar" role="tabpanel">
            @include('master.penilaian.tahap2.panel', [
                'title'        => 'Nominasi Pelajar',
                'tableId'      => 'tbl-nominasi-pelajar',
                'filename'     => 'nominasi-pelajar-tahap2',
                'group'        => 'pelajar',
                'nominasi'     => $nominasiPelajar,
                'penilai'      => $penilai,
                'penilaiLogin' => $penilaiLogin,
                'rankingLogin' => $rankingLogin,
            ])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const subEventId = {{ $subEvent['id'] }};

    // ── Excel Export ──────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableId  = this.dataset.table;
            const filename = this.dataset.filename ?? 'export';
            const table    = document.getElementById(tableId);
            if (!table) return;

            // Pakai SheetJS jika tersedia, fallback ke blob CSV
            if (typeof XLSX !== 'undefined') {
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                XLSX.writeFile(wb, filename + '.xlsx');
            } else {
                // Fallback sederhana: download HTML table sebagai .xls
                const html = table.outerHTML;
                const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
                const a    = document.createElement('a');
                a.href     = URL.createObjectURL(blob);
                a.download = filename + '.xls';
                a.click();
            }
        });
    });

    // ── Simpan Ranking ────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-simpan-ranking').forEach(btn => {
        btn.addEventListener('click', async function () {
            const group = this.dataset.group;
            const pane  = document.getElementById('tab-' + group);
            if (!pane) return;

            const inputs  = pane.querySelectorAll('.input-ranking');
            const ranking = {};
            let valid     = true;

            inputs.forEach(inp => {
                const val = inp.value.trim();
                if (val === '') return;                        // boleh kosong
                const num = parseInt(val, 10);
                if (isNaN(num) || num < 1) {
                    inp.classList.add('is-invalid');
                    valid = false;
                } else {
                    inp.classList.remove('is-invalid');
                    ranking[inp.dataset.usulanId] = num;
                }
            });

            if (!valid) {
                showToast('danger', 'Nilai ranking harus berupa angka ≥ 1.');
                return;
            }

            if (Object.keys(ranking).length === 0) {
                showToast('warning', 'Belum ada ranking yang diisi.');
                return;
            }

            const btnEl = this;
            btnEl.disabled = true;
            btnEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan…';

            try {
                const res = await fetch(`/penilaian/tahap-2/${subEventId}/ranking`, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ ranking }),
                });
                const data = await res.json();
                if (data.success) {
                    showToast('success', data.message ?? 'Ranking berhasil disimpan.');
                    updateTotalRank(pane, ranking);
                } else {
                    showToast('danger', data.message ?? 'Gagal menyimpan ranking.');
                }
            } catch (e) {
                showToast('danger', 'Terjadi kesalahan jaringan.');
            } finally {
                btnEl.disabled = false;
                btnEl.innerHTML = '<i class="bi bi-trophy me-1"></i>Simpan Ranking';
            }
        });
    });

    // Setelah simpan ranking → update kolom "Total Rank" di baris terkait
    // (hanya tambah nilai baru ke total yang sudah ada; idealnya reload, tapi
    //  untuk UX langsung kita set nilai input saja — total rank dihitung server)
    function updateTotalRank(pane, ranking) {
        // Tidak bisa menghitung total rank dari sisi klien (perlu data penilai lain)
        // Cukup tandai baris sudah diranking
        Object.entries(ranking).forEach(([usulanId, rank]) => {
            const cell = pane.querySelector(`.rv-total-rank[data-usulan-id="${usulanId}"]`);
            if (cell) {
                // Refresh total rank dengan reload ringan setelah semua selesai
                cell.innerHTML = `<span class="badge bg-secondary">${rank}</span>`;
            }
        });
    }

    // ── Toast Helper ──────────────────────────────────────────────────────────
    function showToast(type, message) {
        const colors = {
            success: '#198754',
            danger:  '#dc3545',
            warning: '#856404',
            info:    '#0dcaf0',
        };
        const bg = {
            success: 'rgba(25,135,84,0.1)',
            danger:  'rgba(220,53,69,0.1)',
            warning: 'rgba(245,158,11,0.1)',
            info:    'rgba(13,202,240,0.1)',
        };

        const wrap = document.createElement('div');
        wrap.style.cssText = `
            position:fixed; top:20px; right:20px; z-index:9999;
            background:${bg[type] ?? bg.info};
            border:1px solid ${colors[type] ?? colors.info};
            color:${colors[type] ?? colors.info};
            padding:12px 20px; border-radius:8px;
            font-size:0.875rem; font-weight:500;
            max-width:360px; box-shadow:0 4px 12px rgba(0,0,0,0.1);
            animation: fadeIn .2s ease;
        `;
        wrap.textContent = message;
        document.body.appendChild(wrap);
        setTimeout(() => wrap.remove(), 3500);
    }

})();
</script>
@endpush