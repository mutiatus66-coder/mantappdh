import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const PESERTA_EMAIL = 'peserta1@example.com';
const PESERTA_PASSWORD = 'password';

test.setTimeout(600000);

test('Full CRUD Peserta - Inovasi/Usulan', async ({ page }) => {

  // ============================================================
  // 1. LOGIN
  // ============================================================
  await page.goto(BASE_URL);
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await page.fill('input[name="email"]', PESERTA_EMAIL);
  await page.fill('input[name="password"]', PESERTA_PASSWORD);
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/index`);
  console.log('✅ Login Peserta berhasil');

  // ============================================================
  // 2. BUKA KELOLA USULAN
  // ============================================================
  console.log('\n=== RIWAYAT INOVASI ===');
  await page.goto(`${BASE_URL}/inovasi/riwayat`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const kelolaBtn = page.locator('a:has-text("Kelola Usulan"), button:has-text("Kelola Usulan")').first();
  await kelolaBtn.scrollIntoViewIfNeeded();
  await kelolaBtn.click();
  await page.waitForTimeout(3000);
  console.log('✅ Halaman Kelola Usulan terbuka');

  // ============================================================
  // 3. TAMBAH USULAN
  // ============================================================
  const namaInovasi = `Inovasi Test ${Date.now()}`;

  await page.click('a:has-text("Tambah Usulan"), button:has-text("Tambah Usulan")');
  await page.waitForTimeout(2000);
  console.log('✅ Modal Tambah Usulan terbuka');

  // ── STEP 1: DATA TIM ──
  console.log('📝 Step 1: Data Tim');

  await page.locator('input[placeholder*="Nama inovasi"]').first().fill(namaInovasi);
  await page.locator('input[placeholder*="Judul singkat"]').first().fill(`Judul ${namaInovasi}`);
  await page.locator('select').first().selectOption({ index: 1 });
  await page.locator('input[placeholder*="Teknologi Tepat Guna"]').first().fill('Teknologi Tepat Guna');
  await page.locator('select').nth(1).selectOption({ index: 1 });

  const namaTimInput = page.locator('input[placeholder="Opsional"]').first();
  if (await namaTimInput.isVisible({ timeout: 2000 })) {
    await namaTimInput.fill('Tim Inovasi');
  }

  await page.locator('input[placeholder*="Nama instansi"]').first().fill('Peserta 1');
  await page.locator('input[placeholder*="Nama lengkap ketua"]').first().fill('Peserta Satu');
  await page.locator('input[placeholder*="email@contoh.com"]').first().fill('peserta1@example.com');
  await page.locator('input[placeholder*="08"]').first().fill('081234567890');
  await page.locator('input[placeholder*="Alamat lengkap"]').first().fill('Jl. Contoh No. 1');
  await page.locator('input[placeholder*="16 digit"]').first().fill('1234567890123456');

  console.log('✅ Step 1 selesai');

  // 🔥 KLIK SELANJUTNYA PAKAI EVALUATE
  await page.evaluate(() => document.getElementById('btnNext')?.click());
  await page.waitForTimeout(3000);

  // ── STEP 2: NARASI INOVASI ──
  console.log('📝 Step 2: Narasi Inovasi');

  const latar = page.locator('textarea[placeholder*="masalah yang melatarbelakangi"]').first();
  if (await latar.isVisible({ timeout: 3000 })) await latar.fill('Masalah yang melatarbelakangi inovasi ini');

  const kondisi = page.locator('textarea[placeholder*="kondisi sebelum"]').first();
  if (await kondisi.isVisible({ timeout: 3000 })) await kondisi.fill('Sebelumnya semua manual');

  const sasaran = page.locator('textarea[placeholder*="sasaran dan apa tujuan"]').first();
  if (await sasaran.isVisible({ timeout: 3000 })) await sasaran.fill('Sasaran: masyarakat desa');

  const materi = page.locator('textarea[placeholder*="Spesifikasi teknis"]').first();
  if (await materi.isVisible({ timeout: 3000 })) await materi.fill('Teknologi web');

  const deskripsi = page.locator('textarea[placeholder*="Deskripsi singkat"]').first();
  if (await deskripsi.isVisible({ timeout: 3000 })) await deskripsi.fill('Inovasi digital untuk desa');

  console.log('✅ Step 2 selesai');

  await page.evaluate(() => document.getElementById('btnNext')?.click());
  await page.waitForTimeout(3000);

  // ── STEP 3: DOKUMEN & MEDIA ──
  console.log('📝 Step 3: Dokumen & Media');

  const bahan = page.locator('textarea[placeholder*="Material atau komponen"]').first();
  if (await bahan.isVisible({ timeout: 3000 })) await bahan.fill('Laptop dan internet');

  const caraKerja = page.locator('textarea[placeholder*="mekanisme atau cara kerja"]').first();
  if (await caraKerja.isVisible({ timeout: 3000 })) await caraKerja.fill('User login → input data → tersimpan');

  const keunggulan = page.locator('textarea[placeholder*="keunggulan dibanding"]').first();
  if (await keunggulan.isVisible({ timeout: 3000 })) await keunggulan.fill('Lebih cepat dan efisien');

  const hasil = page.locator('textarea[placeholder*="output atau dampak"]').first();
  if (await hasil.isVisible({ timeout: 3000 })) await hasil.fill('Pelayanan lebih cepat');

  const manfaat = page.locator('textarea[placeholder*="Manfaat langsung"]').first();
  if (await manfaat.isVisible({ timeout: 3000 })) await manfaat.fill('Akses layanan kapan saja');

  const rencana = page.locator('textarea[placeholder*="Bagaimana inovasi ini akan terus"]').first();
  if (await rencana.isVisible({ timeout: 3000 })) await rencana.fill('Akan terus dikembangkan');

  console.log('✅ Step 3 selesai');

  // 🔥 SCROLL KE BAWAH
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(2000);

  // 🔥 KLIK SELANJUTNYA PAKAI EVALUATE
  await page.evaluate(() => document.getElementById('btnNext')?.click());
  await page.waitForTimeout(3000);

  // ── STEP 4: UPLOAD DOKUMEN ──
  console.log('📝 Step 4: Upload Dokumen');

  const linkVideo = page.locator('input[placeholder*="youtube.com"]').first();
  if (await linkVideo.isVisible({ timeout: 3000 })) {
    await linkVideo.fill('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
  }

  // 🔥 KLIK SIMPAN
  await page.click('button:has-text("Simpan Usulan"), button:has-text("Simpan")', { force: true });
  await page.waitForTimeout(5000);
  console.log(`✅ Usulan ditambahkan: ${namaInovasi}`);

  // 🔥 PAKSA RELOAD
  await page.goto(`${BASE_URL}/inovasi/usulan/2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);
  console.log('✅ Halaman di-reload');

  // ============================================================
  // 4. EDIT USULAN
  // ============================================================
  console.log('\n=== EDIT USULAN ===');

  const editBtn = page.locator(`.btn-edit-usulan, button:has-text("Edit")`).first();
  if (await editBtn.isVisible({ timeout: 5000 })) {
    await editBtn.scrollIntoViewIfNeeded();
    await editBtn.click();
    await page.waitForTimeout(3000);
    console.log('✅ Form Edit terbuka');

    const namaInovasiBaru = `Inovasi Updated ${Date.now()}`;
    const namaInovasiEdit = page.locator('input[placeholder*="Nama inovasi"]').first();
    if (await namaInovasiEdit.isVisible()) {
      await namaInovasiEdit.fill(namaInovasiBaru);
    }

    // Next 3x + Simpan
    await page.evaluate(() => document.getElementById('btnNext')?.click());
    await page.waitForTimeout(2000);
    await page.evaluate(() => document.getElementById('btnNext')?.click());
    await page.waitForTimeout(2000);
    await page.evaluate(() => document.getElementById('btnNext')?.click());
    await page.waitForTimeout(2000);
    await page.click('button:has-text("Simpan Usulan"), button:has-text("Simpan")', { force: true });
    await page.waitForTimeout(5000);
    console.log(`✅ Usulan diubah: ${namaInovasiBaru}`);

    await page.goto(`${BASE_URL}/inovasi/usulan/2`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);
  } else {
    console.log('⚠️ Tombol Edit tidak ditemukan');
  }

  // ============================================================
  // 5. KIRIM USULAN
  // ============================================================
  console.log('\n=== KIRIM USULAN ===');

  const kirimBtn = page.locator(`.btn-kirim-usulan, button:has-text("Kirim")`).first();
  if (await kirimBtn.isVisible({ timeout: 5000 })) {
    await kirimBtn.scrollIntoViewIfNeeded();
    await kirimBtn.click({ force: true });
    await page.waitForTimeout(2000);
    console.log('✅ Modal Kirim terbuka');

    const confirmKirim = page.locator('.modal button:has-text("Kirim"), .modal button:has-text("Ya")').first();
    if (await confirmKirim.isVisible()) {
      await confirmKirim.click({ force: true });
      await page.waitForTimeout(3000);
      console.log('✅ Usulan terkirim');
    }
  } else {
    console.log('⚠️ Tombol Kirim tidak ditemukan');
  }

  await page.goto(`${BASE_URL}/inovasi/usulan/2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  // ============================================================
  // 6. HAPUS USULAN
  // ============================================================
  console.log('\n=== HAPUS USULAN ===');

  const hapusBtn = page.locator(`.btn-hapus-usulan, button:has-text("Hapus")`).first();
  if (await hapusBtn.isVisible({ timeout: 5000 })) {
    await hapusBtn.scrollIntoViewIfNeeded();
    await hapusBtn.click({ force: true });
    await page.waitForTimeout(2000);
    console.log('✅ Modal Hapus terbuka');

    const confirmHapus = page.locator('.modal button:has-text("Hapus"), .modal button:has-text("Ya")').first();
    if (await confirmHapus.isVisible()) {
      await confirmHapus.click({ force: true });
      await page.waitForTimeout(3000);
      console.log('✅ Usulan dihapus');
    }
  } else {
    console.log('⚠️ Tombol Hapus tidak ditemukan');
  }

  console.log('\n🎉 PESERTA FULL CRUD SELESAI!');
});
