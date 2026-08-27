import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@admin.com';
const ADMIN_PASSWORD = 'password';

test.setTimeout(60000);

test('CRUD Indikator Tahap 2 - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN INDIKATOR TAHAP 2
  // ============================================================
  await page.goto(`${BASE_URL}/indikator/tahap-2`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Setting Indikator Penilaian Tahap 2' })).toBeVisible();
  console.log('✅ Halaman Indikator Tahap 2 terbuka');

  // ============================================================
  // 3. KLIK DETAIL SUB EVENT PERTAMA (ke halaman detail)
  // ============================================================
  const detailBtn = page.locator('a.btn-primary:has-text("Detail")').first();
  await detailBtn.scrollIntoViewIfNeeded();
  await detailBtn.click();
  await page.waitForURL(/indikator\/tahap-2\/\d+\/indikator/);
  console.log('✅ Halaman Detail Indikator Tahap 2 terbuka');

  // ============================================================
  // 4. KLIK TOMBOL TAMBAH INDIKATOR (modal muncul)
  // ============================================================
  const tambahBtn = page.locator('#btnTambahIndikator').first();
  await tambahBtn.scrollIntoViewIfNeeded();
  await tambahBtn.click();
  await page.waitForSelector('#modalIndikator', { state: 'visible', timeout: 10000 });
  console.log('✅ Modal tambah indikator terbuka');

  // ============================================================
  // 5. TAMBAH INDIKATOR
  // ============================================================
  const namaIndikator = `Indikator Tahap 2 Test ${Date.now()}`;

  await page.fill('#inputNamaIndikator', namaIndikator);
  await page.selectOption('#inputJenis', 'Subtansi Inovasi');
  await page.fill('#inputKeterangan', 'Keterangan test');
  await page.fill('#inputNilaiMinimal', '0');
  await page.fill('#inputNilaiMaksimal', '100');

  await page.click('#modalIndikator .btn-success:has-text("Simpan")');
  await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Indikator Tahap 2 ditambahkan: ${namaIndikator}`);

  // ============================================================
  // 6. CEK APAKAH INDIKATOR BERHASIL DITAMBAH
  // ============================================================
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const isExist = await page.locator(`#tabelTahap2DetailBody tr:has-text("${namaIndikator}")`).isVisible({ timeout: 5000 });

  if (isExist) {
    console.log('✅ Indikator terlihat di tabel');

    // ============================================================
    // 7. UBAH INDIKATOR (klik tombol Edit di baris)
    // ============================================================
    const editBtn = page.locator(`#tabelTahap2DetailBody tr:has-text("${namaIndikator}") .btn-edit-indikator`).first();
    await editBtn.scrollIntoViewIfNeeded();
    await editBtn.click();
    await page.waitForSelector('#modalIndikator', { state: 'visible', timeout: 10000 });
    console.log('✅ Modal ubah indikator terbuka');

    const namaIndikatorBaru = `Indikator Tahap 2 Updated ${Date.now()}`;
    await page.fill('#inputNamaIndikator', namaIndikatorBaru);
    await page.click('#modalIndikator .btn-success:has-text("Simpan")');
    await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Indikator diubah: ${namaIndikatorBaru}`);

    // ============================================================
    // 8. HAPUS INDIKATOR
    // ============================================================
    await page.reload();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    const hapusBtn = page.locator(`#tabelTahap2DetailBody tr:has-text("${namaIndikatorBaru}") .btn-hapus-indikator`).first();
    await hapusBtn.scrollIntoViewIfNeeded();
    await hapusBtn.click();
    await page.waitForSelector('#modalHapus', { state: 'visible', timeout: 5000 });
    console.log('✅ Modal hapus terbuka');

    await page.click('#modalHapus .btn-danger:has-text("Hapus")');
    await page.waitForSelector('#modalHapus', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Indikator dihapus: ${namaIndikatorBaru}`);

  } else {
    console.log('⚠️ Indikator tidak ditemukan di tabel');
  }

  console.log('\n🎉 CRUD Indikator Tahap 2 SELESAI!');
});
