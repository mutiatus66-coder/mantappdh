@extends('index')

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

{{-- warning/message --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.2); color:#1e40af; margin: 0 24px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="all-container">

    {{-- ── header ── --}}
    <div class="rv-page-header">
        <div>
            <p class="rv-sub-label">Sub Event</p>
            <h3 class="rv-page-title">{{ $subEvent['sub_event'] }}</h3>
        </div>
        <a href="{{ route('penilaian.tahap1.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    {{-- ── tab ── --}}
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

        {{-- ── bagian umum ── --}}
        <div class="tab-pane fade show active" id="panel-umum" role="tabpanel">
            @include('master.penilaian.tahap1.panel', [
                'group'    => 'umum',
                'title'    => 'Verifikasi Umum',
                'tableId'  => 'tableUmum',
                'filename' => 'verifikasi-umum',
                'nominasi' => $nominasiUmum,
                'penilai'  => $penilai,
            ])
        </div>

        {{-- ── bagian pelajar ── --}}
        <div class="tab-pane fade" id="panel-pelajar" role="tabpanel">
            @include('master.penilaian.tahap1.panel', [
                'group'    => 'pelajar',
                'title'    => 'Verifikasi Pelajar',
                'tableId'  => 'tablePelajar',
                'filename' => 'verifikasi-pelajar',
                'nominasi' => $nominasiPelajar,
                'penilai'  => $penilai,
            ])
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const SIMPAN_URL = '{{ route("penilaian.tahap1.simpan", $subEvent["id"]) }}';
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function buildGroup(name) {
        const cap = s => s.charAt(0).toUpperCase() + s.slice(1);
        return {
            name,
            get rows()    { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]`)] },
            get checked() { return [...document.querySelectorAll(`.chk-row[data-group="${name}"]:checked`)] },
            get checkAll(){ return document.querySelector(`.chk-all[data-group="${name}"]`) },
            get bar()     { return document.getElementById(`simpanBar${cap(name)}`) },
            get count()   { return document.querySelector(`#simpanBar${cap(name)} .simpan-count`) },
        };
    }

    const groups = { umum: buildGroup('umum'), pelajar: buildGroup('pelajar') };

    function syncUI(g, fromInit = false) {
        const total = g.rows.length;
        const n     = g.checked.length;

        g.rows.forEach(chk => {
            chk.closest('tr').classList.toggle('row-lolos', chk.checked);
        });

        if (g.checkAll) {
            g.checkAll.indeterminate = n > 0 && n < total;
            g.checkAll.checked       = total > 0 && n === total;
        }

        if (!fromInit) g.bar.style.display = n > 0 ? 'flex' : 'none';
        g.count.textContent = n;
    }

    Object.values(groups).forEach(g => syncUI(g, true));

    document.querySelectorAll('.chk-row').forEach(chk => {
        chk.addEventListener('change', function () { syncUI(groups[this.dataset.group]); });
    });

    document.querySelectorAll('.chk-all').forEach(chkAll => {
        chkAll.addEventListener('change', function () {
            const g = groups[this.dataset.group];
            g.rows.forEach(chk => chk.checked = this.checked);
            syncUI(g);
        });
    });

    document.querySelectorAll('.btn-rv-simpan').forEach(btn => {
        btn.addEventListener('click', function () {
            const g   = groups[this.dataset.group];
            const ids = g.checked.map(c => c.dataset.id);

            fetch(SIMPAN_URL, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ kategori: g.name, ids }),
            })
            .then(r => r.json())
            .then(data => toast(
                data.success ? 'Data berhasil disimpan!' : 'Gagal menyimpan data.',
                data.success ? 'success' : 'error'
            ))
            .catch(() => toast('Terjadi kesalahan.', 'error'));
        });
    });

    // Rangking
    document.querySelectorAll('.btn-rv-rank').forEach(btn => {
    btn.addEventListener('click', function () {
        const tbody = document.querySelector(`#${this.dataset.table} tbody`);
        if (!tbody) return;

        [...tbody.querySelectorAll('tr')]
            .filter(row => row.querySelector('.rv-nilai')) // skip baris "belum ada data"
            .sort((a, b) => {
                const nilaiA = parseFloat(a.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                const nilaiB = parseFloat(b.querySelector('.rv-nilai')?.dataset.nilai) || 0;
                return nilaiB - nilaiA; // descending: nilai tertinggi di atas
            })
            .forEach((row, i) => {
                const no = row.querySelector('.row-no');
                if (no) no.textContent = i + 1; // update nomor urut
                tbody.appendChild(row);
            });
        });
    });

    // Expor
    document.querySelectorAll('.btn-rv-excel').forEach(btn => {
        btn.addEventListener('click', function () {
            const table = document.getElementById(this.dataset.table);
            if (!table) return;
            const csv = [...table.querySelectorAll('tr')].map(row =>
                [...row.querySelectorAll('th, td')].slice(1)
                    .map(c => `"${c.innerText.trim().replace(/"/g, '""')}"`)
                    .join(',')
            ).join('\n');
            const a = document.createElement('a');
            a.href     = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = `${this.dataset.filename}.csv`;
            a.click();
        });
    });

    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `rv-toast ${type}`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'}"></i>${msg}`;
        document.body.appendChild(el);
        requestAnimationFrame(() => el.classList.add('show'));
        setTimeout(() => {
            el.classList.remove('show');
            el.addEventListener('transitionend', () => el.remove(), { once: true });
        }, 2800);
    }

});
</script>
@endpush