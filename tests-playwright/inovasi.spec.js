import { test, expect, login, USERS, gotoFirstUsulanPage } from './fixtures';

/**
 * Test alur Usulan Inovasi (resources/views/inovasi/usulan.blade.php).
 * Form wizard 3 langkah (data diri -> narasi -> dokumen) + AJAX submit.
 *
 * PENTING soal reload: setelah "Simpan" & "Kirim" sukses, halaman melakukan
 * location.reload() penuh (500ms / 700ms kemudian) untuk menampilkan
 * perubahan. Kita pakai page.waitForEvent('load') (bukan waitForURL, karena
 * URL-nya SAMA PERSIS sebelum & sesudah reload sehingga waitForURL bisa
 * langsung resolve tanpa beneran nunggu).
 *
 * "Hapus" beda sendiri — dia .remove() elemen langsung tanpa reload.
 *
 * Tombol aksi (Kirim/Hapus/Edit) semuanya diawali ikon <i> sebelum teks,
 * yang bikin *accessible name* hasil hitungan browser suka beda-beda
 * antar Chromium/Firefox/WebKit (ada yang nganggep spasi ekstra dari ikon,
 * ada yang enggak) — jadi kita cari tombolnya lewat CLASS CSS
 * (.btn-kirim / .btn-hapus), persis kayak cara JS aplikasinya sendiri
 * ngenalin tombol ini (`e.target.closest('.btn-kirim')`), bukan lewat
 * teks/role yang ambigu.
 *
 * CATATAN: test ini butuh minimal 1 sub event & 1 bidang di database
 * (dari SubEventSeeder & BidangSeeder). Kalau seeder belum jalan atau
 * datanya kosong, test akan gagal dengan pesan yang jelas soal itu.
 */

const uniqueName = () => `Test Inovasi ${Date.now()}`;

// Buka modal "Tambah Usulan". Firefox kadang butuh trigger klik ekstra
// biar modal Bootstrap-nya beneran kebuka (timing animasi beda dari
// Chromium/WebKit) — kalau belum keliatan dalam 5 detik, coba klik sekali lagi.
async function bukaModalTambah(page) {
  await page.click('#btnTambah');
  try {
    await expect(page.locator('#modalUsulan')).toBeVisible({ timeout: 5000 });
  } catch {
    await page.click('#btnTambah');
    await expect(page.locator('#modalUsulan')).toBeVisible({ timeout: 10000 });
  }
}

// Klik "Selanjutnya" lalu pastikan panel tujuan BENERAN aktif sebelum
// lanjut ngisi field di panel itu — jangan asumsikan klik = pindah instan.
async function klikSelanjutnya(page, panelTujuan) {
  await page.click('#btnNext');
  await expect(page.locator(`#panel-${panelTujuan}`)).toHaveClass(/active/);
}

// Isi step 1 (data diri) dengan data valid minimal.
async function isiStep1(page, nama) {
  await page.fill('#fNamaInovasi', nama);
  await page.fill('#fJudul', `Judul ${nama}`);
  await page.selectOption('#fBidangId', { index: 1 }); // pilih opsi pertama yang tersedia
  await page.fill('#fInteraksi', 'Teknologi Tepat Guna');
  await page.selectOption('#fKategori', 'umum'); // hindari wajib isi asal_sekolah
  await page.fill('#fInovator', 'Instansi Uji Coba');
  await page.fill('#fKetuaNama', 'Ketua Uji Coba');
  await page.fill('#fKetuaEmail', 'ketua.ujicoba@example.com');
  await page.fill('#fKetuaWa', '081234567890');
  await page.fill('#fAlamatKetua', 'Jl. Uji Coba No. 1');
  await page.fill('#fKtp', '1234567890123456'); // wajib tepat 16 digit
}

// Isi step 2 (narasi) — semua field yang ditandai wajib di form.
async function isiStep2(page) {
  const narasiWajib = [
    'fLatarBelakang', 'fKondisiSebelumnya', 'fSasaranTujuan',
    'fDeskripsi', 'fCaraKerja', 'fKeunggulan',
    'fHasilDiharapkan', 'fManfaat', 'fRencanaBerkelanjutan',
  ];
  for (const id of narasiWajib) {
    await page.fill(`#${id}`, 'Isi narasi untuk keperluan pengujian otomatis.');
  }
}

// Isi wizard lengkap (step 1 + 2) lalu klik Simpan, dan tunggu event 'load'
// dari reload otomatisnya beneran kejadian sebelum lanjut.
async function buatUsulanBaru(page, nama) {
  await bukaModalTambah(page);
  await isiStep1(page, nama);
  await klikSelanjutnya(page, 2);
  await isiStep2(page);
  await klikSelanjutnya(page, 3);

  // Pasang listener SEBELUM klik Simpan, supaya nggak kelewat event
  // 'load' yang dipicu location.reload() ~500ms kemudian.
  const reloadSelesai = page.waitForEvent('load');
  await page.click('#btnSimpan');
  await expect(page.getByText('Usulan berhasil disimpan.')).toBeVisible();
  await reloadSelesai;
}

