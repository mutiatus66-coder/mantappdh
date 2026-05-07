@extends('index', ['dummy' => true])

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<div id="kt_content" class="content d-flex flex-column flex-column-fluid">
  <div class="p-6">

    <!-- TOP ACTION -->
    <button onclick="bukaModal()"
      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm flex items-center gap-2 transition duration-200">
      <span class="text-lg">+</span>
      Tambah Event
    </button>

    <!-- CARD TABLE -->
    <div class="items-center mb-4 mt-4 bg-white rounded-lg shadow border border-gray-200">
      <div class="p-4 overflow-x-auto">
        <div class="items-center mb-6 mt-4">
          <h2 class="text-lg font-semibold text-blue-600">DATA EVENT</h2>
        </div>
        <table class="w-full border border-gray-300">
          <thead class="bg-gray-200 text-gray-600">
            <tr>
              <th class="px-4 py-3 w-16 text-center border-r border-gray-300">No</th>
              <th class="px-4 py-3 text-center border-r border-gray-300">Nama</th>
              <th class="px-4 py-3 text-center border-r border-gray-300">Email</th>
              <th class="px-4 py-3 text-center border-r border-gray-300">Hak Akses</th>
              <th class="px-4 py-3 text-center border-r border-gray-300">Status</th>
              <th class="px-4 py-3 w-40 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-gray-700">
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 border-r border-gray-300 text-center">1</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">akwardblublu</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">akwardblublu@example.com</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">admin</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">aktif</td>
              <td class="px-4 py-3 text-center">
                <div style="display:flex; flex-direction:row; align-items:center; justify-content:center; gap:6px;">
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1">✏️ Edit</button>
                  <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center gap-1">🗑️ Hapus</button>
                  <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1">🔑 Login As</button>
                </div>
              </td>
            </tr>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 border-r border-gray-300 text-center">2</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">syududu</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">syududu@example.com</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">user</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">aktif</td>
              <td class="px-4 py-3 text-center">
                <div style="display:flex; flex-direction:row; align-items:center; justify-content:center; gap:6px;">
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1">✏️ Edit</button>
                  <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center gap-1">🗑️ Hapus</button>
                  <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1">🔑 Login As</button>
                </div>
              </td>
            </tr>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 border-r border-gray-300 text-center">3</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">cihuyyy</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">cihuyyy@example.com</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">user</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">aktif</td>
              <td class="px-4 py-3 text-center">
                <div style="display:flex; flex-direction:row; align-items:center; justify-content:center; gap:6px;">
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1">✏️ Edit</button>
                  <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center gap-1">🗑️ Hapus</button>
                  <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1">🔑 Login As</button>
                </div>
              </td>
            </tr>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-3 border-r border-gray-300 text-center">4</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">wutwut</td>
              <td class="px-4 py-3 border-r border-gray-300 text-left">wutwut@example.com</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">user</td>
              <td class="px-4 py-3 border-r border-gray-300 text-center">aktif</td>
              <td class="px-4 py-3 text-center">
                <div style="display:flex; flex-direction:row; align-items:center; justify-content:center; gap:6px;">
                  <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1">✏️ Edit</button>
                  <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center gap-1">🗑️ Hapus</button>
                  <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1">🔑 Login As</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
  <div id="kt_content_container" class="container-fluid"></div>
</div>


<!-- ===== MODAL TAMBAH DATA ===== -->
<div id="modalTambahData"
  style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4);
         align-items:center; justify-content:center; z-index:9999;">
  <div style="background:white; border-radius:12px; width:580px; max-width:95vw;
              box-shadow:0 8px 32px rgba(0,0,0,0.2);">

    <!-- Header -->
    <div style="padding:20px 28px 16px; border-bottom:1px solid #e5e7eb;">
      <h2 style="font-size:1rem; font-weight:600; color:#6b7280;">Tambah Data</h2>
    </div>

    <!-- Body -->
    <form action="{{ route('user.store') }}" method="POST">
      @csrf
      <div style="padding:24px 28px; display:flex; flex-direction:column; gap:18px;">

        <!-- Nama -->
        <div style="display:flex; align-items:center; gap:16px;">
          <label style="font-size:0.875rem; font-weight:500; color:#374151; min-width:90px;">Nama</label>
          <input type="text" name="nama" required placeholder="Masukkan nama..."
            style="flex:1; border:1.5px solid #d1d5db; border-radius:6px; padding:8px 12px; font-size:0.875rem; outline:none;"
            onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
        </div>

        <!-- Email -->
        <div style="display:flex; align-items:center; gap:16px;">
          <label style="font-size:0.875rem; font-weight:500; color:#374151; min-width:90px;">Email</label>
          <input type="email" name="email" required placeholder="Masukkan email..."
            style="flex:1; border:1.5px solid #d1d5db; border-radius:6px; padding:8px 12px; font-size:0.875rem; outline:none;"
            onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
        </div>

        <!-- Hak Akses -->
        <div style="display:flex; align-items:center; gap:16px;">
          <label style="font-size:0.875rem; font-weight:500; color:#374151; min-width:90px;">Hak Akses</label>
          <select name="hak_akses" required
            style="flex:1; border:1.5px solid #d1d5db; border-radius:6px; padding:8px 12px; font-size:0.875rem; outline:none; background:white;"
            onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
            <option value="" disabled selected>-- Pilih Hak Akses --</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
        </div>

        <!-- Status -->
        <div style="display:flex; align-items:center; gap:16px;">
          <label style="font-size:0.875rem; font-weight:500; color:#374151; min-width:90px;">Status</label>
          <div style="display:flex; gap:24px;">
            <label style="display:flex; align-items:center; gap:7px; font-size:0.875rem; cursor:pointer;">
              <input type="radio" name="status" value="aktif" checked> Aktif
            </label>
            <label style="display:flex; align-items:center; gap:7px; font-size:0.875rem; cursor:pointer;">
              <input type="radio" name="status" value="nonaktif"> Nonaktif
            </label>
          </div>
        </div>

        <!-- Password -->
        <div style="display:flex; align-items:center; gap:16px;">
          <label style="font-size:0.875rem; font-weight:500; color:#374151; min-width:90px;">Password</label>
          <input type="password" name="password" required placeholder="Masukkan password..."
            style="flex:1; border:1.5px solid #d1d5db; border-radius:6px; padding:8px 12px; font-size:0.875rem; outline:none;"
            onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
        </div>

      </div>

      <!-- Footer -->
      <div style="padding:16px 28px 22px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e5e7eb;">
        <button type="submit"
          style="background:#3b82f6; color:white; padding:9px 24px; border-radius:7px;
                 font-size:0.875rem; font-weight:600; border:none; cursor:pointer;">
          Simpan
        </button>
        <button type="button" onclick="tutupModal()"
          style="background:#9ca3af; color:white; padding:9px 24px; border-radius:7px;
                 font-size:0.875rem; font-weight:600; border:none; cursor:pointer;">
          Batal
        </button>
      </div>
    </form>

  </div>
</div>

<script>
  function bukaModal() {
    var m = document.getElementById('modalTambahData');
    m.style.display = 'flex';
  }
  function tutupModal() {
    var m = document.getElementById('modalTambahData');
    m.style.display = 'none';
  }
  // Klik di luar modal untuk menutup
  document.getElementById('modalTambahData').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
  });
</script>

@endsection