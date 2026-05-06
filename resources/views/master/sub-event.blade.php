@extends('index', ['dummy' => true])

@section('content')

<style>
.sub-card{
    border:none;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 2px 10px rgba(0,0,0,.05);
}
.sub-card .card-header{
    background:#fff;
    border-bottom:1px solid #eff2f5;
}
.modal-backdrop.show{
    opacity:.3;
}
</style>

<div class="row">
    <div class="col-12">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card sub-card">
            <div class="card-header d-flex justify-content-between align-items-center py-5">
                <h3 class="card-title fw-bold text-primary m-0">
                    Data Sub Event
                </h3>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSubEvent">
                    Tambah Sub Event
                </button>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-row-bordered align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th width="50">No</th>
                                <th>Tahun</th>
                                <th>Event</th>
                                <th>Sub Event</th>
                                <th>Kategori</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Berakhir</th>
                                <th width="180" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($subEvents as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['tahun'] }}</td>
                                    <td>{{ $item['event'] }}</td>
                                    <td>{{ $item['sub_event'] }}</td>
                                    <td>
                                        <span class="badge badge-light-primary">
                                            {{ $item['kategori'] ?: '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $item['mulai'] }}</td>
                                    <td>{{ $item['berakhir'] }}</td>
                                    <td class="text-center">

                                        <button
                                            class="btn btn-sm btn-light-primary btn-edit"
                                            data-id="{{ $item['id'] }}">
                                            Edit
                                        </button>

                                        <form
                                            action="{{ route('admin.sub-event.destroy',$item['id']) }}"
                                            method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                onclick="return confirm('Hapus data ini?')"
                                                class="btn btn-sm btn-light-danger">
                                                Hapus
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-10">
                                        Belum ada data
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalSubEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <form id="formSubEvent" method="POST" action="{{ route('admin.sub-event.store') }}">
                @csrf

                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h3 class="modal-title" id="modalTitle">
                        Tambah Sub Event
                    </h3>

                    <div class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                        ✕
                    </div>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-5">
                            <label class="form-label required">Tahun</label>
                            <input
                                type="number"
                                name="tahun"
                                id="tahun"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="form-label required">Event</label>

                            <select
                                name="event"
                                id="event"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Event --</option>

                                @foreach($events as $event)
                                    <option value="{{ $event }}">
                                        {{ $event }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-12 mb-5">
                            <label class="form-label required">Sub Event</label>

                            <input
                                type="text"
                                name="sub_event"
                                id="sub_event"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-12 mb-5">
                            <label class="form-label">Kategori</label>

                            <input
                                type="text"
                                name="kategori"
                                id="kategori"
                                class="form-control">
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="form-label required">Tanggal Mulai</label>

                            <input
                                type="date"
                                name="mulai"
                                id="mulai"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-6 mb-5">
                            <label class="form-label required">Tanggal Berakhir</label>

                            <input
                                type="date"
                                name="berakhir"
                                id="berakhir"
                                class="form-control"
                                required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = new bootstrap.Modal(document.getElementById('modalSubEvent'));

    document.querySelectorAll('.btn-edit').forEach(btn => {

        btn.addEventListener('click', async function () {

            const id = this.dataset.id;

            const response = await fetch(`/sub-event/${id}/edit`)
            const data = await response.json();

            document.getElementById('modalTitle').innerText = 'Edit Sub Event';

            document.getElementById('tahun').value = data.tahun;
            document.getElementById('event').value = data.event;
            document.getElementById('sub_event').value = data.sub_event;
            document.getElementById('kategori').value = data.kategori;
            document.getElementById('mulai').value = data.mulai;
            document.getElementById('berakhir').value = data.berakhir;

            document.getElementById('formMethod').value = 'PUT';

            document.getElementById('formSubEvent')
                .action = `/sub-event/${id}`;

            modal.show();

        });

    });

    document.querySelector('[data-bs-target="#modalSubEvent"]')
        .addEventListener('click', function () {

            document.getElementById('modalTitle').innerText = 'Tambah Sub Event';

            document.getElementById('formSubEvent')
                .action = `{{ route('admin.sub-event.store') }}`;

            document.getElementById('formMethod').value = 'POST';

            document.getElementById('formSubEvent').reset();

        });

});
</script>

@endsection
