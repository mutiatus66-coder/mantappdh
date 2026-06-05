@extends('index', ['dummy' => true])

@section('content')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">

{{-- ══ FLASH MESSAGES ══ --}}
@if(session('success'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(0,172,193,0.10); border:1px solid rgba(0,172,193,0.3); color:#006064; margin: 0 20px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-dismissible fade show mb-4" role="alert"
     style="background:rgba(163,45,45,0.1); border:1px solid rgba(163,45,45,0.3); color:#A32D2D; margin: 0 20px;">
    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══ MAIN CONTAINER ══ --}}
<div class="bidang-container">

    <div class="bidang-header">
        <div class="bidang-title">
            <h3>Master Bidang</h3>
            <p>Kelola bidang untuk setiap sub event</p>
        </div>
    </div>

    {{-- ══ LOOP PER EVENT (PARENT) ══ --}}
    @forelse($events as $event)
    <div class="mb-4">

        {{-- Event Label --}}
        <div class="px-1 mb-2 d-flex align-items-center gap-2">
            <span class="badge bg-secondary text-uppercase" style="font-size:.7rem; letter-spacing:.05em;">
                {{ $event->jenis }}
            </span>
            <h6 class="mb-0 fw-bold" style="color:var(--ri-text-primary); font-size:.95rem;">
                {{ $event->nama_event }}
            </h6>
        </div>

        {{-- Accordion Sub Events --}}
        <div class="accordion" id="accordion-event-{{ $event->id }}">

            @forelse($event->subEvents as $se)
            @php
                $aktif    = $se->bidangs->where('status', 'aktif')->count();
                $nonaktif = $se->bidangs->where('status', 'tidak_aktif')->count();
            @endphp

            <div class="accordion-item bidang-accordion-item mb-2">

                {{-- Accordion Header --}}
                <h2 class="accordion-header" id="heading-{{ $se->id }}">
                    <button class="accordion-button bidang-accordion-btn fw-semibold collapsed px-4"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $se->id }}"
                            aria-expanded="false"
                            aria-controls="collapse-{{ $se->id }}">
                        <span class="me-2 small text-muted">{{ $se->tahun }}</span>
                        <span class="fw-bold">{{ $se->sub_event }}</span>
                    </button>
                </h2>

                {{-- Accordion Body --}}
                <div id="collapse-{{ $se->id }}"
                     class="accordion-collapse collapse"
                     aria-labelledby="heading-{{ $se->id }}">

                    <div class="accordion-body p-4">

                        {{-- Stats + Tambah --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div class="d-flex gap-2">
                                <span class="badge-aktif rounded-pill px-3 py-2">
                                    Aktif <strong>{{ $aktif }}</strong>
                                </span>
                                <span class="badge-nonaktif rounded-pill px-3 py-2">
                                    Tidak Aktif <strong>{{ $nonaktif }}</strong>
                                </span>
                            </div>

                            <button class="btn btn-primary btn-tambah-bidang"
                                    data-sub-event-id="{{ $se->id }}"
                                    data-sub-event-nama="{{ $se->sub_event }}">
                                Tambah Bidang
                            </button>
                        </div>

                        {{-- Tabel Bidang --}}
                        <div class="table-responsive">
                            <table class="table bidang-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="60" class="text-center">No</th>
                                        <th>Bidang</th>
                                        <th width="180" class="text-center">Status</th>
                                        <th width="280" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($se->bidangs as $bidang)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst($bidang->nama) }}</td>
                                        <td class="text-center">
                                            @if($bidang->status === 'aktif')
                                                <span class="badge-aktif px-3 py-2">Aktif</span>
                                            @else
                                                <span class="badge-nonaktif px-3 py-2">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-aksi-wrap">
                                                <button class="btn btn-warning btn-ubah-bidang btn-aksi"
                                                        data-id="{{ $bidang->id }}"
                                                        data-nama="{{ $bidang->nama }}"
                                                        data-status="{{ $bidang->status }}"
                                                        data-sub-event-id="{{ $se->id }}"
                                                        data-sub-event-nama="{{ $se->sub_event }}"
                                                        data-url="{{ route('bidang.update', $bidang->id) }}">
                                                    Ubah
                                                </button>
                                                <button class="btn btn-danger btn-hapus-bidang btn-aksi"
                                                        data-id="{{ $bidang->id }}"
                                                        data-nama="{{ $bidang->nama }}"
                                                        data-url="{{ route('bidang.destroy', $bidang->id) }}">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 empty-row">
                                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                            Belum ada bidang untuk sub event ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            @empty
            <div class="text-center py-4 empty-row">
                <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
                Belum ada sub event untuk event ini.
            </div>
            @endforelse

        </div>
    </div>

    @empty
    <div class="text-center py-5 empty-row">
        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
        Belum ada data event.
    </div>
    @endforelse

</div>


