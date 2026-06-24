{{-- resources/views/master/penilaian/tahap2/show.blade.php --}}
@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="all-container">

    {{-- ── Flash Success ── --}}
    @if(session('success'))
    <div class="alert alert-dismissible fade show mb-4" role="alert"
        style="background:rgba(245,158,11,0.10); border:1px solid rgba(245,158,11,0.3); color:#92400e;">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Header ── --}}
    <div class="rv-page-header">
        <div>
            <p class="rv-sub-label">Sub Event</p>
            <h3 class="rv-page-title">{{ $subEvent['sub_event'] }}</h3>
        </div>
        <a href="{{ route('penilaian.tahap2.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    {{-- ── Keterangan Status ── --}}
    <div class="alert alert-info d-flex gap-3 flex-wrap align-items-center mb-3" style="font-size:.875rem">
        <span><span class="badge bg-secondary me-1"><i class="bi bi-dash-circle"></i></span> Belum ada ranking</span>
        <span><span class="badge rv-rank-badge rv-rank-top me-1"></span>- 3 Besar Akan Tersorot</span>
    </div>

    {{-- ── Tabs ── --}}
    <div class="rv-tabs-wrap">
        <ul class="nav rv-tabs" id="tabNominator" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn active" id="tab-umum-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-umum"
                        type="button" role="tab">Umum</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn" id="tab-pelajar-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-pelajar"
                        type="button" role="tab">Pelajar</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="tabNominatorContent">
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

    // ════════════════════════════════════════════════════════════════
    // TOAST — disamakan dengan sistem ri-toast milik Tahap 1
    // ════════════════════════════════════════════════════════════════
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `ri-toast ri-toast-${type === 'success' ? 'success' : 'error'}`;
        el.innerHTML = `
            <span class="ri-toast-icon">
              <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'}"></i>
            </span>
            <span class="ri-toast-msg">${msg}</span>
            <button class="ri-toast-close" onclick="this.parentElement.remove()">
              <i class="bi bi-x-lg"></i>
            </button>`;
        document.body.appendChild(el);
        requestAnimationFrame(() => el.classList.add('ri-toast-show'));
        setTimeout(() => {
            el.classList.remove('ri-toast-show');
            setTimeout(() => el.remove(), 300);
        }, 3500);
    }

    // ════════════════════════════════════════════════════════════════
    // HELPER: render badge ranking di kolom "Total Rank"
    // ════════════════════════════════════════════════════════════════
    function renderRankBadge(rankNum) {
        if (!rankNum || rankNum < 1) {
            return `<span class="rv-rank-empty" style="color:var(--ri-text-muted)">-</span>`;
        }
        const cls = rankNum <= 3 ? 'rv-rank-top' : 'rv-rank-normal';
        return `<span class="badge rv-rank-badge ${cls}">${rankNum}</span>`;
    }

    // Helper re-nomori kolom "No" setelah sort
    function renumberRows(tbody) {
        tbody.querySelectorAll('tr[data-id]').forEach((tr, idx) => {
            const noCell = tr.querySelector('.row-no');
            if (noCell) noCell.textContent = idx + 1;
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TOMBOL RANKING — sort by total nilai desc → isi input + badge
    // ════════════════════════════════════════════════════════════════
    document.querySelectorAll('.btn-auto-ranking').forEach(btn => {
        btn.addEventListener('click', function () {
            const group = this.dataset.group;
            const tbody = document.getElementById('tbody-' + group);
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
            if (rows.length === 0) return;

            rows.sort((a, b) => {
                const na = parseFloat(a.dataset.nilai) || 0;
                const nb = parseFloat(b.dataset.nilai) || 0;
                return nb - na;
            });

            rows.forEach((tr, idx) => {
                const rank = idx + 1;

                const inp = tr.querySelector('.input-ranking');
                if (inp) inp.value = rank;

                const rankCell = tr.querySelector('.rv-total-rank');
                if (rankCell) rankCell.innerHTML = renderRankBadge(rank);

                tbody.appendChild(tr);

                tr.classList.remove('row-sorted');
                void tr.offsetWidth;
                tr.classList.add('row-sorted');
            });

            renumberRows(tbody);
        });
    });

    // ════════════════════════════════════════════════════════════════
    // SIMPAN RANKING
    // ════════════════════════════════════════════════════════════════
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
                if (val === '') return;
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
                toast('Nilai ranking harus berupa angka ≥ 1.', 'error');
                return;
            }
            if (Object.keys(ranking).length === 0) {
                toast('Belum ada ranking yang diisi. Tekan tombol Ranking terlebih dahulu.', 'error');
                return;
            }

            const btnEl = this;
            const orig  = btnEl.innerHTML;
            btnEl.disabled  = true;
            btnEl.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

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
                    toast(data.message ?? 'Ranking berhasil disimpan.', 'success');
                } else {
                    toast(data.message ?? 'Gagal menyimpan ranking.', 'error');
                }
            } catch (e) {
                toast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                btnEl.disabled  = false;
                btnEl.innerHTML = orig;
            }
        });
    });

    // ════════════════════════════════════════════════════════════════
    // EXCEL EXPORT
    // ════════════════════════════════════════════════════════════════
    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const tableId  = this.dataset.table;
            const filename = this.dataset.filename ?? 'export';
            const table    = document.getElementById(tableId);
            if (!table) return;

            if (typeof XLSX !== 'undefined') {
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
                XLSX.writeFile(wb, filename + '.xlsx');
            } else {
                const blob = new Blob([table.outerHTML], { type: 'application/vnd.ms-excel' });
                const a    = document.createElement('a');
                a.href     = URL.createObjectURL(blob);
                a.download = filename + '.xls';
                a.click();
            }
        });
    });

})();
</script>
@endpush