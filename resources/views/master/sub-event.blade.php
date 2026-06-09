@extends('index')

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data Sub Event</h3>
            <p>Kelola semua sub event yang tersedia</p>
        </div>
        <button class="btn btn-primary" id="btnTambahSubEvent">
            Tambah Sub Event
        </button>
    </div>

    <div class="sub-event-stats">
        <div class="total-badge">
            Total Sub Event: <span id="totalSubEvent">{{ $subEvents->count() }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchSubEvent" placeholder="Cari sub event...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Tahun</th>
                    <th>Event</th>
                    <th>Sub Event</th>
                    <th>Kategori</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Berakhir</th>
                    <th width="220" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelSubEventBody">
                @forelse($subEvents as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->tahun }}</td>
                    <td>{{ $item->event->nama_event ?? '-' }}</td>
                    <td>{{ $item->sub_event }}</td>
                    <td><span class="badge-kategori">{{ $item->kategori ?? '-' }}</span></td>
                    <td>{{ $item->mulai }}</td>
                    <td>{{ $item->berakhir }}</td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-edit-se btn-aksi"
                                    data-id="{{ $item->id }}"
                                    data-tahun="{{ $item->tahun }}"
                                    data-event-id="{{ $item->event_id }}"
                                    data-sub-event="{{ $item->sub_event }}"
                                    data-kategori="{{ $item->kategori }}"
                                    data-mulai="{{ $item->mulai }}"
                                    data-berakhir="{{ $item->berakhir }}"
                                    data-url="{{ route('sub-event.update', $item->id) }}">
                                Ubah
                            </button>
                            <button class="btn btn-danger btn-hapus-se btn-aksi"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->sub_event }}"
                                    data-url="{{ route('sub-event.destroy', $item->id) }}">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data sub event
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Ubah Sub Event ══ --}}
<div class="modal fade" id="modalSubEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 shadow-lg">

            <div class="modal-header px-5 py-4">
                <h5 class="modal-title fw-semibold" id="modalSETitle">Tambah Sub Event</h5>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                        data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <div class="modal-body px-5 py-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold required">Tahun</label>
                        <input type="number" id="seTahun" class="form-control" placeholder="cth. 2025">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold required">Event</label>
                        <select id="seEvent" class="form-select">
                            <option value="">-- Pilih Event --</option>
                            @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold required">Sub Event</label>
                        <input type="text" id="seSubEvent" class="form-control" placeholder="Nama sub event">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Kategori</label>
                        <input type="text" id="seKategori" class="form-control" placeholder="Opsional">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold required">Tanggal Mulai</label>
                        <input type="date" id="seMulai" class="form-control">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold required">Tanggal Berakhir</label>
                        <input type="date" id="seBerakhir" class="form-control">
                    </div>
                </div>
            </div>

            <div class="modal-footer px-5 py-3">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSimpanSE" class="btn btn-success px-4">Simpan</button>
            </div>

        </div>
    </div>
</div>


