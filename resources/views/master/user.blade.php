@extends('index', ['dummy' => true])

@push('styles')
<link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="page-container">

    <div class="sub-event-header">
        <div class="sub-event-title">
            <h3>Data User</h3>
            <p>Kelola semua user yang terdaftar</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser">
            Tambah User
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-dismissible fade show mb-4" role="alert"
         style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#92400e;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="total-badge">
            Total User: <span id="totalUser">{{ $users->count() }}</span>
        </div>
        <div class="search-box">
            <input type="text" id="searchUser" class="form-control" placeholder="Cari nama atau email...">
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="se-table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="text-align:center;">Hak Akses</th>
                    <th style="text-align:center;">Status</th>
                    <th width="320" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users ?? [] as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->email }}</td>
                    <td style="text-align:center;">{{ $item->hak_akses }}</td>
                    <td style="text-align:center;">{{ $item->status }}</td>
                    <td style="text-align:center;">
                        <div class="btn-aksi-wrap">
                            <button class="btn btn-warning btn-aksi btn-edit-user"
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama }}"
                                    data-email="{{ $item->email }}"
                                    data-hak-akses="{{ $item->hak_akses }}"
                                    data-status="{{ $item->status }}">
                                Ubah
                            </button>
                            <button type="button" class="btn btn-danger btn-aksi btn-hapus-user"
                                    data-url="{{ route('user.destroy', $item->id) }}"
                                    data-nama="{{ $item->nama }}">
                                Hapus
                            </button>
                            <a href="{{ route('user.login-as', $item->id) }}"
                               class="btn btn-success btn-aksi">
                                Login As
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-row">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Belum ada data user
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- ══ MODAL — Tambah / Edit User ══ --}}
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg">
            <form id="formUser" method="POST" action="{{ route('user.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formUserMethod" value="POST">

                <div class="modal-header px-5 py-4">
                    <h5 class="modal-title fw-semibold" id="modalUserTitle">Tambah User</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>
                    <div class="modal-body px-5 py-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Nama</label>
                            <input type="text" name="nama" id="inputNama"
                                   class="form-control" placeholder="Masukkan nama..." required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Email</label>
                            <input type="email" name="email" id="inputEmail"
                                   class="form-control" placeholder="Masukkan email..." required>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold required">Hak Akses</label>
                            <select name="hak_akses" id="inputHakAkses" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                <option value="admin">Admin_bapperida</option>
                                <option value="user">User</option>
                                <option value="penilai">Penilai</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="d-flex gap-4 mt-1">
                                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                    <input type="radio" name="status" id="statusAktif" value="aktif" checked> Aktif
                                </label>
                                <label class="d-flex align-items-center gap-2" style="font-size:.875rem; cursor:pointer;">
                                    <input type="radio" name="status" id="statusNonaktif" value="nonaktif"> Nonaktif
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2" id="wrapPassword">
                            <label class="form-label fw-semibold required" id="labelPassword">Password</label>
                            <input type="password" name="password" id="inputPassword"
                                   class="form-control" placeholder="Masukkan password..." required>
                            <div id="passwordHint" style="display:none; font-size:0.78rem; color:var(--ri-text-muted); margin-top:4px;">
                                Kosongkan jika tidak ingin mengubah password
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-5 py-3">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ MODAL — Konfirmasi Hapus User ══ --}}
<div class="modal fade" id="modalHapusUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg text-center px-4 py-4">

            <div class="d-flex justify-content-center mb-3">
                <div class="hapus-icon-circle">
                    <i class="bi bi-trash3" style="font-size:1.6rem; color:var(--ri-btn-danger);"></i>
                </div>
            </div>

            <h5 class="fw-semibold mb-1" style="color:var(--ri-text-primary);">Hapus User Ini?</h5>
            <p class="mb-4 hapus-teks-muted" style="font-size:.875rem; line-height:1.6; color:#6b7280;">
                Tindakan ini tidak dapat dibatalkan. User
                <strong id="namaUserHapus" class="hapus-nama-strong"></strong>
                akan dihapus secara permanen.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-dark btn-aksi px-3" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusUser" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-aksi px-3">
                        Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const storeUrl = "{{ route('user.store') }}";
    const searchInput = document.getElementById('searchUser');
    const totalUserBadge = document.getElementById('totalUser');
    const userRows = Array.from(document.querySelectorAll('.se-table tbody tr'));

    const updateUserCount = () => {
        const visibleRows = userRows.filter(row => row.style.display !== 'none');
        totalUserBadge.textContent = visibleRows.length;
    };

    const filterUsers = () => {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        userRows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const emailCell = row.querySelector('td:nth-child(3)');
            if (!nameCell || !emailCell) {
                return;
            }

            const nameText = nameCell.textContent.trim().toLowerCase();
            const emailText = emailCell.textContent.trim().toLowerCase();
            const matches = nameText.includes(query) || emailText.includes(query);

            row.style.display = matches ? '' : 'none';
            if (matches) {
                visibleCount += 1;
            }
        });

        totalUserBadge.textContent = visibleCount;
    };

    if (searchInput) {
        searchInput.addEventListener('input', filterUsers);
    }

    // Reset modal on close
    document.getElementById('modalUser').addEventListener('hidden.bs.modal', function () {
        document.getElementById('formUser').action          = storeUrl;
        document.getElementById('formUserMethod').value     = 'POST';
        document.getElementById('modalUserTitle').textContent = 'Tambah User';
        document.getElementById('inputNama').value          = '';
        document.getElementById('inputEmail').value         = '';
        document.getElementById('inputHakAkses').value      = '';
        document.getElementById('statusAktif').checked      = true;
        document.getElementById('inputPassword').value      = '';
        document.getElementById('inputPassword').required   = true;
        document.getElementById('passwordHint').style.display = 'none';
        document.getElementById('labelPassword').textContent  = 'Password';
        if (searchInput) {
            searchInput.value = '';
            filterUsers();
        }
    });

    // Edit
    document.querySelectorAll('.btn-edit-user').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('modalUserTitle').textContent   = 'Ubah User';
            document.getElementById('formUser').action              = `/user/${this.dataset.id}`;
            document.getElementById('formUserMethod').value         = 'PUT';
            document.getElementById('inputNama').value              = this.dataset.nama;
            document.getElementById('inputEmail').value             = this.dataset.email;
            document.getElementById('inputHakAkses').value          = this.dataset.hakAkses;
            document.getElementById('inputPassword').required       = false;
            document.getElementById('inputPassword').value          = '';
            document.getElementById('passwordHint').style.display   = 'block';
            document.getElementById('labelPassword').textContent     = 'Password (opsional)';

            if (this.dataset.status === 'nonaktif') {
                document.getElementById('statusNonaktif').checked = true;
            } else {
                document.getElementById('statusAktif').checked = true;
            }

            new bootstrap.Modal(document.getElementById('modalUser')).show();
        });
    });

    // Hapus
    document.querySelectorAll('.btn-hapus-user').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('namaUserHapus').textContent = this.dataset.nama;
            document.getElementById('formHapusUser').action      = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalHapusUser')).show();
        });
    });

});
</script>
@endpush