{{-- ══ MODAL — Tambah / Ubah Bidang ══ --}}
<div class="modal fade" id="modalBidang" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formBidang" method="POST" action="{{ route('bidang.store') }}">
                @csrf
                <input type="hidden" name="_method"      id="formBidangMethod" value="POST">
                <input type="hidden" name="sub_event_id" id="bidangSubEventId">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalBidangTitle">Tambah Bidang</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>

                <div class="modal-body px-5 py-4">

                    <p class="mb-3" style="font-size:0.85rem; color:var(--ri-text-muted);">
                        Sub Event: <strong id="bidangSubEventNama" style="color:var(--ri-text-primary);"></strong>
                    </p>

                    <div class="mb-4">
                        <label class="form-label fw-semibold required">Nama Bidang</label>
                        <input type="text" name="nama" id="bidangNama"
                               class="form-control" placeholder="Masukkan nama bidang..." required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Status</label>
                        <div class="d-flex gap-4 mt-1">
                            <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="status" id="statusAktifBidang" value="aktif" checked> Aktif
                            </label>
                            <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                <input type="radio" name="status" id="statusNonaktifBidang" value="tidak_aktif"> Tidak Aktif
                            </label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnSimpanBidang" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Bidang ══ --}}
<div class="modal fade" id="modalHapusBidang" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-text-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4" style="font-size:.875rem; line-height:1.6; color:var(--ri-text-muted);">
                Tindakan ini tidak dapat dibatalkan. Bidang
                <strong id="namaBidangHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusBidang" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="btnHapusBidang" class="btn btn-danger">Hapus</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl = "{{ route('bidang.store') }}";
    const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Helper: kirim request AJAX ──
    async function sendRequest(url, method, data) {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });
        return res.json();
    }

    // ── Helper: toast notifikasi ──
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `alert alert-dismissible fade show position-fixed bottom-0 end-0 m-4`;
        el.style.cssText = `z-index:9999; min-width:280px; background:${type === 'success' ? 'rgba(0,172,193,0.15)' : 'rgba(163,45,45,0.12)'}; border:1px solid ${type === 'success' ? 'rgba(0,172,193,0.4)' : 'rgba(163,45,45,0.3)'}; color:${type === 'success' ? '#006064' : '#A32D2D'};`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'} me-2"></i>${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    // ── Helper: update baris tabel tanpa reload ──
    function updateRow(id, nama, status) {
        const rows = document.querySelectorAll('.btn-ubah-bidang');
        rows.forEach(btn => {
            if (btn.dataset.id == id) {
                const tr = btn.closest('tr');
                // Update nama
                tr.cells[1].textContent = nama.charAt(0).toUpperCase() + nama.slice(1);
                // Update status badge
                tr.cells[2].innerHTML = status === 'aktif'
                    ? `<span class="badge-aktif px-3 py-2">Aktif</span>`
                    : `<span class="badge-nonaktif px-3 py-2">Tidak Aktif</span>`;
                // Update data-attribute tombol
                btn.dataset.nama   = nama;
                btn.dataset.status = status;
            }
        });
    }

    // ── Helper: tambah baris baru ke tabel ──
    function appendRow(bidang, subEventId) {
        const tbody = document.querySelector(`#collapse-${subEventId} tbody`);
        if (!tbody) return;

        // Hapus baris "belum ada data" jika ada
        const emptyRow = tbody.querySelector('.empty-row');
        if (emptyRow) emptyRow.closest('tr').remove();

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center">${rowCount}</td>
            <td>${bidang.nama.charAt(0).toUpperCase() + bidang.nama.slice(1)}</td>
            <td class="text-center">
                ${bidang.status === 'aktif'
                    ? `<span class="badge-aktif px-3 py-2">Aktif</span>`
                    : `<span class="badge-nonaktif px-3 py-2">Tidak Aktif</span>`}
            </td>
            <td class="text-center">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-ubah-bidang btn-aksi"
                            data-id="${bidang.id}"
                            data-nama="${bidang.nama}"
                            data-status="${bidang.status}"
                            data-sub-event-id="${subEventId}"
                            data-sub-event-nama="${bidang.sub_event_nama ?? ''}"
                            data-url="${bidang.update_url}">
                        Ubah
                    </button>
                    <button class="btn btn-danger btn-hapus-bidang btn-aksi"
                            data-id="${bidang.id}"
                            data-nama="${bidang.nama}"
                            data-url="${bidang.destroy_url}">
                        Hapus
                    </button>
                </div>
            </td>`;
        tbody.appendChild(tr);

        // Re-attach event listener pada tombol baru
        tr.querySelector('.btn-ubah-bidang').addEventListener('click', handleUbah);
        tr.querySelector('.btn-hapus-bidang').addEventListener('click', handleHapus);
    }

    // ── Reset modal ──
    function resetModal() {
        document.getElementById('formBidangMethod').value         = 'POST';
        document.getElementById('modalBidangTitle').textContent   = 'Tambah Bidang';
        document.getElementById('bidangNama').value               = '';
        document.getElementById('bidangSubEventId').value         = '';
        document.getElementById('bidangSubEventNama').textContent = '';
        document.getElementById('statusAktifBidang').checked      = true;
        document.getElementById('btnSimpanBidang').disabled       = false;
        document.getElementById('btnSimpanBidang').textContent    = 'Simpan';
    }

    document.getElementById('modalBidang').addEventListener('hidden.bs.modal', resetModal);

    // ── Tambah Bidang ──
    document.querySelectorAll('.btn-tambah-bidang').forEach(btn => {
        btn.addEventListener('click', function () {
            resetModal();
            document.getElementById('bidangSubEventId').value         = this.dataset.subEventId;
            document.getElementById('bidangSubEventNama').textContent = this.dataset.subEventNama;
            document.getElementById('modalBidangTitle').textContent   = 'Tambah Bidang';
            new bootstrap.Modal(document.getElementById('modalBidang')).show();
        });
    });

    // ── Handler Ubah (bisa dipanggil ulang untuk baris baru) ──
    function handleUbah() {
        resetModal();
        document.getElementById('modalBidangTitle').textContent   = 'Ubah Bidang';
        document.getElementById('formBidangMethod').value         = 'PUT';
        document.getElementById('bidangSubEventId').value         = this.dataset.subEventId;
        document.getElementById('bidangSubEventNama').textContent = this.dataset.subEventNama;
        document.getElementById('bidangNama').value               = this.dataset.nama;
        document.getElementById('btnSimpanBidang').dataset.updateId  = this.dataset.id;
        document.getElementById('btnSimpanBidang').dataset.updateUrl = this.dataset.url;

        const radioId = this.dataset.status === 'tidak_aktif'
            ? 'statusNonaktifBidang' : 'statusAktifBidang';
        document.getElementById(radioId).checked = true;

        new bootstrap.Modal(document.getElementById('modalBidang')).show();
    }

    document.querySelectorAll('.btn-ubah-bidang').forEach(btn => {
        btn.addEventListener('click', handleUbah);
    });

    // ── Submit AJAX (Tambah & Ubah) ──
    document.getElementById('btnSimpanBidang').addEventListener('click', async function () {
        const method    = document.getElementById('formBidangMethod').value;
        const nama      = document.getElementById('bidangNama').value.trim();
        const status    = document.querySelector('input[name="status"]:checked').value;
        const subEventId = document.getElementById('bidangSubEventId').value;

        if (!nama) {
            document.getElementById('bidangNama').focus();
            return;
        }

        this.disabled     = true;
        this.textContent  = 'Menyimpan...';

        try {
            const url  = method === 'PUT' ? this.dataset.updateUrl : storeUrl;
            const data = { nama, status, sub_event_id: subEventId, _method: method };
            const res  = await sendRequest(url, 'POST', data);

            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalBidang')).hide();
                toast(method === 'PUT' ? 'Bidang berhasil diubah!' : 'Bidang berhasil ditambahkan!');

                if (method === 'PUT') {
                    updateRow(this.dataset.updateId, nama, status);
                } else {
                    appendRow(res.bidang, subEventId);
                }
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
                this.disabled    = false;
                this.textContent = 'Simpan';
            }
        } catch {
            toast('Terjadi kesalahan.', 'error');
            this.disabled    = false;
            this.textContent = 'Simpan';
        }
    });

    // ── Handler Hapus ──
    function handleHapus() {
        document.getElementById('namaBidangHapus').textContent  = this.dataset.nama;
        document.getElementById('formHapusBidang').dataset.id   = this.dataset.id;
        document.getElementById('formHapusBidang').dataset.url  = this.dataset.url;
        document.getElementById('formHapusBidang').dataset.nama = this.dataset.nama;
        new bootstrap.Modal(document.getElementById('modalHapusBidang')).show();
    }

    document.querySelectorAll('.btn-hapus-bidang').forEach(btn => {
        btn.addEventListener('click', handleHapus);
    });

    // ── Submit Hapus AJAX ──
    document.getElementById('btnHapusBidang').addEventListener('click', async function () {
        const url  = document.getElementById('formHapusBidang').dataset.url;
        const id   = document.getElementById('formHapusBidang').dataset.id;
        const nama = document.getElementById('formHapusBidang').dataset.nama;

        this.disabled    = true;
        this.textContent = 'Menghapus...';

        try {
            const res = await sendRequest(url, 'POST', { _method: 'DELETE' });
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalHapusBidang')).hide();
                toast(`Bidang "${nama}" berhasil dihapus!`);
                // Hapus baris dari tabel
                document.querySelectorAll('.btn-hapus-bidang').forEach(btn => {
                    if (btn.dataset.id == id) btn.closest('tr').remove();
                });
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch {
            toast('Terjadi kesalahan.', 'error');
        }

        this.disabled    = false;
        this.textContent = 'Hapus';
    });

});
</script>
@endpush