test.describe('Inovasi - navigasi & akses', () => {
  test('belum login diarahkan ke halaman login', async ({ page }) => {
    await page.goto('/inovasi/riwayat');
    await expect(page).toHaveURL(/\/(login|sign-in)/);
  });

  test('peserta bisa lihat daftar sub event di riwayat', async ({ pesertaPage }) => {
    await pesertaPage.goto('/inovasi/riwayat');
    await expect(pesertaPage.getByRole('heading', { name: 'Riwayat Inovasi' })).toBeVisible();
  });

  test('admin diarahkan ke usulan-riwayat (bukan form usulan) saat buka halaman usulan', async ({ authedPage }) => {
    await authedPage.goto('/inovasi/riwayat');
    const firstCard = authedPage.getByRole('link', { name: /Kelola Usulan/i }).first();
    await expect(firstCard).toBeVisible();
    await firstCard.click();

    // InovasiController@usulan: admin_bapperida di-redirect ke usulan-riwayat
    await expect(authedPage).toHaveURL(/\/inovasi\/usulan-riwayat\/\d+/);
  });
});

test.describe('Inovasi - CRUD usulan (peserta)', () => {
  test('peserta bisa tambah usulan baru (draft, belum dikirim)', async ({ pesertaPage }) => {
    const nama = uniqueName();
    await gotoFirstUsulanPage(pesertaPage);
    await buatUsulanBaru(pesertaPage, nama);

    await expect(pesertaPage.locator('#modalUsulan')).toBeHidden();
    const card = pesertaPage.locator('.u-card').filter({ hasText: nama });
    await expect(card).toBeVisible();
    await expect(card.getByText('Melengkapi Data')).toBeVisible();
  });

  test('validasi: tidak bisa lanjut ke step 2 kalau field wajib step 1 kosong', async ({ pesertaPage }) => {
    await gotoFirstUsulanPage(pesertaPage);
    await bukaModalTambah(pesertaPage);

    // Langsung klik Next tanpa isi apa-apa
    await pesertaPage.click('#btnNext');

    // Tetap di panel 1, muncul pesan error
    await expect(pesertaPage.locator('#panel-1')).toHaveClass(/active/);
    await expect(pesertaPage.locator('#e-nama_inovasi')).toBeVisible();
  });

  test('validasi: KTP harus tepat 16 digit', async ({ pesertaPage }) => {
    const nama = uniqueName();
    await gotoFirstUsulanPage(pesertaPage);
    await bukaModalTambah(pesertaPage);

    await isiStep1(pesertaPage, nama);
    // Timpa KTP jadi kurang dari 16 digit
    await pesertaPage.fill('#fKtp', '12345');
    await pesertaPage.click('#btnNext');

    await expect(pesertaPage.locator('#panel-1')).toHaveClass(/active/);
    await expect(pesertaPage.getByText(/NIK harus tepat 16 digit/i)).toBeVisible();
  });

  test('peserta bisa hapus usulan draft', async ({ pesertaPage }) => {
    const nama = uniqueName();
    await gotoFirstUsulanPage(pesertaPage);
    await buatUsulanBaru(pesertaPage, nama);

    // Hapus TIDAK reload halaman — elemen di-remove() langsung via JS
    const card = pesertaPage.locator('.u-card').filter({ hasText: nama });
    await card.locator('.btn-hapus').click();

    await expect(pesertaPage.locator('#modalHapus')).toBeVisible();
    await pesertaPage.click('#btnOkHapus');

    await expect(pesertaPage.getByText('Usulan berhasil dihapus.')).toBeVisible();
    await expect(pesertaPage.locator('.u-card').filter({ hasText: nama })).toHaveCount(0);
  });

  test('peserta bisa kirim usulan setelah semua field wajib lengkap', async ({ pesertaPage }) => {
    const nama = uniqueName();
    await gotoFirstUsulanPage(pesertaPage);
    await buatUsulanBaru(pesertaPage, nama);

    const card = pesertaPage.locator('.u-card').filter({ hasText: nama });

    // Pasang listener SEBELUM klik Kirim, sama alasannya kayak di atas
    const reloadSelesai = pesertaPage.waitForEvent('load');
    await card.locator('.btn-kirim').click();

    await expect(pesertaPage.locator('#modalKirim')).toBeVisible();
    await pesertaPage.click('#btnOkKirim');
    await expect(pesertaPage.getByText('Usulan berhasil dikirim!')).toBeVisible();
    await reloadSelesai;

    // Status berubah jadi "Sedang Dinilai", tombol aksi berganti jadi "Sudah dikirim"
    const cardSetelahKirim = pesertaPage.locator('.u-card').filter({ hasText: nama });
    await expect(cardSetelahKirim.getByText('Sedang Dinilai')).toBeVisible();
    await expect(cardSetelahKirim.getByText('Sudah dikirim')).toBeVisible();
  });
});