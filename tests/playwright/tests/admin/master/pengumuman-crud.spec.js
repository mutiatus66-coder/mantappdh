import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

test.setTimeout(60000);

test('CRUD Pengumuman - Admin', async ({ page }) => {

  // ============================================================
  // 1. LOGIN
  // ============================================================
  await page.goto(BASE_URL);
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', ADMIN_PASSWORD);
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/index`);
  console.log('✅ Login Admin berhasil');

  // ============================================================
  // 2. BUKA HALAMAN PENGUMUMAN
  // ============================================================
  await page.goto(`${BASE_URL}/pengumuman`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Master Pengumuman' })).toBeVisible();
  console.log('✅ Halaman Pengumuman terbuka');

  // ============================================================
  // 3. TAMBAH PENGUMUMAN
  // ============================================================
  const judulPengumuman = `Pengumuman Test ${Date.now()}`;

  await page.click('#btnTambahPengumuman');
  await page.waitForSelector('#modalPengumuman', { state: 'visible' });
  await page.fill('#pJudul', judulPengumuman);
  await page.fill('#pDeskripsi', 'Ini adalah deskripsi pengumuman test');
  await page.selectOption('#pStatus', 'Published');
  await page.click('#btnSimpanPengumuman');
  await page.waitForSelector('#modalPengumuman', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Pengumuman ditambahkan: ${judulPengumuman}`);

  // ============================================================
  // 4. CARI PENGUMUMAN & UBAH
  // ============================================================
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  // Cari search box
  let searchInput = page.locator('#tabelPengumuman_filter input, .dataTables_filter input, input[type="search"]').first();
  await searchInput.waitFor({ state: 'visible', timeout: 15000 });
  await searchInput.fill(judulPengumuman);
  await page.waitForTimeout(3000);

  // Tunggu hasil search
  await page.waitForSelector(`#tabelPengumumanBody tr:has-text("${judulPengumuman}")`, { timeout: 10000 });
  await expect(page.locator('#tabelPengumumanBody')).toContainText(judulPengumuman);
  console.log('✅ Pengumuman ditemukan di tabel');

  // ============================================================
  // 5. UBAH PENGUMUMAN
  // ============================================================
  const judulBaru = `Pengumuman Updated ${Date.now()}`;

  // 🔥 CARI TOMBOL UBAH PAKAI EVALUATE
  const editResult = await page.evaluate((judul) => {
    const rows = document.querySelectorAll('#tabelPengumumanBody tr');
    for (const row of rows) {
      if (row.textContent.includes(judul)) {
        const btn = row.querySelector('.btn-edit-pengumuman');
        if (btn) {
          btn.click();
          return 'success';
        }
      }
    }
    return 'not found';
  }, judulPengumuman);

  console.log(`🔍 Hasil cari tombol Ubah: ${editResult}`);

  if (editResult === 'not found') {
    const anyEditBtn = page.locator('.btn-edit-pengumuman').first();
    if (await anyEditBtn.isVisible()) {
      await anyEditBtn.click({ force: true });
      console.log('⚠️ Klik tombol Ubah pertama (force)');
    }
  }

  await page.waitForSelector('#modalPengumuman', { state: 'visible', timeout: 10000 });
  await page.fill('#pJudul', judulBaru);
  await page.fill('#pDeskripsi', 'Deskripsi sudah diupdate');
  await page.selectOption('#pStatus', 'Draft');
  await page.click('#btnSimpanPengumuman');
  await page.waitForSelector('#modalPengumuman', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Pengumuman diubah: ${judulBaru}`);

  // ============================================================
  // 6. VERIFIKASI UBAH
  // ============================================================
  await searchInput.fill(judulBaru);
  await page.waitForTimeout(3000);
  await page.waitForSelector(`#tabelPengumumanBody tr:has-text("${judulBaru}")`, { timeout: 10000 });
  await expect(page.locator('#tabelPengumumanBody')).toContainText(judulBaru);
  console.log('✅ Perubahan pengumuman terverifikasi');

  // ============================================================
  // 7. HAPUS PENGUMUMAN
  // ============================================================
  // 🔥 CARI TOMBOL HAPUS PAKAI EVALUATE
  const hapusResult = await page.evaluate((judul) => {
    const rows = document.querySelectorAll('#tabelPengumumanBody tr');
    for (const row of rows) {
      if (row.textContent.includes(judul)) {
        const btn = row.querySelector('.btn-hapus-pengumuman');
        if (btn) {
          btn.click();
          return 'success';
        }
      }
    }
    return 'not found';
  }, judulBaru);

  console.log(`🔍 Hasil cari tombol Hapus: ${hapusResult}`);

  if (hapusResult === 'not found') {
    const anyHapusBtn = page.locator('.btn-hapus-pengumuman').first();
    if (await anyHapusBtn.isVisible()) {
      await anyHapusBtn.click({ force: true });
      console.log('⚠️ Klik tombol Hapus pertama (force)');
    }
  }

  await page.waitForSelector('#modalHapusPengumuman', { state: 'visible', timeout: 5000 });
  await page.click('#btnHapusPengumuman');

  try {
    await page.waitForSelector('#modalHapusPengumuman', { state: 'hidden', timeout: 10000 });
    console.log('✅ Modal hapus tertutup');
  } catch (e) {
    console.log('⚠️ Modal hapus tidak tertutup, klik batal');
    await page.click('#btnBatalHapusPengumuman');
    await page.waitForSelector('#modalHapusPengumuman', { state: 'hidden', timeout: 5000 });
  }

  // ============================================================
  // 8. VERIFIKASI HAPUS
  // ============================================================
  await searchInput.fill(judulBaru);
  await page.waitForTimeout(1000);

  const isExist = await page.locator(`#tabelPengumumanBody tr:has-text("${judulBaru}")`).isVisible({ timeout: 3000 });
  expect(isExist).toBe(false);
  console.log(`✅ Pengumuman dihapus: ${judulBaru}`);

  await searchInput.fill('');

  console.log('\n🎉 CRUD Pengumuman SELESAI!');
});
