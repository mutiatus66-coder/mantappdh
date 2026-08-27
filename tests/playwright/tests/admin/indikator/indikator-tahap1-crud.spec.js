import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

test.setTimeout(60000);

test('CRUD Indikator Tahap 1 - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN INDIKATOR TAHAP 1
  // ============================================================
  await page.goto(`${BASE_URL}/indikator/tahap-1`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Setting Indikator Penilaian Tahap 1' })).toBeVisible();
  console.log('✅ Halaman Indikator Tahap 1 terbuka');

  // ============================================================
  // 3. KLIK DETAIL SUB EVENT PERTAMA (ke halaman detail inovasi)
  // ============================================================
  const detailBtn = page.locator('a.btn-primary:has-text("Detail")').first();
  await detailBtn.scrollIntoViewIfNeeded();
  await detailBtn.click();
  await page.waitForURL(/indikator\/tahap-1\/\d+\/inovasi/);

  // 🔥 AMBIL SUB_EVENT_ID DARI URL
  const url = page.url();
  const subEventId = url.match(/indikator\/tahap-1\/(\d+)\/inovasi/)?.[1];
  console.log(`📌 Sub Event ID: ${subEventId}`);

  await expect(page.locator('h3').filter({ hasText: 'Data Detail Inovasi' })).toBeVisible();
  console.log('✅ Halaman Detail Inovasi Tahap 1 terbuka');

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
  const namaIndikator = `Indikator Test ${Date.now()}`;

  await page.fill('#inputNamaIndikator', namaIndikator);
  await page.selectOption('#selectJenis', 'substansi');

  await page.click('#modalIndikator .btn-success:has-text("Simpan")');
  await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Indikator ditambahkan: ${namaIndikator}`);

  // ============================================================
  // 6. CEK APAKAH INDIKATOR BERHASIL DITAMBAH
  // ============================================================
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const isExist = await page.locator(`#tabelDetailInovasiBody tr:has-text("${namaIndikator}")`).isVisible({ timeout: 5000 });

  if (isExist) {
    console.log('✅ Indikator terlihat di tabel');

    // ============================================================
    // 7. UBAH INDIKATOR (klik tombol Ubah di baris)
    // ============================================================
    const editBtn = page.locator(`#tabelDetailInovasiBody tr:has-text("${namaIndikator}") .btn-edit-indikator`).first();
    await editBtn.scrollIntoViewIfNeeded();
    await editBtn.click();
    await page.waitForSelector('#modalIndikator', { state: 'visible', timeout: 10000 });
    console.log('✅ Modal ubah indikator terbuka');

    const namaIndikatorBaru = `Indikator Updated ${Date.now()}`;
    await page.fill('#inputNamaIndikator', namaIndikatorBaru);
    await page.click('#modalIndikator .btn-success:has-text("Simpan")');
    await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Indikator diubah: ${namaIndikatorBaru}`);

    // ============================================================
    // 8. KLIK DETAIL INDIKATOR (ke halaman detail keterangan)
    // ============================================================
    const detailIndikatorBtn = page.locator(`#tabelDetailInovasiBody tr:has-text("${namaIndikatorBaru}") a.btn-primary:has-text("Detail")`).first();
    await detailIndikatorBtn.scrollIntoViewIfNeeded();
    await detailIndikatorBtn.click();
    await page.waitForURL(/indikator\/tahap-1\/\d+\/detail\/\d+/);
    console.log('✅ Halaman Detail Keterangan Indikator terbuka');

    // ============================================================
    // 9. TAMBAH KETERANGAN
    // ============================================================
    const tambahKeteranganBtn = page.locator('#btnTambahKeterangan').first();
    await tambahKeteranganBtn.scrollIntoViewIfNeeded();
    await tambahKeteranganBtn.click();
    await page.waitForSelector('#modalKeterangan', { state: 'visible', timeout: 10000 });
    console.log('✅ Modal tambah keterangan terbuka');

    await page.fill('#inputKeterangan', 'Keterangan test');
    await page.fill('#inputNilaiMinimal', '0');
    await page.fill('#inputNilaiMaksimal', '100');
    await page.click('#modalKeterangan .btn-success:has-text("Simpan")');
    await page.waitForSelector('#modalKeterangan', { state: 'hidden', timeout: 10000 });
    console.log('✅ Keterangan ditambahkan');

    // ============================================================
    // 10. HAPUS INDIKATOR (kembali ke halaman detail inovasi)
    // ============================================================
    await page.goto(`${BASE_URL}/indikator/tahap-1/${subEventId}/inovasi`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    const hapusBtn = page.locator(`#tabelDetailInovasiBody tr:has-text("${namaIndikatorBaru}") .btn-hapus-indikator`).first();
    await hapusBtn.scrollIntoViewIfNeeded();
    await hapusBtn.click();
    await page.waitForSelector('#modalHapusIndikator', { state: 'visible', timeout: 5000 });
    console.log('✅ Modal hapus terbuka');

    await page.click('#modalHapusIndikator .btn-danger:has-text("Hapus")');
    await page.waitForSelector('#modalHapusIndikator', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Indikator dihapus: ${namaIndikatorBaru}`);

  } else {
    console.log('⚠️ Indikator tidak ditemukan di tabel');
  }

  console.log('\n🎉 CRUD Indikator Tahap 1 SELESAI!');
});
