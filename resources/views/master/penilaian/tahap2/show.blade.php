@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="all-container">

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

    {{-- ── Warning jika tidak ada data lolos ── --}}
    @if(empty($nominasiUmum) && empty($nominasiPelajar))
    <div class="alert mb-4 d-flex align-items-center gap-3"
         style="background:rgba(234,179,8,0.08); border:1px solid rgba(234,179,8,0.3); color:#854d0e; border-radius:10px; padding:16px 20px;">
        <i class="bi bi-exclamation-triangle-fill fs-5" style="color:#ca8a04;"></i>
        <div>
            <div class="fw-semibold">Belum ada data yang lolos Tahap 1</div>
            <div class="small mt-1">Silakan tentukan kelulusan peserta di
                <a href="{{ route('penilaian.tahap1.show', $subEvent['id']) }}" class="fw-semibold" style="color:#92400e;">Penilaian Tahap 1</a>
                terlebih dahulu.
            </div>
        </div>
    </div>
    @endif

    {{-- ── Tabs ── --}}
    <div class="rv-tabs-wrap">
        <ul class="nav rv-tabs" id="tabNominator" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn active"
                        id="tab-umum" data-bs-toggle="tab" data-bs-target="#panel-umum"
                        type="button" role="tab">Umum</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn"
                        id="tab-pelajar" data-bs-toggle="tab" data-bs-target="#panel-pelajar"
                        type="button" role="tab">Pelajar</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="tabNominatorContent">

        {{-- ── Tab Umum ── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">
            @include('master.penilaian.tahap2.panel', [
                'group'    => 'umum',
                'title'    => 'Nominator Umum',
                'tableId'  => 'tableUmum',
                'filename' => 'nominasi-umum',
                'nominasi' => $nominasiUmum,
                'penilai'  => $penilai,
                'indikators'            => $indikators,
                'penilaiLogin'          => $penilaiLogin,
                'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
            ])
        </div>

        {{-- ── Tab Pelajar ── --}}
        <div class="tab-pane fade" id="panel-pelajar" role="tabpanel">
            @include('master.penilaian.tahap2.panel', [
                'group'    => 'pelajar',
                'title'    => 'Nominator Pelajar',
                'tableId'  => 'tablePelajar',
                'filename' => 'nominasi-pelajar',
                'nominasi' => $nominasiPelajar,
                'penilai'  => $penilai,
                'indikators'            => $indikators,
                'penilaiLogin'          => $penilaiLogin,
                'nilaiLoginPerInovator' => $nilaiLoginPerInovator,
            ])
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const NILAI_URL = '{{ route("penilaian.tahap2.simpan.nilai", $subEvent["id"]) }}';
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const nilaiDb = @json($nilaiLoginPerInovator ?? []);

    const cap = s => s.charAt(0).toUpperCase() + s.slice(1);

    // ── Modal Input Nilai Tahap 2 ─────────────────────────────────────────
    let activeInovatorId = null;

    document.querySelectorAll('.btn-input-nilai-t2').forEach(btn => {
        btn.addEventListener('click', function () {
            activeInovatorId = this.dataset.inovatorId;

            document.querySelectorAll('.modal-inovator-nama-t2').forEach(el => el.textContent = this.dataset.inovator);
            document.querySelectorAll('.modal-inovasi-nama-t2').forEach(el => el.textContent = this.dataset.namaInovasi);

            const savedNilai = nilaiDb[activeInovatorId] ?? {};
            document.querySelectorAll('.input-nilai-item-t2').forEach(inp => {
                const kid = inp.dataset.keteranganId;
                inp.value = savedNilai[kid] !== undefined ? savedNilai[kid] : '';
            });

            const group   = this.closest('.rv-card')
                            ?.querySelector('.btn-simpan-nilai-modal-t2')?.dataset.group ?? 'umum';
            const modalId = 'modalNilaiTahap2' + cap(group);
            bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId)).show();
        });
    });

    document.querySelectorAll('.btn-simpan-nilai-modal-t2').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!activeInovatorId) return;

            const group = this.dataset.group;
            const nilai = {};
            document.querySelectorAll('.input-nilai-item-t2[data-group="' + group + '"]').forEach(inp => {
                if (inp.value !== '') nilai[inp.dataset.keteranganId] = parseInt(inp.value, 10);
            });

            if (Object.keys(nilai).length === 0) {
                toast('Isi minimal satu nilai terlebih dahulu.', 'error');
                return;
            }

            const orig    = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            fetch(NILAI_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ usulan_id: activeInovatorId, nilai }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toast('Nilai berhasil disimpan!', 'success');
                    nilaiDb[activeInovatorId] = Object.assign(nilaiDb[activeInovatorId] ?? {}, nilai);
                    const modalId = 'modalNilaiTahap2' + cap(group);
                    bootstrap.Modal.getInstance(document.getElementById(modalId))?.hide();
                } else {
                    toast(data.message ?? 'Gagal menyimpan nilai.', 'error');
                }
            })
            .catch(() => toast('Terjadi kesalahan jaringan.', 'error'))
            .finally(() => { this.disabled = false; this.innerHTML = orig; });
        });
    });

    // ── Rangking ──
    document.querySelectorAll('.btn-rv-rank').forEach(btn => {
        btn.addEventListener('click', function () {
            const tbody = document.querySelector('#' + this.dataset.table + ' tbody');
            if (!tbody) return;

            const rows = [...tbody.querySelectorAll('tr')].filter(row => row.querySelector('.rv-nilai'));
            rows.sort((a, b) => {
                const nilaiA = parseFloat(a.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                const nilaiB = parseFloat(b.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                return nilaiB - nilaiA;
            });

            rows.forEach((row, i) => {
                const rankCell = row.querySelector('.rv-rank-cell');
                if (rankCell) rankCell.textContent = i + 1;
                const no = row.querySelector('.row-no');
                if (no) no.textContent = i + 1;
                tbody.appendChild(row);
            });
        });
    });

    // ── Export CSV ──
    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const table = document.getElementById(this.dataset.table);
            if (!table) return;

            const csv = [...table.querySelectorAll('tr')].map(row =>
                [...row.querySelectorAll('th, td')]
                    .map(c => '"' + c.innerText.trim().replace(/"/g, '""') + '"')
                    .join(',')
            ).join('\n');

            const a = document.createElement('a');
            a.href     = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = this.dataset.filename + '.csv';
            a.click();
        });
    });

    // ── Toast ──
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

});
</script>
@endpush