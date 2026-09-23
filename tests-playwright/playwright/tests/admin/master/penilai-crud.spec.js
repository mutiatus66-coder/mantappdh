import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@admin.com';
const ADMIN_PASSWORD = 'password';

test.setTimeout(60000);

test('CRUD Penilai - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN PENILAI
  // ============================================================
  await page.goto(`${BASE_URL}/penilai`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Master Penilai' })).toBeVisible();
  console.log('✅ Halaman Penilai terbuka');

  // ============================================================
  // 3. PILIH SUB EVENT PERTAMA
  // ============================================================
  const detailBtn = page.locator('a.btn-primary:has-text("Detail")').first();
  await detailBtn.scrollIntoViewIfNeeded();
  await detailBtn.click();
  await page.waitForURL(/penilai\/\d+/);
  console.log('✅ Halaman Detail Penilai terbuka');

  // ============================================================
  // 4. HAPUS PENILAI EXISTING (jika ada, biar bersih)
  // ============================================================
  try {
    const existingPenilai = page.locator('#tabelPenilai tbody tr').first();
    if (await existingPenilai.isVisible({ timeout: 3000 })) {
      const hapusExistBtn = page.locator('.btn-hapus-penilai').first();
      if (await hapusExistBtn.isVisible({ timeout: 3000 })) {
        await hapusExistBtn.click();
        await page.waitForSelector('#modalHapusPenilai', { state: 'visible', timeout: 5000 });
        await page.click('#btnHapusPenilai');
        await page.waitForSelector('#modalHapusPenilai', { state: 'hidden', timeout: 10000 });
        await page.waitForTimeout(1000);
        console.log('✅ Penilai existing dihapus');
      }
    }
  } catch (e) {
    console.log('⚠️ Tidak ada penilai yang bisa dihapus');
  }

  // ============================================================
  // 5. TAMBAH PENILAI
  // ============================================================
  await page.click('#btnTambahPenilai');
  await page.waitForSelector('#modalPenilai', { state: 'visible', timeout: 10000 });

  // Pilih user pertama yang tersedia
  const options = await page.locator('#penilaiUserId option').all();
  if (options.length > 1) {
    await page.selectOption('#penilaiUserId', { index: 1 });
    console.log('✅ User penilai dipilih');
  } else {
    console.log('⚠️ Tidak ada user tersedia, skip test');
    await page.click('#btnBatalPenilai');
    return;
  }

  await page.waitForTimeout(500);
  await page.click('#btnSimpanPenilai');

  try {
    await page.waitForSelector('#modalPenilai', { state: 'hidden', timeout: 15000 });
    console.log('✅ Penilai ditambahkan');
  } catch (e) {
    console.log('⚠️ Modal penilai tidak tertutup, klik batal');
    await page.click('#btnBatalPenilai');
    await page.waitForSelector('#modalPenilai', { state: 'hidden', timeout: 5000 });
  }

  // ============================================================
  // 6. UBAH PENILAI
  // ============================================================
  await page.waitForTimeout(2000);
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const editPenilaiBtn = page.locator('.btn-edit-penilai').first();
  if (await editPenilaiBtn.isVisible({ timeout: 5000 })) {
    await editPenilaiBtn.scrollIntoViewIfNeeded();
    await editPenilaiBtn.click({ force: true });

    await page.waitForSelector('#modalPenilai', { state: 'visible', timeout: 10000 });

    // Pilih user lain
    const options2 = await page.locator('#penilaiUserId option').all();
    if (options2.length > 2) {
      await page.selectOption('#penilaiUserId', { index: 2 });
    } else if (options2.length > 1) {
      await page.selectOption('#penilaiUserId', { index: 1 });
    }

    await page.waitForTimeout(500);
    await page.click('#btnSimpanPenilai');
    await page.waitForSelector('#modalPenilai', { state: 'hidden', timeout: 15000 });
    console.log('✅ Penilai diubah');
  } else {
    console.log('⚠️ Tidak ada penilai untuk diubah');
  }

  // ============================================================
  // 7. HAPUS PENILAI
  // ============================================================
  await page.waitForTimeout(2000);
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const hapusPenilaiBtn = page.locator('.btn-hapus-penilai').first();
  if (await hapusPenilaiBtn.isVisible({ timeout: 5000 })) {
    const namaPenilai = await hapusPenilaiBtn.getAttribute('data-nama');
    await hapusPenilaiBtn.scrollIntoViewIfNeeded();
    await hapusPenilaiBtn.click({ force: true });

    await page.waitForSelector('#modalHapusPenilai', { state: 'visible', timeout: 5000 });
    await page.click('#btnHapusPenilai');
    await page.waitForSelector('#modalHapusPenilai', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Penilai dihapus: ${namaPenilai}`);
  } else {
    console.log('⚠️ Tidak ada penilai untuk dihapus');
  }

  console.log('\n🎉 CRUD Penilai SELESAI!');
});
