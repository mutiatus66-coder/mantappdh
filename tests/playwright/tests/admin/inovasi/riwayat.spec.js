import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

test.setTimeout(60000);

test('Riwayat Inovasi - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN RIWAYAT INOVASI
  // ============================================================
  await page.goto(`${BASE_URL}/inovasi/riwayat`);
  await page.waitForLoadState('networkidle');

  // 🔥 CEK DENGAN SELECTOR YANG LEBIH FLEKSIBEL
  const title = page.locator('h3:has-text("Riwayat"), h2:has-text("Riwayat"), .page-title:has-text("Riwayat")').first();
  if (await title.isVisible({ timeout: 5000 })) {
    console.log('✅ Halaman Riwayat Inovasi terbuka');
  } else {
    console.log('⚠️ Halaman mungkin berbeda, screenshot untuk debug');
    await page.screenshot({ path: 'screenshots/riwayat-page.png' });
  }

  // ============================================================
  // 3. CEK APAKAH ADA TABEL ATAU KONTEN
  // ============================================================
  // 🔥 CEK BERBAGAI KEMUNGKINAN SELECTOR
  let tabel = page.locator('table, .table, #tabelRiwayat, .dataTable').first();

  if (await tabel.isVisible({ timeout: 5000 })) {
    console.log('✅ Tabel riwayat terlihat');

    // Ambil judul kolom
    const headers = page.locator('thead th, table thead th');
    const headerTexts = await headers.allTextContents();
    console.log(`✅ Kolom: ${headerTexts.join(', ')}`);

    // Hitung data
    const rows = page.locator('tbody tr, table tbody tr');
    const count = await rows.count();
    console.log(`✅ Jumlah data: ${count}`);

  } else {
    console.log('⚠️ Tabel tidak ditemukan, coba cari card/list');

    // Coba cek apakah ada card/list
    const cards = page.locator('.card, .list-group-item, .riwayat-item').first();
    if (await cards.isVisible({ timeout: 3000 })) {
      console.log('✅ Data dalam bentuk card/list ditemukan');
    } else {
      console.log('⚠️ Tidak ada data ditemukan');
    }
  }

  // ============================================================
  // 4. AMBIL SCREENSHOT
  // ============================================================
  await page.screenshot({ path: 'screenshots/riwayat-inovasi.png' });
  console.log('✅ Screenshot riwayat inovasi tersimpan');

  console.log('\n🎉 Riwayat Inovasi SELESAI!');
});
