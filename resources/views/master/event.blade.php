@extends('index', ['dummy' => true])

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div id="kt_content" class="content d-flex flex-column flex-column-fluid">
  <div class="p-6">

    <!-- TOP ACTION -->
    <button onclick="bukaModal()"
  class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm flex items-center gap-2 transition duration-200">
  <span class="text-lg">+</span> Tambah Event
</button>

    <!-- CARD TABLE -->
    <div class="items-center mb-4 mt-4 bg-white rounded-lg shadow border border-gray-200">
      <div class="p-4 overflow-x-auto">
        <div class="items-center mb-6 mt-4">
          <h2 class="text-lg font-semibold text-blue-600">DATA EVENT</h2>
        </div>
        <table class="w-full border border-gray-300 border-collapse">
          <thead class="bg-gray-200 text-gray-600">
            <tr>
              <th class="px-4 py-3 w-16 text-center border border-gray-300">No</th>
              <th class="px-4 py-3 text-left border border-gray-300">Event</th>
              <th class="px-4 py-3 w-40 text-center border border-gray-300">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-gray-700">
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 text-center border border-gray-300">1</td>
              <td class="px-4 py-3 border border-gray-300">INOVASI DAERAH KAB. MAGETAN</td>
              <td class="px-4 py-3 text-center border border-gray-300">
                <a href="#">                  
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center justify-center gap-1 mx-auto">
                    ✏️ Edit
                  </button>
                </a>
              </td>
            </tr>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 text-center border border-gray-300">2</td>
              <td class="px-4 py-3 border border-gray-300">LOMBA INOVASI DAN TEKNOLOGI (INOTEK AWARD)</td>
              <td class="px-4 py-3 text-center border border-gray-300">
                <a href="#"">                  
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center justify-center gap-1 mx-auto">
                    ✏️ Edit
                  </button>
                </a>
              </td>
            </tr>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 text-center border border-gray-300">3</td>
              <td class="px-4 py-3 border border-gray-300">PAMERAN</td>
              <td class="px-4 py-3 text-center border border-gray-300">
                <a href="#">
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center justify-center gap-1 mx-auto">
                    ✏️ Edit
                  </button>
                </a>
              </td>
            </tr>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 text-center border border-gray-300">4</td>
              <td class="px-4 py-3 border border-gray-300">INOTEK AWARD</td>
              <td class="px-4 py-3 text-center border border-gray-300">
                <a href="#">                  
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center justify-center gap-1 mx-auto">
                    ✏️ Edit
                  </button>
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
  <div id="kt_content_container" class="container-fluid"></div>
</div>

<!-- ===== MODAL TAMBAH EVENT ===== -->
<div id="modalTambahEvent"
  style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); 
         align-items:center; justify-content:center; z-index:9999;">
  <div style="background:white; border-radius:12px; width:560px; max-width:95vw; box-shadow:0 8px 32px rgba(0,0,0,0.18);">

    <!-- Header -->
    <div style="padding:22px 28px 14px; border-bottom:1px solid #e5e7eb;">
      <h2 style="font-size:1rem; font-weight:600; color:#6b7280;">Tambah Event</h2>
    </div>

    <!-- Body -->
    <form action="{{ route('event.store') }}" method="POST">
      @csrf
      <div style="padding:24px 28px; display:flex; flex-direction:column; gap:20px;">

        <!-- Nama Event -->
        <div style="display:flex; align-items:center; gap:20px;">
          <label style="font-size:0.875rem; font-weight:500; color:#374151; min-width:100px;">Nama Event</label>
          <input type="text" name="nama_event" required placeholder="Masukkan nama event..."
            style="flex:1; border:1.5px solid #d1d5db; border-radius:6px; padding:9px 12px; font-size:0.875rem; outline:none;">
        </div>

        <!-- Jenis -->
        <div style="display:flex; align-items:center; gap:20px;">
          <label style="font-size:0.875rem; font-weight:500; color:#374151; min-width:100px;">Jenis</label>
          <div style="display:flex; gap:24px;">
            <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer;">
              <input type="radio" name="jenis" value="INOTEK" checked> INOTEK
            </label>
            <label style="display:flex; align-items:center; gap:8px; font-size:0.875rem; cursor:pointer;">
              <input type="radio" name="jenis" value="INODA"> INODA
            </label>
          </div>
        </div>

      </div>

      <!-- Footer -->
      <div style="padding:16px 28px 22px; display:flex; justify-content:flex-end; gap:10px;">
        <button type="submit"
          style="background:#3b82f6; color:white; padding:9px 24px; border-radius:7px; font-size:0.875rem; font-weight:600; border:none; cursor:pointer;">
          Simpan
        </button>
        <button type="button" onclick="tutupModal()"
          style="background:#9ca3af; color:white; padding:9px 24px; border-radius:7px; font-size:0.875rem; font-weight:600; border:none; cursor:pointer;">
          Batal
        </button>
      </div>
    </form>

  </div>
</div>

<script>
  function bukaModal() {
    var m = document.getElementById('modalTambahEvent');
    m.style.display = 'flex';
  }
  function tutupModal() {
    var m = document.getElementById('modalTambahEvent');
    m.style.display = 'none';
  }

  // Klik di luar modal untuk menutup
  document.getElementById('modalTambahEvent').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
  });
</script>

@endsection