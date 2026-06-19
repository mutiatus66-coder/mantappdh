@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data Event</h3>
            <p>Kelola semua event yang tersedia</p>
        </div>
        <button class="btn btn-primary" id="btnTambahEvent">
            Tambah Event
        </button>
    </div>

    <div class="sub-event-stats">
        <div class="total-badge">
            Total Event: <span id="totalEvent">{{ $events->count() }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchEvent" placeholder="Cari event...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama Event</th>
                    <th style="text-align:center;">Jenis</th>
                    <th width="220" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelEventBody">
                @forelse($events as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_event }}</td>
                    <td style="text-align:center;"><span class="badge-kategori">{{ $item->jenis ?? '-' }}</span></td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-edit-event btn-aksi"
                                    data-id="{{ $item->id }}"
                                    data-nama-event="{{ $item->nama_event }}"
                                    data-jenis="{{ $item->jenis }}"
                                    data-url="{{ route('event.update', $item->id) }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-hapus-event btn-aksi"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_event }}"
                                    data-url="{{ route('event.destroy', $item->id) }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="emptyRow">
                    <td colspan="4" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data event
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Ubah Event ══ --}}
<div class="modal fade" id="modalEvent" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalEventTitle">Tambah Event</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        id="btnTutupModalEvent" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <div class="modal-body px-5 py-4">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Nama Event</label>
                        <input type="text" id="inputNamaEvent"
                               class="form-control" placeholder="Masukkan nama event...">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Jenis</label>
                        <select id="inputJenis" class="form-select">
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="INOTEK">INOTEK</option>
                            <option value="INODA">INODA</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" id="btnBatalEvent">Batal</button>
                <button type="button" id="btnSimpanEvent" class="btn btn-success px-4">Simpan</button>
            </div>
        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Event ══ --}}
