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
                                <tbody id="tbody-se-{{ $se->id }}">
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
                                    <tr class="empty-row-wrapper">
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
<div class="modal fade" id="modalBidang" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalBidangTitle">Tambah Bidang</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalBidang" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <div class="modal-body px-5 py-4">

                <p class="mb-3" style="font-size:0.85rem; color:var(--ri-text-muted);">
                    Sub Event: <strong id="bidangSubEventNama" style="color:var(--ri-text-primary);"></strong>
                </p>

                <div class="mb-4">
                    <label class="form-label fw-semibold required">Nama Bidang</label>
                    <input type="text" id="bidangNama"
                           class="form-control" placeholder="Masukkan nama bidang...">
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="d-flex gap-4 mt-1">
                        <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                            <input type="radio" name="statusBidang" id="statusAktifBidang" value="aktif" checked> Aktif
                        </label>
                        <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                            <input type="radio" name="statusBidang" id="statusNonaktifBidang" value="tidak_aktif"> Tidak Aktif
                        </label>
                    </div>
                </div>

            </div>

            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalBidang">Batal</button>
                <button type="button" id="btnSimpanBidang" class="btn btn-success px-4">Simpan</button>
            </div>

        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Bidang ══ --}}
<div class="modal fade" id="modalHapusBidang" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
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
                <button type="button" class="btn btn-dark btn-aksi px-3" id="btnBatalHapusBidang">Batal</button>
                <button type="button" id="btnHapusBidang" class="btn btn-danger btn-aksi px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Konstanta ──
    const STORE_URL = "{{ route('bidang.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Modal: singleton + static backdrop ──
    const modalBidangEl = document.getElementById('modalBidang');
    const modalHapusEl  = document.getElementById('modalHapusBidang');
    const modalBidang   = new bootstrap.Modal(modalBidangEl);
    const modalHapus    = new bootstrap.Modal(modalHapusEl);

    // ── State ──
    let activeMode         = 'store';
    let activeUpdateId     = null;
    let activeUpdateUrl    = null;
    let activeSubEventId   = null;
    let activeSubEventNama = null;
    let activeHapusId      = null;
    let activeHapusUrl     = null;
    let activeHapusNama    = null;
    let isSaving           = false;
    let isDeleting         = false;

    // ────────────────────────────────────────────
    // HELPER: AJAX pakai FormData agar _method terbaca Laravel
    // ────────────────────────────────────────────
    async function sendRequest(url, data) {
        const form = new FormData();
        Object.entries(data).forEach(([k, v]) => form.append(k, v));
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: form,
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message ?? `HTTP ${res.status}`);
        }
        return res.json();
    }

    // ────────────────────────────────────────────
    // HELPER: Toast
    // ────────────────────────────────────────────
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

    // ────────────────────────────────────────────
    // HELPER: Update baris yang sudah ada
    // ────────────────────────────────────────────
    function updateRow(id, nama, status) {
        const ubahBtn = document.querySelector(`.btn-ubah-bidang[data-id="${id}"]`);
        if (!ubahBtn) return;
        const tr = ubahBtn.closest('tr');
        tr.cells[1].textContent = nama.charAt(0).toUpperCase() + nama.slice(1);
        tr.cells[2].innerHTML   = status === 'aktif'
            ? `<span class="badge-aktif px-3 py-2">Aktif</span>`
            : `<span class="badge-nonaktif px-3 py-2">Tidak Aktif</span>`;
        ubahBtn.dataset.nama   = nama;
        ubahBtn.dataset.status = status;
        const hapusBtn = tr.querySelector('.btn-hapus-bidang');
        if (hapusBtn) hapusBtn.dataset.nama = nama;
    }

    // ────────────────────────────────────────────
    // HELPER: Tambah baris baru ke tbody sub event
    // ────────────────────────────────────────────
    function appendRow(bidang, subEventId) {
        const tbody = document.getElementById(`tbody-se-${subEventId}`);
        if (!tbody) return;

        const emptyRow = tbody.querySelector('.empty-row-wrapper');
        if (emptyRow) emptyRow.remove();

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
        // Tidak perlu re-attach listener — pakai event delegation di bawah
    }

    // ────────────────────────────────────────────
    // HELPER: Loading state tombol Simpan
    // ────────────────────────────────────────────
    function setSimpanLoading(loading) {
        document.getElementById('btnSimpanBidang').disabled     = loading;
        document.getElementById('btnSimpanBidang').textContent  = loading ? 'Menyimpan...' : 'Simpan';
        document.getElementById('btnBatalBidang').disabled      = loading;
        document.getElementById('btnTutupModalBidang').disabled = loading;
    }

    // ────────────────────────────────────────────
    // HELPER: Loading state tombol Hapus
    // ────────────────────────────────────────────
    function setHapusLoading(loading) {
        document.getElementById('btnHapusBidang').disabled      = loading;
        document.getElementById('btnHapusBidang').textContent   = loading ? 'Menghapus...' : 'Hapus';
        document.getElementById('btnBatalHapusBidang').disabled = loading;
    }

    // ────────────────────────────────────────────
    // EVENT DELEGATION: Tambah, Ubah, Hapus
    // Satu listener di document body — menangkap semua tombol
    // termasuk baris yang baru di-append
    // ────────────────────────────────────────────
    document.body.addEventListener('click', function (e) {

        // ── Tambah Bidang ──
        const tambahBtn = e.target.closest('.btn-tambah-bidang');
        if (tambahBtn) {
            activeMode         = 'store';
            activeUpdateId     = null;
            activeUpdateUrl    = null;
            activeSubEventId   = tambahBtn.dataset.subEventId;
            activeSubEventNama = tambahBtn.dataset.subEventNama;
            document.getElementById('modalBidangTitle').textContent   = 'Tambah Bidang';
            document.getElementById('bidangSubEventNama').textContent = activeSubEventNama;
            document.getElementById('bidangNama').value               = '';
            document.getElementById('statusAktifBidang').checked      = true;
            setSimpanLoading(false);
            modalBidang.show();
            return;
        }

        // ── Ubah Bidang ──
        const ubahBtn = e.target.closest('.btn-ubah-bidang');
        if (ubahBtn) {
            activeMode         = 'update';
            activeUpdateId     = ubahBtn.dataset.id;
            activeUpdateUrl    = ubahBtn.dataset.url;
            activeSubEventId   = ubahBtn.dataset.subEventId;
            activeSubEventNama = ubahBtn.dataset.subEventNama;
            document.getElementById('modalBidangTitle').textContent   = 'Ubah Bidang';
            document.getElementById('bidangSubEventNama').textContent = activeSubEventNama;
            document.getElementById('bidangNama').value               = ubahBtn.dataset.nama;
            const radioId = ubahBtn.dataset.status === 'tidak_aktif'
                ? 'statusNonaktifBidang' : 'statusAktifBidang';
            document.getElementById(radioId).checked = true;
            setSimpanLoading(false);
            modalBidang.show();
            return;
        }

        // ── Hapus Bidang ──
        const hapusBtn = e.target.closest('.btn-hapus-bidang');
        if (hapusBtn) {
            activeHapusId   = hapusBtn.dataset.id;
            activeHapusUrl  = hapusBtn.dataset.url;
            activeHapusNama = hapusBtn.dataset.nama;
            document.getElementById('namaBidangHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapus.show();
        }
    });

    // ────────────────────────────────────────────
    // MODAL: tutup manual (guard saat loading)
    // ────────────────────────────────────────────
    document.getElementById('btnTutupModalBidang').addEventListener('click', function () {
        if (isSaving) return;
        modalBidang.hide();
    });
    document.getElementById('btnBatalBidang').addEventListener('click', function () {
        if (isSaving) return;
        modalBidang.hide();
    });
    document.getElementById('btnBatalHapusBidang').addEventListener('click', function () {
        if (isDeleting) return;
        modalHapus.hide();
    });

    // ────────────────────────────────────────────
    // SUBMIT: Tambah / Ubah
    // ────────────────────────────────────────────
    document.getElementById('btnSimpanBidang').addEventListener('click', async function () {
        if (isSaving) return;

        const nama   = document.getElementById('bidangNama').value.trim();
        const status = document.querySelector('input[name="statusBidang"]:checked').value;

        if (!nama) {
            document.getElementById('bidangNama').focus();
            return;
        }

        isSaving = true;
        setSimpanLoading(true);

        try {
            const isUpdate = activeMode === 'update';
            const url      = isUpdate ? activeUpdateUrl : STORE_URL;
            const res      = await sendRequest(url, {
                _method:      isUpdate ? 'PUT' : 'POST',
                nama,
                status,
                sub_event_id: activeSubEventId,
            });

            if (res.success) {
                modalBidang.hide();
                toast(isUpdate ? 'Bidang berhasil diubah!' : 'Bidang berhasil ditambahkan!');
                if (isUpdate) {
                    updateRow(activeUpdateId, nama, status);
                } else {
                    appendRow(res.bidang, activeSubEventId);
                }
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
            }
        } catch (e) {
            console.error(e);
            toast(e.message ?? 'Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isSaving = false;
            setSimpanLoading(false);
        }
    });

    // ────────────────────────────────────────────
    // SUBMIT: Hapus
    // ────────────────────────────────────────────
    document.getElementById('btnHapusBidang').addEventListener('click', async function () {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapus.hide();
                toast(`Bidang "${activeHapusNama}" berhasil dihapus!`);
                const hapusBtn = document.querySelector(`.btn-hapus-bidang[data-id="${activeHapusId}"]`);
                if (hapusBtn) hapusBtn.closest('tr').remove();
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch (e) {
            console.error(e);
            toast(e.message ?? 'Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isDeleting = false;
            setHapusLoading(false);
        }
    });

});
</script>
@endpush