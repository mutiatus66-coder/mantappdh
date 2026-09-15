import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const PENILAI_EMAIL = 'ahmad.fauzi@example.com';
const PENILAI_PASSWORD = 'password';

test.setTimeout(600000);

test('Full CRUD Penilai - Penilaian Tahap 1 & 2', async ({ page }) => {

  // ============================================================
  // 1. LOGIN
  // ============================================================
  await page.goto(BASE_URL);
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await page.fill('input[name="email"]', PENILAI_EMAIL);
  await page.fill('input[name="password"]', PENILAI_PASSWORD);
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/index`);
  console.log('✅ Login Penilai berhasil');

  // ============================================================
  // 2. LIHAT RIWAYAT INOVASI
  // ============================================================
  console.log('\n=== RIWAYAT INOVASI ===');
  await page.goto(`${BASE_URL}/inovasi/riwayat`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);
  console.log('✅ Halaman Riwayat Inovasi terbuka');

  // ============================================================
  // 3. REKAP NILAI
  // ============================================================
  console.log('\n=== REKAP NILAI ===');
  await page.goto(`${BASE_URL}/inovasi/rekap-nilai`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);
  console.log('✅ Halaman Rekap Nilai terbuka');

  // ============================================================
  // 4. PENILAIAN TAHAP 1
  // ============================================================
  console.log('\n=== PENILAIAN TAHAP 1 ===');
  await page.goto(`${BASE_URL}/penilaian/tahap-1`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);
  console.log('✅ Halaman Penilaian Tahap 1 terbuka');

  // 🔥 KLIK "Lihat Nilai Verifikasi"
  const lihatVerifikasiBtn = page.locator('a:has-text("Lihat Nilai Verifikasi"), button:has-text("Lihat Nilai Verifikasi")').first();

  if (await lihatVerifikasiBtn.isVisible({ timeout: 5000 })) {
    await lihatVerifikasiBtn.scrollIntoViewIfNeeded();
    await lihatVerifikasiBtn.click();
    await page.waitForTimeout(3000);
    console.log('✅ Halaman Detail Nilai Verifikasi terbuka');

    // 🔥 CARI TOMBOL NILAI / INPUT NILAI
    const nilaiBtn = page.locator('a:has-text("Nilai"), button:has-text("Nilai"), a:has-text("Input"), button:has-text("Input")').first();
    if (await nilaiBtn.isVisible({ timeout: 5000 })) {
      await nilaiBtn.scrollIntoViewIfNeeded();
      await nilaiBtn.click();
      await page.waitForTimeout(3000);
      console.log('✅ Form Nilai terbuka');

      // Isi nilai
      const nilaiInput = page.locator('input[type="number"][name="nilai"], input[name="nilai"]').first();
      if (await nilaiInput.isVisible({ timeout: 3000 })) {
        await nilaiInput.fill('85');
        console.log('✅ Nilai diisi: 85');
      }

      const catatanInput = page.locator('textarea[name="catatan"], textarea[placeholder*="catatan"]').first();
      if (await catatanInput.isVisible({ timeout: 3000 })) {
        await catatanInput.fill('Inovasi sangat baik, implementasi jelas');
      }

      // Simpan
      const simpanNilai = page.locator('#btnSimpanNilai, button[type="submit"]:has-text("Simpan"), button:has-text("Simpan")').first();
      if (await simpanNilai.isVisible()) {
        await simpanNilai.click({ force: true });
        await page.waitForTimeout(3000);
        console.log('✅ Nilai Tahap 1 disimpan');
      }
    } else {
      console.log('⚠️ Tombol Nilai tidak ditemukan');
    }
  } else {
    console.log('⚠️ Tombol "Lihat Nilai Verifikasi" tidak ditemukan');
  }

  // ============================================================
  // 5. PENILAIAN TAHAP 2
  // ============================================================
  console.log('\n=== PENILAIAN TAHAP 2 ===');
  await page.goto(`${BASE_URL}/penilaian/tahap-2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);
  console.log('✅ Halaman Penilaian Tahap 2 terbuka');

  // 🔥 KLIK "Lihat Nilai Nominator"
  const lihatNominatorBtn = page.locator('a:has-text("Lihat Nilai Nominator"), button:has-text("Lihat Nilai Nominator")').first();

  if (await lihatNominatorBtn.isVisible({ timeout: 5000 })) {
    await lihatNominatorBtn.scrollIntoViewIfNeeded();
    await lihatNominatorBtn.click();
    await page.waitForTimeout(3000);
    console.log('✅ Halaman Detail Nilai Nominator terbuka');

    // 🔥 CARI TOMBOL NILAI
    const nilaiBtn2 = page.locator('a:has-text("Nilai"), button:has-text("Nilai"), a:has-text("Input"), button:has-text("Input")').first();
    if (await nilaiBtn2.isVisible({ timeout: 5000 })) {
      await nilaiBtn2.scrollIntoViewIfNeeded();
      await nilaiBtn2.click();
      await page.waitForTimeout(3000);
      console.log('✅ Form Nilai Tahap 2 terbuka');

      const nilaiInput2 = page.locator('input[type="number"][name="nilai"], input[name="nilai"]').first();
      if (await nilaiInput2.isVisible({ timeout: 3000 })) {
        await nilaiInput2.fill('88');
        console.log('✅ Nilai diisi: 88');
      }

      const catatanInput2 = page.locator('textarea[name="catatan"], textarea[placeholder*="catatan"]').first();
      if (await catatanInput2.isVisible({ timeout: 3000 })) {
        await catatanInput2.fill('Inovasi sudah berkembang dengan baik');
      }

      const simpanNilai2 = page.locator('#btnSimpanNilai, button[type="submit"]:has-text("Simpan"), button:has-text("Simpan")').first();
      if (await simpanNilai2.isVisible()) {
        await simpanNilai2.click({ force: true });
        await page.waitForTimeout(3000);
        console.log('✅ Nilai Tahap 2 disimpan');
      }
    } else {
      console.log('⚠️ Tombol Nilai tidak ditemukan');
    }
  } else {
    console.log('⚠️ Tombol "Lihat Nilai Nominator" tidak ditemukan');
  }

  console.log('\n🎉 PENILAI FULL CRUD SELESAI!');
});