{{-- ══ MODAL — Konfirmasi Hapus Sub Event ══ --}}
<div class="modal fade" id="modalHapusSE" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus Data Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. Sub event
                <strong id="namaSEHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-aksi px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnHapusSE" class="btn btn-danger btn-aksi px-3">Hapus</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl    = "{{ route('sub-event.store') }}";
    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const tbody       = document.getElementById('tabelSubEventBody');
    const totalSpan   = document.getElementById('totalSubEvent');
    const searchInput = document.getElementById('searchSubEvent');

    // ── Helper: AJAX ──
    async function sendRequest(url, method, data) {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
            body: JSON.stringify(data),
        });
        const modalSubEvent = new bootstrap.Modal(document.getElementById('modalSubEvent'));
        const modalHapusSE  = new bootstrap.Modal(document.getElementById('modalHapusSE'));
        return res.json();
    }

    // ── Helper: toast ──
    function toast(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = 'alert alert-dismissible fade show position-fixed bottom-0 end-0 m-4';
        el.style.cssText = `z-index:9999; min-width:280px;
            background:${type === 'success' ? 'rgba(245,158,11,0.12)' : 'rgba(163,45,45,0.12)'};
            border:1px solid ${type === 'success' ? 'rgba(245,158,11,0.4)' : 'rgba(163,45,45,0.3)'};
            color:${type === 'success' ? '#92400e' : '#A32D2D'};`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'x-circle-fill'} me-2"></i>${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    // ── Helper: update baris ──
    function updateRow(id, data, eventNama) {
        document.querySelectorAll('.btn-edit-se').forEach(btn => {
            if (btn.dataset.id == id) {
                const tr = btn.closest('tr');
                tr.cells[1].textContent = data.tahun;
                tr.cells[2].textContent = eventNama;
                tr.cells[3].textContent = data.sub_event;
                tr.cells[4].innerHTML   = `<span class="badge-kategori">${data.kategori || '-'}</span>`;
                tr.cells[5].textContent = data.mulai;
                tr.cells[6].textContent = data.berakhir;
                // Update dataset tombol
                btn.dataset.tahun     = data.tahun;
                btn.dataset.eventId   = data.event_id;
                btn.dataset.subEvent  = data.sub_event;
                btn.dataset.kategori  = data.kategori ?? '';
                btn.dataset.mulai     = data.mulai;
                btn.dataset.berakhir  = data.berakhir;
            }
        });
    }

    // ── Helper: tambah baris baru ──
    function appendRow(se) {
        const emptyRow = tbody.querySelector('.empty-row');
        if (emptyRow) emptyRow.closest('tr').remove();

        const rowCount = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>${se.tahun}</td>
            <td>${se.event_nama}</td>
            <td>${se.sub_event}</td>
            <td><span class="badge-kategori">${se.kategori || '-'}</span></td>
            <td>${se.mulai}</td>
            <td>${se.berakhir}</td>
            <td style="text-align:center;">
                <div class="btn-aksi-wrap">
                    <button class="btn btn-warning btn-edit-se btn-aksi"
                            data-id="${se.id}"
                            data-tahun="${se.tahun}"
                            data-event-id="${se.event_id}"
                            data-sub-event="${se.sub_event}"
                            data-kategori="${se.kategori ?? ''}"
                            data-mulai="${se.mulai}"
                            data-berakhir="${se.berakhir}"
                            data-url="${se.update_url}">
                        Ubah
                    </button>
                    <button class="btn btn-danger btn-hapus-se btn-aksi"
                            data-id="${se.id}"
                            data-nama="${se.sub_event}"
                            data-url="${se.destroy_url}">
                        Hapus
                    </button>
                </div>
            </td>`;
        tbody.appendChild(tr);

        tr.querySelector('.btn-edit-se').addEventListener('click', handleEdit);
        tr.querySelector('.btn-hapus-se').addEventListener('click', handleHapus);

        totalSpan.textContent = tbody.querySelectorAll('tr').length;
    }

    // ── Reset modal ──
    function resetModal() {
        document.getElementById('modalSETitle').textContent = 'Tambah Sub Event';
        ['seTahun','seSubEvent','seKategori','seMulai','seBerakhir'].forEach(id => {
            document.getElementById(id).value = '';
        });
        document.getElementById('seEvent').value = '';
        const btn = document.getElementById('btnSimpanSE');
        btn.disabled    = false;
        btn.textContent = 'Simpan';
        delete btn.dataset.updateId;
        delete btn.dataset.updateUrl;
        btn.dataset.mode = 'store';
    }

    document.getElementById('modalSubEvent').addEventListener('hidden.bs.modal', resetModal);

    // ── Tambah ──
    document.getElementById('btnTambahSubEvent').addEventListener('click', function () {
        resetModal();
        new bootstrap.Modal(document.getElementById('modalSubEvent')).show();
    });

    // ── Handler Edit ──
    function handleEdit() {
        resetModal();
        document.getElementById('modalSETitle').textContent = 'Ubah Sub Event';
        document.getElementById('seTahun').value            = this.dataset.tahun;
        document.getElementById('seEvent').value            = this.dataset.eventId;
        document.getElementById('seSubEvent').value         = this.dataset.subEvent;
        document.getElementById('seKategori').value         = this.dataset.kategori;
        document.getElementById('seMulai').value            = this.dataset.mulai;
        document.getElementById('seBerakhir').value         = this.dataset.berakhir;
        const btn = document.getElementById('btnSimpanSE');
        btn.dataset.mode      = 'update';
        btn.dataset.updateId  = this.dataset.id;
        btn.dataset.updateUrl = this.dataset.url;
        new bootstrap.Modal(document.getElementById('modalSubEvent')).show();
    }

    document.querySelectorAll('.btn-edit-se').forEach(btn => {
        btn.addEventListener('click', handleEdit);
    });

    // ── Submit AJAX (Tambah & Ubah) ──
    document.getElementById('btnSimpanSE').addEventListener('click', async function () {
        const tahun    = document.getElementById('seTahun').value.trim();
        const eventId  = document.getElementById('seEvent').value;
        const subEvent = document.getElementById('seSubEvent').value.trim();
        const kategori = document.getElementById('seKategori').value.trim();
        const mulai    = document.getElementById('seMulai').value;
        const berakhir = document.getElementById('seBerakhir').value;

        if (!tahun || !eventId || !subEvent || !mulai || !berakhir) {
            toast('Harap isi semua field yang wajib.', 'error');
            return;
        }

        this.disabled    = true;
        this.textContent = 'Menyimpan...';

        try {
            const isUpdate = this.dataset.mode === 'update';
            const url      = isUpdate ? this.dataset.updateUrl : storeUrl;
            const res      = await sendRequest(url, 'POST', {
                _method: isUpdate ? 'PUT' : 'POST',
                event_id: eventId, tahun, sub_event: subEvent,
                kategori, mulai, berakhir,
            });

            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalSubEvent')).hide();
                toast(isUpdate ? 'Sub Event berhasil diubah!' : 'Sub Event berhasil ditambahkan!');

                if (isUpdate) {
                    updateRow(this.dataset.updateId,
                        { tahun, event_id: eventId, sub_event: subEvent, kategori, mulai, berakhir },
                        res.event_nama);
                } else {
                    appendRow(res.subEvent);
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
        document.getElementById('namaSEHapus').textContent    = this.dataset.nama;
        document.getElementById('btnHapusSE').dataset.id      = this.dataset.id;
        document.getElementById('btnHapusSE').dataset.url     = this.dataset.url;
        document.getElementById('btnHapusSE').dataset.nama    = this.dataset.nama;
        new bootstrap.Modal(document.getElementById('modalHapusSE')).show();
    }

    document.querySelectorAll('.btn-hapus-se').forEach(btn => {
        btn.addEventListener('click', handleHapus);
    });

    // ── Submit Hapus AJAX ──
    document.getElementById('btnHapusSE').addEventListener('click', async function () {
        const url  = this.dataset.url;
        const id   = this.dataset.id;
        const nama = this.dataset.nama;

        this.disabled    = true;
        this.textContent = 'Menghapus...';

        try {
            const res = await sendRequest(url, 'POST', { _method: 'DELETE' });
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalHapusSE')).hide();
                toast(`Sub Event "${nama}" berhasil dihapus!`);

                document.querySelectorAll('.btn-hapus-se').forEach(btn => {
                    if (btn.dataset.id == id) btn.closest('tr').remove();
                });

                tbody.querySelectorAll('tr').forEach((tr, i) => {
                    if (!tr.querySelector('.empty-row')) tr.cells[0].textContent = i + 1;
                });
                totalSpan.textContent = tbody.querySelectorAll('tr:not(:has(.empty-row))').length;
            } else {
                toast(res.message ?? 'Gagal menghapus data.', 'error');
            }
        } catch {
            toast('Terjadi kesalahan.', 'error');
        }

        this.disabled    = false;
        this.textContent = 'Hapus';
    });

    // ── Search ──
    searchInput.addEventListener('keyup', function () {
        const kw = this.value.toLowerCase().trim();
        let n = 0;
        tbody.querySelectorAll('tr').forEach(r => {
            if (r.querySelector('.empty-row')) return;
            const show = r.textContent.toLowerCase().includes(kw);
            r.style.display = show ? '' : 'none';
            if (show) n++;
        });
        totalSpan.textContent = n;
    });

});
</script>
@endpush