import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

test.setTimeout(60000);

test('Rekap Nilai Inovasi - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN REKAP NILAI
  // ============================================================
  await page.goto(`${BASE_URL}/inovasi/rekap-nilai`);
  await page.waitForLoadState('networkidle');

  const title = page.locator('h3:has-text("Rekap Nilai"), h2:has-text("Rekap"), .page-title:has-text("Rekap")').first();
  if (await title.isVisible({ timeout: 5000 })) {
    console.log('✅ Halaman Rekap Nilai terbuka');
  } else {
    console.log('⚠️ Halaman mungkin berbeda');
    await page.screenshot({ path: 'screenshots/rekap-page.png' });
  }

  // ============================================================
  // 3. CEK APAKAH ADA TABEL ATAU KONTEN
  // ============================================================
  let tabel = page.locator('table, .table, #tabelRekapNilai, .dataTable').first();

  if (await tabel.isVisible({ timeout: 5000 })) {
    console.log('✅ Tabel rekap nilai terlihat');

    const headers = page.locator('thead th, table thead th');
    const headerTexts = await headers.allTextContents();
    console.log(`✅ Kolom: ${headerTexts.join(', ')}`);

    const rows = page.locator('tbody tr, table tbody tr');
    const count = await rows.count();
    console.log(`✅ Jumlah data: ${count}`);

  } else {
    console.log('⚠️ Tabel tidak ditemukan');
  }

  // ============================================================
  // 4. AMBIL SCREENSHOT
  // ============================================================
  await page.screenshot({ path: 'screenshots/rekap-nilai.png' });
  console.log('✅ Screenshot rekap nilai tersimpan');

  console.log('\n🎉 Rekap Nilai Inovasi SELESAI!');
});
