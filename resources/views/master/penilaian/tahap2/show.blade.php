{{-- resources/views/master/penilaian/tahap2/show.blade.php --}}
@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
{{--
    PENTING: Jangan pakai datatables.css lokal — versi lama (1.x) akan
    membuat tampilan DT v2.x + ColumnControl berantakan. Pakai CDN. —Regan.
--}}
<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.css"
      rel="stylesheet"
      integrity="sha384-wExd39N36yrzP/MYKag3xdBw+uoLSMRfH0f2+A/gxs5f3COtMPq/+indiwzt2Bcm"
      crossorigin="anonymous">
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
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    {{-- ── Tabs ── --}}
    <div class="rv-tabs-wrap">
        <ul class="nav rv-tabs" id="tabNominator" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn active" id="tab-umum-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-umum"
                        type="button" role="tab">
                    Umum
                    @php $countUmum = count($nominasiUmum); @endphp
                    @if($countUmum > 0)
                        <span class="rv-tab-count">{{ $countUmum }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="rv-tab-btn" id="tab-pelajar-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-pelajar"
                        type="button" role="tab">
                    Pelajar
                    @php $countPelajar = count($nominasiPelajar); @endphp
                    @if($countPelajar > 0)
                        <span class="rv-tab-count">{{ $countPelajar }}</span>
                    @endif
                </button>
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
{{--
    CDN wajib di-load di sini (bukan di panel) agar tidak terduplikat.
    Panel di-include 2x, kalau CDN ada di panel maka jQuery + DT di-load 2x.
--}}
<script src="{{ asset('assets/jquery/jquery-4.0.0.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"
        integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"
        integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.3.8/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cc-1.2.1/r-3.0.8/datatables.min.js"
        integrity="sha384-R/5yB/Q48CmXPUHiIs/s7Oi2np8MQlE/bd774P/X5aCQMbUHQgY0MXTaPFUCd/GZ"
        crossorigin="anonymous"></script>

{{--
    @stack('dt-init') harus diletakkan SETELAH CDN DT di atas.
    Panel menaruh inisialisasi DataTables-nya ke stack ini via @push('dt-init').
    Dengan urutan ini: CDN siap dulu → baru init per-tabel dijalankan.
--}}
@stack('dt-init')

<script>
(function () {
    'use strict';

    const subEventId = {{ $subEvent['id'] }};
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ════════════════════════════════════════════════════════════════
    // TOAST
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
    // SIMPAN RANKING
    // (btn-auto-ranking sudah ditangani di dalam panel via DT API)
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

            const orig      = this.innerHTML;
            this.disabled   = true;
            this.innerHTML  = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

            try {
                const res  = await fetch(`/penilaian/tahap-2/${subEventId}/ranking`, {
                    method : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept'      : 'application/json',
                    },
                    body: JSON.stringify({ ranking }),
                });
                const data = await res.json();
                if (data.success) {
                    toast(data.message ?? 'Ranking berhasil disimpan.', 'success');
                } else {
                    toast(data.message ?? 'Gagal menyimpan ranking.', 'error');
                }
            } catch {
                toast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.disabled  = false;
                this.innerHTML = orig;
            }
        });
    });

})();
</script>
@endpush