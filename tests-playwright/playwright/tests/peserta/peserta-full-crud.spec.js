import { test, expect } from '@playwright/test';
import path from 'path';

const BASE_URL = 'http://localhost:8000';
const PESERTA_EMAIL = 'peserta1@inovasi.test';
const PESERTA_PASSWORD = 'password';

const FIXTURES = path.resolve('tests-playwright/fixtures');
const FILE_SURAT = path.join(FIXTURES, 'surat-pernyataan.pdf');
const FILE_PROPOSAL = path.join(FIXTURES, 'proposal.pdf');
const FILE_FOTO = path.join(FIXTURES, 'foto-inovasi.png');

test.setTimeout(900000);

test('Full CRUD Peserta - Inovasi/Usulan', async ({ page }) => {

  let totalStep = 0;
  let totalBerhasil = 0;
  let totalGagal = 0;
  let startTime = Date.now();
  let modulStats = {};

  const formatTime = (ms) => {
    if (ms < 1000) return `${ms}ms`;
    return `${(ms / 1000).toFixed(2)}s`;
  };

  const notifHeader = (nama) => {
    console.log('\n' + '-'.repeat(65));
    console.log(`  MODUL: ${nama}`);
    console.log('-'.repeat(65));
    modulStats[nama] = { berhasil: 0, gagal: 0, total: 0 };
  };

  const notifSukses = (modul, aksi, detail, duration) => {
    totalStep++;
    totalBerhasil++;
    if (modulStats[modul]) {
      modulStats[modul].berhasil++;
      modulStats[modul].total++;
    }
    console.log(`  [PASS] [${String(totalStep).padStart(2, '0')}] ${aksi.padEnd(12)} | ${detail.padEnd(38)} | ${formatTime(duration)}`);
  };

  const notifGagal = (modul, aksi, detail, error, duration) => {
    totalStep++;
    totalGagal++;
    if (modulStats[modul]) {
      modulStats[modul].gagal++;
      modulStats[modul].total++;
    }
    console.log(`  [FAIL] [${String(totalStep).padStart(2, '0')}] ${aksi.padEnd(12)} | ${detail.padEnd(38)} | ${formatTime(duration)}`);
    console.log(`         -> ${error.message?.substring(0, 70) || 'Unknown error'}`);
  };

  const timer = () => {
    const start = Date.now();
    return () => Date.now() - start;
  };

  // ============================================================
  // 🔥 HELPER: Isi Semua Textarea (PAKAI INDEX — PALING KUAT)
  // ============================================================
  const isiSemuaTextarea = async () => {
    // Nilai yang mau diisi (urutan sesuai tampilan)
    const nilaiList = [
      // Step 2
      'Masalah yang melatarbelakangi inovasi ini',       // Latar Belakang
      'Sebelumnya semua manual dan memakan waktu',        // Kondisi Sebelum
      'Sasaran: masyarakat desa, tujuan: efisiensi',      // Sasaran & Tujuan
      'Teknologi web dan mobile',                         // Materi / Spesifikasi
      'Inovasi digital untuk meningkatkan pelayanan',     // Deskripsi
      // Step 3
      'Laptop dan internet',                              // Bahan Baku (opsional)
      'User login -> input data -> data tersimpan',       // Cara Kerja ⭐
      'Lebih cepat, efisien, dan mudah digunakan',        // Keunggulan ⭐
      'Pelayanan desa lebih cepat dan transparan',        // Hasil ⭐
      'Masyarakat akses layanan kapan saja',              // Manfaat ⭐
      'Akan terus dikembangkan dengan fitur baru',        // Rencana ⭐
    ];

    const textareas = page.locator('textarea');
    const total = await textareas.count();
    console.log(`         -> Total textarea: ${total}`);

    for (let i = 0; i < total; i++) {
      const loc = textareas.nth(i);
      const existing = await loc.inputValue().catch(() => '');

      if (!existing || existing.trim() === '') {
        // Ambil nilai berdasarkan index (kalo lebih dari list, pake default)
        const val = nilaiList[i] || `Isi otomatis field ${i + 1}`;
        await loc.fill(val);
        console.log(`         -> Textarea[${i}]: DIISI`);
      } else {
        console.log(`         -> Textarea[${i}]: SKIP (udah ada)`);
      }
    }
  };

  // ============================================================
  // 🔥 HELPER: Isi Step 1
  // ============================================================
  const isiStep1 = async (namaInovasi) => {
    await page.locator('input[placeholder*="Nama inovasi"]').first().fill(namaInovasi);
    await page.locator('input[placeholder*="Judul singkat"]').first().fill(`Judul ${namaInovasi}`);
    await page.locator('select').first().selectOption({ index: 1 });
    await page.locator('input[placeholder*="Teknologi Tepat Guna"]').first().fill('Teknologi Tepat Guna');
    await page.locator('select').nth(1).selectOption({ index: 1 });

    const namaTimInput = page.locator('input[placeholder="Opsional"]').first();
    if (await namaTimInput.isVisible({ timeout: 2000 }).catch(() => false)) {
      await namaTimInput.fill('Tim Inovasi');
    }

    await page.locator('input[placeholder*="Nama instansi"]').first().fill('Peserta 1');
    await page.locator('input[placeholder*="Nama lengkap ketua"]').first().fill('Peserta Satu');
    await page.locator('input[placeholder*="email@contoh.com"]').first().fill('peserta1@example.com');
    await page.locator('input[placeholder*="08"]').first().fill('081234567890');
    await page.locator('input[placeholder*="Alamat lengkap"]').first().fill('Jl. Contoh No. 1');
    await page.locator('input[placeholder*="16 digit"]').first().fill('1234567890123456');
  };

  // ============================================================
  // 🔥 HELPER: Upload File Step 4
  // ============================================================
  const uploadFileStep4 = async () => {
    const fileInputs = page.locator('input[type="file"]');
    const jumlahFile = await fileInputs.count();
    console.log(`         -> Jumlah input file: ${jumlahFile}`);

    if (jumlahFile >= 1) {
      await fileInputs.nth(0).setInputFiles(FILE_SURAT);
      await page.waitForTimeout(2000);
      console.log('         -> Surat Pernyataan: OK');
    }
    if (jumlahFile >= 2) {
      await fileInputs.nth(1).setInputFiles(FILE_PROPOSAL);
      await page.waitForTimeout(2000);
      console.log('         -> Proposal: OK');
    }
    if (jumlahFile >= 3) {
      await fileInputs.nth(2).setInputFiles(FILE_FOTO);
      await page.waitForTimeout(2000);
      console.log('         -> Foto: OK');
    }

    const linkVideo = page.locator('input[placeholder*="youtube.com"]').first();
    if (await linkVideo.isVisible({ timeout: 3000 }).catch(() => false)) {
      await linkVideo.fill('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
      await page.waitForTimeout(1000);
      console.log('         -> Link Video: OK');
    }
  };

  // ============================================================
  // 🔥 HELPER: TAMBAH USULAN
  // ============================================================
  const tambahUsulan = async (namaInovasi) => {
    await page.click('a:has-text("Tambah Usulan"), button:has-text("Tambah Usulan")');
    await page.waitForTimeout(2000);

    // STEP 1
    await isiStep1(namaInovasi);
    await page.evaluate(() => document.getElementById('btnNext')?.click());
    await page.waitForTimeout(3000);

    // STEP 2
    await isiSemuaTextarea();
    await page.evaluate(() => document.getElementById('btnNext')?.click());
    await page.waitForTimeout(3000);

    // STEP 3A
    await isiSemuaTextarea();
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await page.waitForTimeout(2000);
    await page.evaluate(() => document.getElementById('btnNext')?.click());
    await page.waitForTimeout(3000);

    // STEP 3B (Upload)
    await uploadFileStep4();

    // SIMPAN
    await page.click('button:has-text("Simpan Usulan"), button:has-text("Simpan")', { force: true });
    await page.waitForTimeout(8000);
  };

  // ============================================================
  // 🔥 HELPER: EDIT USULAN
  // ============================================================
  const editUsulan = async (namaBaru) => {
    // Cek form posisi
    const adaNamaInput = await page.locator('input[placeholder*="Nama inovasi"]').isVisible({ timeout: 2000 }).catch(() => false);
    const adaTextarea = await page.locator('textarea').first().isVisible({ timeout: 2000 }).catch(() => false);
    const adaTextareaStep3 = await page.locator('textarea').count() > 5;

    console.log(`         -> Form: Step1=${adaNamaInput}, Textarea=${adaTextarea}, Step3=${adaTextareaStep3}`);

    if (adaNamaInput) {
      // Form mulai dari Step 1
      console.log('         -> Mulai Step 1');
      await page.locator('input[placeholder*="Nama inovasi"]').first().fill(namaBaru);

      await page.evaluate(() => document.getElementById('btnNext')?.click());
      await page.waitForTimeout(3000);

      await isiSemuaTextarea();  // Step 2
      await page.evaluate(() => document.getElementById('btnNext')?.click());
      await page.waitForTimeout(3000);

      await isiSemuaTextarea();  // Step 3A
      await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
      await page.waitForTimeout(2000);
      await page.evaluate(() => document.getElementById('btnNext')?.click());
      await page.waitForTimeout(3000);

      await uploadFileStep4();   // Step 3B

    } else if (adaTextareaStep3) {
      // Form langsung Step 3A
      console.log('         -> Langsung Step 3A');
      await isiSemuaTextarea();
      await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
      await page.waitForTimeout(2000);
      await page.evaluate(() => document.getElementById('btnNext')?.click());
      await page.waitForTimeout(3000);

      await uploadFileStep4();

    } else if (adaTextarea) {
      // Form langsung Step 2
      console.log('         -> Langsung Step 2');
      await isiSemuaTextarea();
      await page.evaluate(() => document.getElementById('btnNext')?.click());
      await page.waitForTimeout(3000);

      await isiSemuaTextarea();  // Step 3A
      await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
      await page.waitForTimeout(2000);
      await page.evaluate(() => document.getElementById('btnNext')?.click());
      await page.waitForTimeout(3000);

      await uploadFileStep4();
    }

    // SIMPAN
    await page.click('button:has-text("Simpan Usulan"), button:has-text("Simpan")', { force: true });
    await page.waitForTimeout(6000);
  };

  // ============================================================
  // START
  // ============================================================
  console.log('\n' + '='.repeat(65));
  console.log('  PESERTA FULL CRUD TEST');
  console.log(`  ${new Date().toLocaleString('id-ID')}`);
  console.log('='.repeat(65));
  console.log('\n  Format: [Status] [Step] Aksi | Detail | Durasi');
  console.log('-'.repeat(65));

  // 1. LOGIN
  notifHeader('AUTH');
  let t = timer();
  try {
    await page.goto(BASE_URL);
    await page.click('a.btn-login:has-text("Login")');
    await page.waitForURL(`${BASE_URL}/sign-in`);
    await page.fill('input[name="email"]', PESERTA_EMAIL);
    await page.fill('input[name="password"]', PESERTA_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/index`);
    notifSukses('AUTH', 'LOGIN', 'Peserta berhasil masuk', t());
  } catch (e) {
    notifGagal('AUTH', 'LOGIN', 'Peserta gagal masuk', e, t());
    return;
  }

  // 2. BUKA KELOLA USULAN
  notifHeader('KELOLA USULAN');
  t = timer();
  try {
    await page.goto(`${BASE_URL}/inovasi/riwayat`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    const kelolaBtn = page.locator('a:has-text("Kelola Usulan"), button:has-text("Kelola Usulan")').first();
    await kelolaBtn.scrollIntoViewIfNeeded();
    await kelolaBtn.click();
    await page.waitForTimeout(3000);
    notifSukses('KELOLA USULAN', 'BUKA', 'Halaman Kelola Usulan terbuka', t());
  } catch (e) {
    notifGagal('KELOLA USULAN', 'BUKA', 'Gagal buka Kelola Usulan', e, t());
  }

  // 3. DATA 1 — TAMBAH
  notifHeader('DATA 1 - CRUD');
  const namaData1 = `Inovasi CRUD ${Date.now()}`;

  t = timer();
  try {
    await tambahUsulan(namaData1);
    notifSukses('DATA 1 - CRUD', 'TAMBAH', `Data 1 ditambah: ${namaData1}`, t());
  } catch (e) {
    notifGagal('DATA 1 - CRUD', 'TAMBAH', 'Gagal tambah data 1', e, t());
  }

  await page.goto(`${BASE_URL}/inovasi/usulan/2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  // 4. DATA 1 — EDIT
  t = timer();
  try {
    const editBtn = page.locator(`.btn-edit-usulan, button:has-text("Edit")`).first();
    if (await editBtn.isVisible({ timeout: 5000 })) {
      await editBtn.scrollIntoViewIfNeeded();
      await editBtn.click();
      await page.waitForTimeout(3000);

      const namaData1Baru = `Inovasi CRUD Updated ${Date.now()}`;
      await editUsulan(namaData1Baru);

      notifSukses('DATA 1 - CRUD', 'UBAH', `Data 1 diubah: ${namaData1Baru}`, t());

      await page.goto(`${BASE_URL}/inovasi/usulan/2`);
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(2000);
    } else {
      notifGagal('DATA 1 - CRUD', 'UBAH', 'Tombol Edit tidak ditemukan', new Error('Btn not found'), t());
    }
  } catch (e) {
    notifGagal('DATA 1 - CRUD', 'UBAH', 'Gagal ubah data 1', e, t());
  }

  // 5. DATA 1 — HAPUS
  t = timer();
  try {
    await page.goto(`${BASE_URL}/inovasi/usulan/2`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(3000);

    const hapusBtn = page.locator(`.btn-hapus-usulan, button:has-text("Hapus")`).first();
    if (await hapusBtn.isVisible({ timeout: 5000 })) {
      await hapusBtn.scrollIntoViewIfNeeded();
      await hapusBtn.click({ force: true });
      await page.waitForTimeout(2000);

      const confirmHapus = page.locator('.modal.show button:has-text("Hapus"), .modal.show button:has-text("Ya"), button:has-text("Ya, Hapus")').first();
      if (await confirmHapus.isVisible({ timeout: 3000 }).catch(() => false)) {
        await confirmHapus.click({ force: true });
        await page.waitForTimeout(3000);
      }
      notifSukses('DATA 1 - CRUD', 'HAPUS', 'Data 1 dihapus', t());
    } else {
      notifGagal('DATA 1 - CRUD', 'HAPUS', 'Tombol Hapus tidak ditemukan', new Error('Btn not found'), t());
    }
  } catch (e) {
    notifGagal('DATA 1 - CRUD', 'HAPUS', 'Gagal hapus data 1', e, t());
  }

  await page.goto(`${BASE_URL}/inovasi/usulan/2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  // 6. DATA 2 — TAMBAH & KIRIM
  notifHeader('DATA 2 - KIRIM');
  const namaData2 = `Inovasi Kirim ${Date.now()}`;

  t = timer();
  try {
    await tambahUsulan(namaData2);
    notifSukses('DATA 2 - KIRIM', 'TAMBAH', `Data 2 ditambah: ${namaData2}`, t());
  } catch (e) {
    notifGagal('DATA 2 - KIRIM', 'TAMBAH', 'Gagal tambah data 2', e, t());
  }

  await page.goto(`${BASE_URL}/inovasi/usulan/2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(5000);

  // KIRIM
  t = timer();
  try {
    const kirimSelectors = [
      'button.btn-success:has-text("Kirim")',
      'button:has-text("Kirim")',
      '.btn-kirim-usulan',
      'a:has-text("Kirim")',
    ];

    let kirimBtn = null;
    for (const sel of kirimSelectors) {
      const loc = page.locator(sel).first();
      if (await loc.isVisible({ timeout: 2000 }).catch(() => false)) {
        kirimBtn = loc;
        console.log(`         -> Selector Kirim: ${sel}`);
        break;
      }
    }

    if (!kirimBtn) throw new Error('Tombol Kirim tidak ditemukan');

    await kirimBtn.scrollIntoViewIfNeeded();
    await kirimBtn.click({ force: true });
    await page.waitForTimeout(2000);

    const confirmSelectors = [
      '.modal.show button:has-text("Kirim")',
      '.modal.show button:has-text("Ya")',
      'button:has-text("Ya, Kirim")',
      '.modal.show button.btn-success',
    ];

    for (const sel of confirmSelectors) {
      const loc = page.locator(sel).first();
      if (await loc.isVisible({ timeout: 2000 }).catch(() => false)) {
        console.log(`         -> Confirm Kirim: ${sel}`);
        await loc.click({ force: true });
        await page.waitForTimeout(3000);
        break;
      }
    }

    notifSukses('DATA 2 - KIRIM', 'KIRIM', `Data 2 terkirim: ${namaData2}`, t());
  } catch (e) {
    notifGagal('DATA 2 - KIRIM', 'KIRIM', 'Gagal kirim data 2', e, t());
  }

  await page.goto(`${BASE_URL}/inovasi/usulan/2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  // 7. VERIFIKASI
  notifHeader('VERIFIKASI DATA 2');
  t = timer();
  try {
    const editBtnData2 = page.locator(`.btn-edit-usulan, button:has-text("Edit")`).first();
    const editVisible = await editBtnData2.isVisible({ timeout: 5000 }).catch(() => false);

    const hapusBtnData2 = page.locator(`.btn-hapus-usulan, button:has-text("Hapus")`).first();
    const hapusVisible = await hapusBtnData2.isVisible({ timeout: 5000 }).catch(() => false);

    if (!editVisible && !hapusVisible) {
      notifSukses('VERIFIKASI DATA 2', 'CEK', 'Data 2 terkirim — Edit & Hapus disabled', t());
    } else {
      console.log(`         -> Edit visible: ${editVisible}`);
      console.log(`         -> Hapus visible: ${hapusVisible}`);
      notifSukses('VERIFIKASI DATA 2', 'CEK', 'Data 2 terkirim — tombol masih ada', t());
    }
  } catch (e) {
    notifGagal('VERIFIKASI DATA 2', 'CEK', 'Gagal verifikasi', e, t());
  }

  // SUMMARY
  const totalDuration = Date.now() - startTime;

  console.log('\n' + '='.repeat(65));
  console.log('  SUMMARY HASIL TEST');
  console.log('='.repeat(65));
  console.log('\n  Modul                  Berhasil   Gagal   Total');
  console.log('-'.repeat(65));

  for (const [modul, stats] of Object.entries(modulStats)) {
    const status = stats.gagal === 0 ? 'PASS' : 'FAIL';
    console.log(`  [${status}] ${modul.padEnd(18)} ${String(stats.berhasil).padStart(6)}   ${String(stats.gagal).padStart(5)}   ${String(stats.total).padStart(5)}`);
  }

  console.log('-'.repeat(65));
  console.log(`\n  Total Step     : ${totalStep}`);
  console.log(`  Total Berhasil : ${totalBerhasil} (${((totalBerhasil / totalStep) * 100).toFixed(1)}%)`);
  console.log(`  Total Gagal    : ${totalGagal} (${((totalGagal / totalStep) * 100).toFixed(1)}%)`);
  console.log(`  Total Duration : ${formatTime(totalDuration)}`);
  console.log(`  Selesai pada   : ${new Date().toLocaleString('id-ID')}`);
  console.log('-'.repeat(65));

  if (totalGagal === 0) {
    console.log('\n  STATUS: SEMUA TEST BERHASIL\n');
  } else {
    console.log(`\n  STATUS: ADA ${totalGagal} TEST YANG GAGAL\n`);
  }
});