<div class="modal fade" id="modalHapusEvent" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Data event
                <strong id="namaEventHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-aksi px-3" id="btnBatalHapus">Batal</button>
                <button type="button" id="btnHapusEvent" class="btn btn-danger btn-aksi px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {

    // ── Konstanta ──
    const STORE_URL = "{{ route('event.store') }}";
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Elemen ──
    const tbody       = document.getElementById('tabelEventBody');
    const totalSpan   = document.getElementById('totalEvent');
    const searchInput = document.getElementById('searchEvent');

    // ── Modal ──
    const modalEvent = new bootstrap.Modal(document.getElementById('modalEvent'));
    const modalHapus = new bootstrap.Modal(document.getElementById('modalHapusEvent'));

    // ── State ──
    let activeMode      = 'store';
    let activeUpdateId  = null;
    let activeUpdateUrl = null;
    let activeHapusId   = null;
    let activeHapusUrl  = null;
    let activeHapusNama = null;
    let isSaving        = false;
    let isDeleting      = false;

    // ────────────────────────────────────────────
    // HELPER: AJAX
    // ────────────────────────────────────────────
    async function sendRequest(url, data) {
        const form = new FormData();
        Object.entries(data).forEach(([k, v]) => form.append(k, v));
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
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
    // HELPER: Update baris
    // ────────────────────────────────────────────
    function updateRow(id, namaEvent, jenis) {
        const editBtn = tbody.querySelector(`.btn-edit-event[data-id="${id}"]`);
        if (!editBtn) return;
        const tr = editBtn.closest('tr');
        tr.cells[1].textContent = namaEvent;
        tr.cells[2].innerHTML   = `<span class="badge-kategori">${jenis}</span>`;
        editBtn.dataset.namaEvent = namaEvent;
        editBtn.dataset.jenis     = jenis;
        const hapusBtn = tr.querySelector('.btn-hapus-event');
        if (hapusBtn) hapusBtn.dataset.nama = namaEvent;
    }

    // ────────────────────────────────────────────
    // HELPER: Tambah baris baru
    // ────────────────────────────────────────────
    function appendRow(event) {
        const emptyRow = tbody.querySelector('#emptyRow');
        if (emptyRow) emptyRow.remove();

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>${event.nama_event}</td>
            <td style="text-align:center;"><span class="badge-kategori">${event.jenis}</span></td>
            <td style="text-align:center;">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-edit-event btn-aksi"
                            data-id="${event.id}"
                            data-nama-event="${event.nama_event}"
                            data-jenis="${event.jenis}"
                            data-url="${event.update_url}">
                        Ubah
                    </button>
                    <button class="btn btn-danger btn-hapus-event btn-aksi"
                            data-id="${event.id}"
                            data-nama="${event.nama_event}"
                            data-url="${event.destroy_url}">
                        Hapus
                    </button>
                </div>
            </td>`;
        tbody.appendChild(tr);
        totalSpan.textContent = tbody.querySelectorAll('tr').length;
    }

    // ────────────────────────────────────────────
    // HELPER: Renumber
    // ────────────────────────────────────────────
    function renumberRows() {
        let n = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            if (!tr.querySelector('.empty-row')) tr.cells[0].textContent = ++n;
        });
        totalSpan.textContent = n;
    }

    // ────────────────────────────────────────────
    // HELPER: Loading state
    // ────────────────────────────────────────────
    function setSimpanLoading(loading) {
        document.getElementById('btnSimpanEvent').disabled      = loading;
        document.getElementById('btnSimpanEvent').textContent   = loading ? 'Menyimpan...' : 'Simpan';
        document.getElementById('btnBatalEvent').disabled       = loading;
        document.getElementById('btnTutupModalEvent').disabled  = loading;
    }

    function setHapusLoading(loading) {
        document.getElementById('btnHapusEvent').disabled   = loading;
        document.getElementById('btnHapusEvent').textContent = loading ? 'Menghapus...' : 'Hapus';
        document.getElementById('btnBatalHapus').disabled   = loading;
    }

    // ────────────────────────────────────────────
    // MODAL: buka Tambah
    // ────────────────────────────────────────────
    document.getElementById('btnTambahEvent').addEventListener('click', function () {
        activeMode      = 'store';
        activeUpdateId  = null;
        activeUpdateUrl = null;
        document.getElementById('modalEventTitle').textContent = 'Tambah Event';
        document.getElementById('inputNamaEvent').value        = '';
        document.getElementById('inputJenis').value            = '';
        setSimpanLoading(false);
        modalEvent.show();
    });

    // ────────────────────────────────────────────
    // MODAL: buka Ubah / Hapus via event delegation
    // ────────────────────────────────────────────
    tbody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-event');
        if (editBtn) {
            activeMode      = 'update';
            activeUpdateId  = editBtn.dataset.id;
            activeUpdateUrl = editBtn.dataset.url;
            document.getElementById('modalEventTitle').textContent = 'Ubah Event';
            document.getElementById('inputNamaEvent').value        = editBtn.dataset.namaEvent;
            document.getElementById('inputJenis').value            = editBtn.dataset.jenis;
            setSimpanLoading(false);
            modalEvent.show();
            return;
        }

        const hapusBtn = e.target.closest('.btn-hapus-event');
        if (hapusBtn) {
            activeHapusId   = hapusBtn.dataset.id;
            activeHapusUrl  = hapusBtn.dataset.url;
            activeHapusNama = hapusBtn.dataset.nama;
            document.getElementById('namaEventHapus').textContent = activeHapusNama;
            setHapusLoading(false);
            modalHapus.show();
        }
    });

    // ────────────────────────────────────────────
    // MODAL: tutup manual
    // ────────────────────────────────────────────
    document.getElementById('btnTutupModalEvent').addEventListener('click', function () {
        if (isSaving) return;
        modalEvent.hide();
    });
    document.getElementById('btnBatalEvent').addEventListener('click', function () {
        if (isSaving) return;
        modalEvent.hide();
    });
    document.getElementById('btnBatalHapus').addEventListener('click', function () {
        if (isDeleting) return;
        modalHapus.hide();
    });

    // ────────────────────────────────────────────
    // SUBMIT: Tambah / Ubah
    // ────────────────────────────────────────────
    document.getElementById('btnSimpanEvent').addEventListener('click', async function () {
        if (isSaving) return;

        const namaEvent = document.getElementById('inputNamaEvent').value.trim();
        const jenis     = document.getElementById('inputJenis').value;

        if (!namaEvent) { document.getElementById('inputNamaEvent').focus(); return; }
        if (!jenis)     { document.getElementById('inputJenis').focus(); return; }

        isSaving = true;
        setSimpanLoading(true);

        try {
            const isUpdate = activeMode === 'update';
            const url      = isUpdate ? activeUpdateUrl : STORE_URL;
            const res      = await sendRequest(url, {
                nama_event: namaEvent,
                jenis,
                _method: isUpdate ? 'PUT' : 'POST',
            });

            if (res.success) {
                modalEvent.hide();
                toast(isUpdate ? 'Event berhasil diubah!' : 'Event berhasil ditambahkan!');
                isUpdate ? updateRow(activeUpdateId, namaEvent, jenis) : appendRow(res.event);
            } else {
                toast(res.message ?? 'Gagal menyimpan data.', 'error');
            }
        } catch (e) {
            console.error(e);
            toast('Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isSaving = false;
            setSimpanLoading(false);
        }
    });

    // ────────────────────────────────────────────
    // SUBMIT: Hapus
    // ────────────────────────────────────────────
    document.getElementById('btnHapusEvent').addEventListener('click', async function () {
        if (isDeleting) return;

        isDeleting = true;
        setHapusLoading(true);

        try {
            const res = await sendRequest(activeHapusUrl, { _method: 'DELETE' });
            if (res.success) {
                modalHapus.hide();
                toast(`Event "${activeHapusNama}" berhasil dihapus!`);
                const hapusBtn = tbody.querySelector(`.btn-hapus-event[data-id="${activeHapusId}"]`);
                if (hapusBtn) hapusBtn.closest('tr').remove();
                renumberRows();
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch (e) {
            console.error(e);
            toast('Terjadi kesalahan, coba lagi.', 'error');
        } finally {
            isDeleting = false;
            setHapusLoading(false);
        }
    });

    // ────────────────────────────────────────────
    // SEARCH
    // ────────────────────────────────────────────
    searchInput.addEventListener('input', function () {
        const kw = this.value.toLowerCase().trim();
        let n = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            if (tr.querySelector('.empty-row')) return;
            const show = tr.textContent.toLowerCase().includes(kw);
            tr.style.display = show ? '' : 'none';
            if (show) n++;
        });
        totalSpan.textContent = n;
    });

})();
</script>
@endpush