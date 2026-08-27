import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

test('CRUD Bidang - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN BIDANG
  // ============================================================
  await page.goto(`${BASE_URL}/bidang`);
  await expect(page.locator('h3').filter({ hasText: 'Master Bidang' })).toBeVisible();
  console.log('✅ Halaman Bidang terbuka');

  // ============================================================
  // 3. BUKA ACCORDION PERTAMA
  // ============================================================
  const accordionBtn = page.locator('.accordion-button').first();
  await accordionBtn.scrollIntoViewIfNeeded();
  await accordionBtn.click();
  await page.waitForTimeout(2000);

  // Ambil ID sub event pertama dari accordion
  const subEventId = await accordionBtn.getAttribute('data-se-id') || '1';
  console.log(`📌 Sub Event ID: ${subEventId}`);

  // ============================================================
  // 4. TAMBAH BIDANG
  // ============================================================
  const namaBidang = `Bidang Test ${Date.now()}`;
  const tambahBtn = page.locator('.btn-tambah-bidang').first();
  await tambahBtn.scrollIntoViewIfNeeded();
  await tambahBtn.click();
  await page.waitForSelector('#modalBidang', { state: 'visible' });
  await page.fill('#bidangNama', namaBidang);
  await page.click('#statusAktifBidang');
  await page.click('#btnSimpanBidang');
  await page.waitForSelector('#modalBidang', { state: 'hidden', timeout: 10000 });

  // 🔥 CEK DI TABEL YANG SPESIFIK (pakai ID tabel bidang)
  const tabelBidang = page.locator(`#tabelBidang-${subEventId} tbody`);
  await expect(tabelBidang).toContainText(namaBidang);
  console.log(`✅ Bidang ditambahkan: ${namaBidang}`);

  // ============================================================
  // 5. UBAH BIDANG
  // ============================================================
  const namaBidangBaru = `Bidang Updated ${Date.now()}`;

  // 🔥 CARI TOMBOL UBAH DI TABEL SPESIFIK
  const editBtn = page.locator(`#tabelBidang-${subEventId} tbody tr:has-text("${namaBidang}") .btn-ubah-bidang`).first();
  await page.waitForTimeout(1000);
  await editBtn.scrollIntoViewIfNeeded();
  await editBtn.waitFor({ state: 'visible', timeout: 10000 });
  await editBtn.click();

  await page.waitForSelector('#modalBidang', { state: 'visible' });
  await page.fill('#bidangNama', namaBidangBaru);
  await page.click('#statusNonaktifBidang');
  await page.click('#btnSimpanBidang');
  await page.waitForSelector('#modalBidang', { state: 'hidden', timeout: 10000 });

  await expect(tabelBidang).toContainText(namaBidangBaru);
  console.log(`✅ Bidang diubah: ${namaBidangBaru}`);

  // ============================================================
  // 6. HAPUS BIDANG
  // ============================================================
  const hapusBtn = page.locator(`#tabelBidang-${subEventId} tbody tr:has-text("${namaBidangBaru}") .btn-hapus-bidang`).first();
  await page.waitForTimeout(1000);
  await hapusBtn.scrollIntoViewIfNeeded();
  await hapusBtn.waitFor({ state: 'visible', timeout: 10000 });
  await hapusBtn.click();

  await page.waitForSelector('#modalHapusBidang', { state: 'visible', timeout: 5000 });
  await page.click('#btnHapusBidang');
  await page.waitForSelector('#modalHapusBidang', { state: 'hidden', timeout: 10000 });

  await expect(tabelBidang).not.toContainText(namaBidangBaru);
  console.log(`✅ Bidang dihapus: ${namaBidangBaru}`);

  console.log('\n🎉 CRUD Bidang SELESAI!');
});
