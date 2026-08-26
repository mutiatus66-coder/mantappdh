import { test, expect, login, USERS } from './fixtures';

/**
 * Test CRUD Event (resources/views/master/event.blade.php).
 * Halaman ini pakai AJAX + modal Bootstrap + DataTables (bukan reload biasa),
 * jadi kita nunggu toast & baris tabel muncul/berubah, bukan nunggu navigasi.
 */

// Nama unik per run supaya nggak bentrok sama data lama / run sebelumnya
const uniqueName = () => `Test Event ${Date.now()}`;

// Cari baris di tabel berdasarkan teks (substring biasa, BUKAN regex) —
// aman dari karakter spesial regex kayak "(", ")" yang mungkin ada di nama event.
function eventRow(page, text) {
  return page.locator('#tabelEventBody tr').filter({ hasText: text });
}

test.describe('Event - proteksi akses', () => {
  test('peserta tidak bisa akses /event (403)', async ({ page }) => {
    await login(page, USERS.peserta);
    // Pastikan login (redirect ke /index) beneran kelar dulu sebelum lanjut
    // navigasi ke /event — kalau enggak, kadang browser (terutama WebKit)
    // masih di tengah proses login pas request /event dikirim, jadi
    // dianggap belum login (redirect ke login) bukannya 403.
    await page.waitForURL(/\/index/);

    const response = await page.goto('/event');
    expect(response.status()).toBe(403);
  });

  test('penilai tidak bisa akses /event (403)', async ({ page }) => {
    await login(page, USERS.penilai);
    await page.waitForURL(/\/index/);

    const response = await page.goto('/event');
    expect(response.status()).toBe(403);
  });

  test('belum login diarahkan ke halaman login', async ({ page }) => {
    await page.goto('/event');
    await expect(page).toHaveURL(/\/(login|sign-in)/);
  });
});

test.describe('Event - CRUD (admin_bapperida)', () => {
  test('admin bisa lihat halaman Data Event', async ({ authedPage }) => {
    await authedPage.goto('/event');
    await expect(authedPage.getByRole('heading', { name: 'Data Event' })).toBeVisible();
    await expect(authedPage.locator('#tabelEvent')).toBeVisible();
  });

  test('admin bisa tambah event baru', async ({ authedPage }) => {
    const nama = uniqueName();

    await authedPage.goto('/event');
    await authedPage.click('#btnTambahEvent');

    await expect(authedPage.locator('#modalEvent')).toBeVisible();
    await authedPage.fill('#inputNamaEvent', nama);
    await authedPage.selectOption('#inputJenis', 'INOTEK');
    await authedPage.click('#btnSimpanEvent');

    // Toast sukses muncul
    await expect(authedPage.getByText('Event berhasil ditambahkan!')).toBeVisible();
    // Modal otomatis tertutup
    await expect(authedPage.locator('#modalEvent')).toBeHidden();

    // Cari baris barunya lewat search box DataTables (biar gak keganggu pagination)
    await authedPage.getByRole('searchbox').fill(nama);
    await expect(eventRow(authedPage, nama)).toBeVisible();
  });

  test('admin tidak bisa tambah event dengan nama+jenis yang sama (duplikat)', async ({ authedPage }) => {
    const nama = uniqueName();

    await authedPage.goto('/event');

    // Tambah pertama kali — harus sukses
    await authedPage.click('#btnTambahEvent');
    await authedPage.fill('#inputNamaEvent', nama);
    await authedPage.selectOption('#inputJenis', 'INOTEK');
    await authedPage.click('#btnSimpanEvent');
    await expect(authedPage.getByText('Event berhasil ditambahkan!')).toBeVisible();

    // Tambah lagi dengan nama & jenis persis sama — harus ditolak
    await authedPage.click('#btnTambahEvent');
    await authedPage.fill('#inputNamaEvent', nama);
    await authedPage.selectOption('#inputJenis', 'INOTEK');
    await authedPage.click('#btnSimpanEvent');
    await expect(authedPage.getByText(/sudah ada/i)).toBeVisible();
  });

  test('admin bisa ubah event', async ({ authedPage }) => {
    const namaAwal = uniqueName();
    const namaBaru = `${namaAwal} (Diubah)`;

    await authedPage.goto('/event');

    // Siapkan data dulu
    await authedPage.click('#btnTambahEvent');
    await authedPage.fill('#inputNamaEvent', namaAwal);
    await authedPage.selectOption('#inputJenis', 'INOTEK');
    await authedPage.click('#btnSimpanEvent');
    await expect(authedPage.getByText('Event berhasil ditambahkan!')).toBeVisible();
    
    // Cari baris lewat search, lalu klik "Ubah" di baris itu
    await authedPage.getByRole('searchbox').fill(namaAwal);
    const row = eventRow(authedPage, namaAwal);
    await row.getByRole('button', { name: 'Ubah' }).click();

    await expect(authedPage.locator('#modalEvent')).toBeVisible();
    await authedPage.fill('#inputNamaEvent', namaBaru);
    await authedPage.selectOption('#inputJenis', 'INODA');
    await authedPage.click('#btnSimpanEvent');

    await expect(authedPage.getByText('Event berhasil diubah!')).toBeVisible();

    await authedPage.getByRole('searchbox').fill(namaBaru);
    await expect(eventRow(authedPage, namaBaru)).toBeVisible();
  });

  test('admin bisa hapus event', async ({ authedPage }) => {
    const nama = uniqueName();

    await authedPage.goto('/event');

    // Siapkan data dulu
    await authedPage.click('#btnTambahEvent');
    await authedPage.fill('#inputNamaEvent', nama);
    await authedPage.selectOption('#inputJenis', 'INOTEK');
    await authedPage.click('#btnSimpanEvent');
    await expect(authedPage.getByText('Event berhasil ditambahkan!')).toBeVisible();

    // Cari baris lewat search, klik "Hapus", konfirmasi di modal
    await authedPage.getByRole('searchbox').fill(nama);
    const row = eventRow(authedPage, nama);
    await row.getByRole('button', { name: 'Hapus' }).click();

    await expect(authedPage.locator('#modalHapusEvent')).toBeVisible();
    await authedPage.click('#btnHapusEvent');

    await expect(authedPage.getByText(/berhasil dihapus/i)).toBeVisible();

    // Baris sudah tidak ada lagi di tabel
    await authedPage.getByRole('searchbox').fill(nama);
    await expect(eventRow(authedPage, nama)).toHaveCount(0);
  });
});