import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@admin.com';
const ADMIN_PASSWORD = 'password';

test.setTimeout(120000);

test('Debug Indikator Tahap 2', async ({ page }) => {

  // Login
  await page.goto(BASE_URL);
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', ADMIN_PASSWORD);
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/index`);
  console.log('✅ Login Admin berhasil');

  // Buka halaman Indikator Tahap 2
  await page.goto(`${BASE_URL}/indikator/tahap-2`);
  await page.waitForLoadState('networkidle');

  const detailBtn = page.locator('a.btn-primary:has-text("Detail")').first();
  await detailBtn.scrollIntoViewIfNeeded();
  await detailBtn.click();
  await page.waitForURL(/indikator\/tahap-2\/\d+\/indikator/);
  console.log('✅ Halaman Detail Indikator Tahap 2 terbuka');

  // Tambah indikator
  const namaIndikator = `Debug Test ${Date.now()}`;
  await page.click('#btnTambahIndikator');
  await page.waitForSelector('#modalIndikator', { state: 'visible' });
  await page.fill('#inputNamaIndikator', namaIndikator);
  await page.selectOption('#inputJenis', 'Subtansi Inovasi');
  await page.fill('#inputKeterangan', 'Keterangan debug');
  await page.fill('#inputNilaiMinimal', '0');
  await page.fill('#inputNilaiMaksimal', '100');
  await page.click('#modalIndikator .btn-success:has-text("Simpan")');
  await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Indikator ditambahkan: ${namaIndikator}`);

  // Reload
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  // 🔥 SCREENSHOT HALAMAN
  await page.screenshot({ path: 'screenshots/debug-ind2-1.png', fullPage: true });
  console.log('📸 Screenshot 1 tersimpan');

  // 🔥 CEK APAKAH DATA ADA DI HTML
  const bodyText = await page.locator('body').textContent();
  const dataAda = bodyText?.includes(namaIndikator);
  console.log(`📌 Data "${namaIndikator}" ada di halaman? ${dataAda ? '✅ YA' : '❌ TIDAK'}`);

  // 🔥 CEK SEMUA TOMBOL YANG ADA
  const allButtons = await page.locator('#tabelTahap2DetailBody button, #tabelTahap2DetailBody a').allTextContents();
  console.log('📌 Semua tombol di tabel:', allButtons.slice(0, 20));

  // 🔥 CEK JUMLAH BARIS
  const rowCount = await page.locator('#tabelTahap2DetailBody tr').count();
  console.log(`📌 Jumlah baris: ${rowCount}`);

  // 🔥 CEK HALAMAN PAGINATION
  const pageInfo = await page.locator('.dataTables_info, .dt-info').textContent();
  console.log(`📌 Info: ${pageInfo}`);

  // 🔥 SCROLL KE BAWAH
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(2000);
  await page.screenshot({ path: 'screenshots/debug-ind2-2.png', fullPage: true });
  console.log('📸 Screenshot 2 tersimpan');
});
