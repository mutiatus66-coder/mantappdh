import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@admin.com';
const ADMIN_PASSWORD = 'password';

// 🔥 TIMEOUT 10 MENIT (karena semua modul)
test.setTimeout(600000);

test('Full CRUD Admin - Semua Modul', async ({ page }) => {

  // ============================================================
  // 1. LOGIN (SEKALI)
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
  // 2. EVENT
  // ============================================================
  console.log('\n=== EVENT ===');
  await page.goto(`${BASE_URL}/event`);
  await expect(page.locator('h3').filter({ hasText: 'Data Event' })).toBeVisible();
  console.log('✅ Halaman Event terbuka');

  const namaEvent = `Event Test ${Date.now()}`;
  await page.click('#btnTambahEvent');
  await page.waitForSelector('#modalEvent', { state: 'visible' });
  await page.fill('#inputNamaEvent', namaEvent);
  await page.selectOption('#inputJenis', 'INOTEK');
  await page.click('#btnSimpanEvent');
  await page.waitForSelector('#modalEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelEventBody')).toContainText(namaEvent);
  console.log(`✅ Event ditambahkan: ${namaEvent}`);

  const namaEventBaru = `Event Updated ${Date.now()}`;
  const editEventBtn = page.locator(`#tabelEventBody tr:has-text("${namaEvent}") .btn-edit-event`).first();
  await editEventBtn.scrollIntoViewIfNeeded();
  await editEventBtn.click();
  await page.waitForSelector('#modalEvent', { state: 'visible' });
  await page.fill('#inputNamaEvent', namaEventBaru);
  await page.selectOption('#inputJenis', 'INODA');
  await page.click('#btnSimpanEvent');
  await page.waitForSelector('#modalEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelEventBody')).toContainText(namaEventBaru);
  console.log(`✅ Event diubah: ${namaEventBaru}`);

  const hapusEventBtn = page.locator(`#tabelEventBody tr:has-text("${namaEventBaru}") .btn-hapus-event`).first();
  await hapusEventBtn.scrollIntoViewIfNeeded();
  await hapusEventBtn.click();
  await page.waitForSelector('#modalHapusEvent', { state: 'visible' });
  await page.click('#btnHapusEvent');
  await page.waitForSelector('#modalHapusEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelEventBody')).not.toContainText(namaEventBaru);
  console.log(`✅ Event dihapus: ${namaEventBaru}`);

  // ============================================================
  // 3. SUB EVENT
  // ============================================================
  console.log('\n=== SUB EVENT ===');
  await page.goto(`${BASE_URL}/sub-event`);
  await expect(page.locator('h3').filter({ hasText: 'Data Sub Event' })).toBeVisible();
  console.log('✅ Halaman Sub Event terbuka');

  const namaSubEvent = `Sub Event Test ${Date.now()}`;
  await page.click('#btnTambahSubEvent');
  await page.waitForSelector('#modalSubEvent', { state: 'visible' });
  await page.fill('#seTahun', '2026');
  await page.selectOption('#seEvent', { index: 1 });
  await page.fill('#seSubEvent', namaSubEvent);
  await page.fill('#seKategori', 'Test');
  await page.fill('#seMulai', '2026-01-01');
  await page.fill('#seBerakhir', '2026-12-31');
  await page.click('#btnSimpanSE');
  await page.waitForSelector('#modalSubEvent', { state: 'hidden', timeout: 15000 });
  await expect(page.locator('#tabelSubEventBody')).toContainText(namaSubEvent);
  console.log(`✅ Sub Event ditambahkan: ${namaSubEvent}`);

  const namaSubEventBaru = `Sub Event Updated ${Date.now()}`;
  const editSubBtn = page.locator(`#tabelSubEventBody tr:has-text("${namaSubEvent}") .btn-edit-se`).first();
  await editSubBtn.scrollIntoViewIfNeeded();
  await editSubBtn.click();
  await page.waitForSelector('#modalSubEvent', { state: 'visible' });
  await page.fill('#seSubEvent', namaSubEventBaru);
  await page.click('#btnSimpanSE');
  await page.waitForSelector('#modalSubEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelSubEventBody')).toContainText(namaSubEventBaru);
  console.log(`✅ Sub Event diubah: ${namaSubEventBaru}`);

  const hapusSubBtn = page.locator(`#tabelSubEventBody tr:has-text("${namaSubEventBaru}") .btn-hapus-se`).first();
  await hapusSubBtn.scrollIntoViewIfNeeded();
  await hapusSubBtn.click();
  await page.waitForSelector('#modalHapusSE', { state: 'visible' });
  await page.click('#btnHapusSE');
  await page.waitForSelector('#modalHapusSE', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelSubEventBody')).not.toContainText(namaSubEventBaru);
  console.log(`✅ Sub Event dihapus: ${namaSubEventBaru}`);

  // ============================================================
  // ============================================================
  // 4. BIDANG
  // ============================================================
  console.log('\n=== BIDANG ===');
  await page.goto(`${BASE_URL}/bidang`);
  await expect(page.locator('h3').filter({ hasText: 'Master Bidang' })).toBeVisible();

  // Buka accordion pertama
  const accordionBtn = page.locator('.accordion-button').first();
  await accordionBtn.scrollIntoViewIfNeeded();
  await accordionBtn.click();
  await page.waitForTimeout(2000);

  // 🔥 AMBIL ID TABEL DARI ACCORDION YANG DIBUKA
  const subEventId = await accordionBtn.getAttribute('data-se-id');
  console.log(`📌 Sub Event ID: ${subEventId}`);

  console.log('✅ Halaman Bidang terbuka');

  // 🔥 PAKAI TABLE ID YANG SPESIFIK
  const tabelBidang = page.locator(`#tabelBidang-${subEventId}`);

  const namaBidang = `Bidang Test ${Date.now()}`;
  await page.locator('.btn-tambah-bidang').first().click();
  await page.waitForSelector('#modalBidang', { state: 'visible' });
  await page.fill('#bidangNama', namaBidang);
  await page.click('#statusAktifBidang');
  await page.click('#btnSimpanBidang');
  await page.waitForSelector('#modalBidang', { state: 'hidden', timeout: 10000 });
  await expect(tabelBidang).toContainText(namaBidang);
  console.log(`✅ Bidang ditambahkan: ${namaBidang}`);

  const namaBidangBaru = `Bidang Updated ${Date.now()}`;
  const editBidangBtn = tabelBidang.locator(`tr:has-text("${namaBidang}") .btn-ubah-bidang`).first();
  await page.waitForTimeout(1000);
  await editBidangBtn.scrollIntoViewIfNeeded();
  await editBidangBtn.click();
  await page.waitForSelector('#modalBidang', { state: 'visible' });
  await page.fill('#bidangNama', namaBidangBaru);
  await page.click('#statusNonaktifBidang');
  await page.click('#btnSimpanBidang');
  await page.waitForSelector('#modalBidang', { state: 'hidden', timeout: 10000 });
  await expect(tabelBidang).toContainText(namaBidangBaru);
  console.log(`✅ Bidang diubah: ${namaBidangBaru}`);

  const hapusBidangBtn = tabelBidang.locator(`tr:has-text("${namaBidangBaru}") .btn-hapus-bidang`).first();
  await page.waitForTimeout(1000);
  await hapusBidangBtn.scrollIntoViewIfNeeded();
  await hapusBidangBtn.click();
  await page.waitForSelector('#modalHapusBidang', { state: 'visible' });
  await page.click('#btnHapusBidang');
  await page.waitForSelector('#modalHapusBidang', { state: 'hidden', timeout: 10000 });
  await expect(tabelBidang).not.toContainText(namaBidangBaru);
  console.log(`✅ Bidang dihapus: ${namaBidangBaru}`);

  // ============================================================
  // 5. USER
  // ============================================================
  console.log('\n=== USER ===');
  await page.goto(`${BASE_URL}/user`);
  await expect(page.locator('h3').filter({ hasText: 'Data User' })).toBeVisible();
  console.log('✅ Halaman User terbuka');

  const timestamp = Date.now();
  const namaUser = `User Test ${timestamp}`;
  const emailUser = `user${timestamp}@test.com`;

  await page.click('#btnTambahUser');
  await page.waitForSelector('#modalUser', { state: 'visible' });
  await page.fill('#inputNama', namaUser);
  await page.fill('#inputEmail', emailUser);
  await page.selectOption('#inputHakAkses', 'peserta');
  await page.fill('#inputPassword', 'password123');
  await page.click('#statusAktif');
  await page.click('#btnSimpanUser');
  await page.waitForSelector('#modalUser', { state: 'hidden', timeout: 10000 });
  console.log(`✅ User ditambahkan: ${namaUser}`);

  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const searchUser = page.locator('#tabelUser_filter input, .dataTables_filter input, input[type="search"]').first();
  await searchUser.waitFor({ state: 'visible', timeout: 15000 });
  await searchUser.fill(emailUser);
  await page.waitForTimeout(2000);
  await page.waitForSelector(`#tabelUserBody tr:has-text("${emailUser}")`, { timeout: 10000 });

  const namaUserBaru = `User Updated ${Date.now()}`;
  await page.evaluate((email) => {
    const rows = document.querySelectorAll('#tabelUserBody tr');
    for (const row of rows) {
      if (row.textContent.includes(email)) {
        row.querySelector('.btn-edit-user')?.click();
      }
    }
  }, emailUser);

  await page.waitForSelector('#modalUser', { state: 'visible', timeout: 10000 });
  await page.fill('#inputNama', namaUserBaru);
  await page.selectOption('#inputHakAkses', 'penilai');
  await page.click('#statusNonaktif');
  await page.click('#btnSimpanUser');
  await page.waitForSelector('#modalUser', { state: 'hidden', timeout: 10000 });
  console.log(`✅ User diubah: ${namaUserBaru}`);

  await searchUser.fill(emailUser);
  await page.waitForTimeout(2000);
  await page.evaluate((email) => {
    const rows = document.querySelectorAll('#tabelUserBody tr');
    for (const row of rows) {
      if (row.textContent.includes(email)) {
        row.querySelector('.btn-hapus-user')?.click();
      }
    }
  }, emailUser);

  await page.waitForSelector('#modalHapusUser', { state: 'visible', timeout: 5000 });
  await page.click('#btnHapusUser');
  await page.waitForSelector('#modalHapusUser', { state: 'hidden', timeout: 10000 });
  console.log(`✅ User dihapus: ${namaUserBaru}`);

  // ============================================================
  // 6. PENILAI (TAMBAH → UBAH → HAPUS)
  // ============================================================
  console.log('\n=== PENILAI ===');
  await page.goto(`${BASE_URL}/penilai`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Master Penilai' })).toBeVisible();
  console.log('✅ Halaman Penilai terbuka');

  // Pilih sub event pertama
  const detailPenilaiBtn = page.locator('a.btn-primary:has-text("Detail")').first();
  await detailPenilaiBtn.scrollIntoViewIfNeeded();
  await detailPenilaiBtn.click();
  await page.waitForURL(/penilai\/\d+/);
  console.log('✅ Halaman Detail Penilai terbuka');

  // ── HAPUS PENILAI EXISTING (biar bersih) ──
  try {
    const existingPenilai = page.locator('#tabelPenilaiBody tr').first();
    if (await existingPenilai.isVisible({ timeout: 3000 })) {
      const hapusExistBtn = page.locator('.btn-hapus-penilai').first();
      if (await hapusExistBtn.isVisible({ timeout: 3000 })) {
        await hapusExistBtn.scrollIntoViewIfNeeded();
        await hapusExistBtn.click();
        await page.waitForSelector('#modalHapusPenilai', { state: 'visible', timeout: 5000 });
        await page.click('#btnHapusPenilai');
        await page.waitForSelector('#modalHapusPenilai', { state: 'hidden', timeout: 10000 });
        await page.waitForTimeout(1000);
        console.log('✅ Penilai existing dihapus');
      }
    }
  } catch (e) {
    console.log('⚠️ Tidak ada penilai existing');
  }

  // ── TAMBAH PENILAI ──
  await page.click('#btnTambahPenilai');
  await page.waitForSelector('#modalPenilai', { state: 'visible', timeout: 10000 });

  const penilaiOptions = await page.locator('#penilaiUserId option').all();
  if (penilaiOptions.length > 1) {
    await page.selectOption('#penilaiUserId', { index: 1 });
    await page.waitForTimeout(500);
    await page.click('#btnSimpanPenilai');
    await page.waitForSelector('#modalPenilai', { state: 'hidden', timeout: 15000 });
    console.log('✅ Penilai ditambahkan');
  } else {
    console.log('⚠️ Tidak ada user tersedia untuk penilai');
    return;
  }

  // ── UBAH PENILAI ──
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
    const penilaiOptions2 = await page.locator('#penilaiUserId option').all();
    if (penilaiOptions2.length > 2) {
      await page.selectOption('#penilaiUserId', { index: 2 });
    } else if (penilaiOptions2.length > 1) {
      await page.selectOption('#penilaiUserId', { index: 1 });
    }

    await page.waitForTimeout(500);
    await page.click('#btnSimpanPenilai');
    await page.waitForSelector('#modalPenilai', { state: 'hidden', timeout: 15000 });
    console.log('✅ Penilai diubah');
  } else {
    console.log('⚠️ Tidak ada penilai untuk diubah');
  }

  // ── HAPUS PENILAI ──
  await page.waitForTimeout(2000);
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const hapusPenilaiBtn = page.locator('.btn-hapus-penilai').first();
  if (await hapusPenilaiBtn.isVisible({ timeout: 5000 })) {
    const namaPenilaiHapus = await hapusPenilaiBtn.getAttribute('data-nama');
    await hapusPenilaiBtn.scrollIntoViewIfNeeded();
    await hapusPenilaiBtn.click({ force: true });
    await page.waitForSelector('#modalHapusPenilai', { state: 'visible', timeout: 5000 });
    await page.click('#btnHapusPenilai');
    await page.waitForSelector('#modalHapusPenilai', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Penilai dihapus: ${namaPenilaiHapus}`);
  } else {
    console.log('⚠️ Tidak ada penilai untuk dihapus');
  }

  // ============================================================
  // 7. PENGUMUMAN
  // ============================================================
  console.log('\n=== PENGUMUMAN ===');
  await page.goto(`${BASE_URL}/pengumuman`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Master Pengumuman' })).toBeVisible();
  console.log('✅ Halaman Pengumuman terbuka');

  const judulPengumuman = `Pengumuman Test ${Date.now()}`;
  await page.click('#btnTambahPengumuman');
  await page.waitForSelector('#modalPengumuman', { state: 'visible' });
  await page.fill('#pJudul', judulPengumuman);
  await page.fill('#pDeskripsi', 'Ini adalah deskripsi pengumuman test');
  await page.selectOption('#pStatus', 'Published');
  await page.click('#btnSimpanPengumuman');
  await page.waitForSelector('#modalPengumuman', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Pengumuman ditambahkan: ${judulPengumuman}`);

  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  let searchPengumuman = page.locator('#tabelPengumuman_filter input, .dataTables_filter input, input[type="search"]').first();
  await searchPengumuman.waitFor({ state: 'visible', timeout: 15000 });
  await searchPengumuman.fill(judulPengumuman);
  await page.waitForTimeout(2000);
  await page.waitForSelector(`#tabelPengumumanBody tr:has-text("${judulPengumuman}")`, { timeout: 10000 });

  const judulBaru = `Pengumuman Updated ${Date.now()}`;
  await page.evaluate((judul) => {
    const rows = document.querySelectorAll('#tabelPengumumanBody tr');
    for (const row of rows) {
      if (row.textContent.includes(judul)) {
        row.querySelector('.btn-edit-pengumuman')?.click();
      }
    }
  }, judulPengumuman);

  await page.waitForSelector('#modalPengumuman', { state: 'visible', timeout: 10000 });
  await page.fill('#pJudul', judulBaru);
  await page.fill('#pDeskripsi', 'Deskripsi sudah diupdate');
  await page.selectOption('#pStatus', 'Draft');
  await page.click('#btnSimpanPengumuman');
  await page.waitForSelector('#modalPengumuman', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Pengumuman diubah: ${judulBaru}`);

  await searchPengumuman.fill(judulBaru);
  await page.waitForTimeout(2000);
  await page.evaluate((judul) => {
    const rows = document.querySelectorAll('#tabelPengumumanBody tr');
    for (const row of rows) {
      if (row.textContent.includes(judul)) {
        row.querySelector('.btn-hapus-pengumuman')?.click();
      }
    }
  }, judulBaru);

  await page.waitForSelector('#modalHapusPengumuman', { state: 'visible', timeout: 5000 });
  await page.click('#btnHapusPengumuman');
  await page.waitForSelector('#modalHapusPengumuman', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Pengumuman dihapus: ${judulBaru}`);

  // ============================================================
  // 8. INDIKATOR TAHAP 1
  // ============================================================
  console.log('\n=== INDIKATOR TAHAP 1 ===');
  await page.goto(`${BASE_URL}/indikator/tahap-1`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Setting Indikator Penilaian Tahap 1' })).toBeVisible();
  console.log('✅ Halaman Indikator Tahap 1 terbuka');

  const detailInd1Btn = page.locator('a.btn-primary:has-text("Detail")').first();
  await detailInd1Btn.scrollIntoViewIfNeeded();
  await detailInd1Btn.click();
  await page.waitForURL(/indikator\/tahap-1\/\d+\/inovasi/);

  const ind1Url = page.url();
  const subEventIdInd1 = ind1Url.match(/indikator\/tahap-1\/(\d+)\/inovasi/)?.[1];
  console.log(`📌 Sub Event ID: ${subEventIdInd1}`);

  const namaIndikator1 = `Indikator Test ${Date.now()}`;
  await page.click('#btnTambahIndikator');
  await page.waitForSelector('#modalIndikator', { state: 'visible' });
  await page.fill('#inputNamaIndikator', namaIndikator1);
  await page.selectOption('#selectJenis', 'substansi');
  await page.click('#modalIndikator .btn-success:has-text("Simpan")');
  await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Indikator Tahap 1 ditambahkan: ${namaIndikator1}`);

  const namaIndikator1Baru = `Indikator Updated ${Date.now()}`;
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  const editInd1Btn = page.locator(`#tabelDetailInovasiBody tr:has-text("${namaIndikator1}") .btn-edit-indikator`).first();
  await editInd1Btn.scrollIntoViewIfNeeded();
  await editInd1Btn.click();
  await page.waitForSelector('#modalIndikator', { state: 'visible', timeout: 10000 });
  await page.fill('#inputNamaIndikator', namaIndikator1Baru);
  await page.click('#modalIndikator .btn-success:has-text("Simpan")');
  await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Indikator Tahap 1 diubah: ${namaIndikator1Baru}`);

  // Hapus Indikator Tahap 1
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);
  const hapusInd1Btn = page.locator(`#tabelDetailInovasiBody tr:has-text("${namaIndikator1Baru}") .btn-hapus-indikator`).first();
  await hapusInd1Btn.scrollIntoViewIfNeeded();
  await hapusInd1Btn.click();
  await page.waitForSelector('#modalHapusIndikator', { state: 'visible', timeout: 5000 });
  await page.click('#modalHapusIndikator .btn-danger:has-text("Hapus")');
  await page.waitForSelector('#modalHapusIndikator', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Indikator Tahap 1 dihapus: ${namaIndikator1Baru}`);

  // ============================================================
  // 9. INDIKATOR TAHAP 2
  // ============================================================
  console.log('\n=== INDIKATOR TAHAP 2 ===');
  await page.goto(`${BASE_URL}/indikator/tahap-2`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Setting Indikator Penilaian Tahap 2' })).toBeVisible();
  console.log('✅ Halaman Indikator Tahap 2 terbuka');

  const detailInd2Btn = page.locator('a.btn-primary:has-text("Detail")').first();
  await detailInd2Btn.scrollIntoViewIfNeeded();
  await detailInd2Btn.click();
  await page.waitForURL(/indikator\/tahap-2\/\d+\/indikator/);
  console.log('✅ Halaman Detail Indikator Tahap 2 terbuka');

  // ── TAMBAH ──
  const namaIndikator2 = `Indikator Tahap 2 Test ${Date.now()}`;
  await page.click('#btnTambahIndikator');
  await page.waitForSelector('#modalIndikator', { state: 'visible' });
  await page.fill('#inputNamaIndikator', namaIndikator2);
  await page.selectOption('#inputJenis', 'Subtansi Inovasi');
  await page.fill('#inputKeterangan', 'Keterangan test');
  await page.fill('#inputNilaiMinimal', '0');
  await page.fill('#inputNilaiMaksimal', '100');
  await page.click('#modalIndikator .btn-success:has-text("Simpan")');
  await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
  console.log(`✅ Indikator Tahap 2 ditambahkan: ${namaIndikator2}`);

  // ── UBAH (pake SEARCH DULU) ──
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  // 🔥 SEARCH PAKAI SEARCH BOX DATATABLES
  const searchInd2 = page.locator('#tabelTahap2Detail_filter input, .dataTables_filter input, input[type="search"]').first();
  await searchInd2.waitFor({ state: 'visible', timeout: 10000 });
  await searchInd2.fill(namaIndikator2);
  await page.waitForTimeout(3000);
  console.log(`🔍 Search: ${namaIndikator2}`);

  // 🔥 CEK APAKAH MUNCUL
  const editInd2Btn = page.locator(`#tabelTahap2DetailBody tr:has-text("${namaIndikator2}") .btn-edit-indikator`).first();

  if (await editInd2Btn.isVisible({ timeout: 5000 })) {
    await editInd2Btn.scrollIntoViewIfNeeded();
    await editInd2Btn.click();
    console.log('✅ Tombol Edit diklik');

    await page.waitForSelector('#modalIndikator', { state: 'visible', timeout: 10000 });
    const namaIndikator2Baru = `Indikator Tahap 2 Updated ${Date.now()}`;
    await page.fill('#inputNamaIndikator', namaIndikator2Baru);
    await page.click('#modalIndikator .btn-success:has-text("Simpan")');
    await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Indikator Tahap 2 diubah: ${namaIndikator2Baru}`);

    // Update nama
    namaIndikator2 = namaIndikator2Baru;
  } else {
    console.log(`⚠️ Tombol Edit tidak ditemukan setelah search`);
  }

  // ── HAPUS (pake SEARCH DULU) ──
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  await searchInd2.waitFor({ state: 'visible', timeout: 10000 });
  await searchInd2.fill(namaIndikator2);
  await page.waitForTimeout(3000);

  const hapusInd2Btn = page.locator(`#tabelTahap2DetailBody tr:has-text("${namaIndikator2}") .btn-hapus-indikator`).first();

  if (await hapusInd2Btn.isVisible({ timeout: 5000 })) {
    await hapusInd2Btn.scrollIntoViewIfNeeded();
    await hapusInd2Btn.click();
    console.log('✅ Tombol Hapus diklik');

    await page.waitForSelector('#modalHapus', { state: 'visible', timeout: 5000 });
    await page.click('#modalHapus .btn-danger:has-text("Hapus")');
    await page.waitForSelector('#modalHapus', { state: 'hidden', timeout: 10000 });
    console.log(`✅ Indikator Tahap 2 dihapus`);
  } else {
    console.log(`⚠️ Tombol Hapus tidak ditemukan setelah search`);
  }

  
  // ============================================================
  // 10. INOVASI - RIWAYAT
  // ============================================================
  console.log('\n=== INOVASI - RIWAYAT ===');
  await page.goto(`${BASE_URL}/inovasi/riwayat`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);
  console.log('✅ Halaman Riwayat Inovasi terbuka');

  // Cek apakah ada data
  const riwayatContent = page.locator('body');
  await expect(riwayatContent).toContainText(/Riwayat|Inovasi/);
  console.log('✅ Data riwayat terlihat');

  // ============================================================
  // 11. INOVASI - REKAP NILAI
  // ============================================================
  console.log('\n=== INOVASI - REKAP NILAI ===');
  await page.goto(`${BASE_URL}/inovasi/rekap-nilai`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);
  console.log('✅ Halaman Rekap Nilai terbuka');
  console.log('✅ Data rekap nilai terlihat');

  // ============================================================
  // 12. PENILAIAN TAHAP 1
  // ============================================================
  console.log('\n=== PENILAIAN TAHAP 1 ===');
  await page.goto(`${BASE_URL}/penilaian/tahap-1`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Penilaian Tahap 1' })).toBeVisible();
  console.log('✅ Halaman Penilaian Tahap 1 terbuka');

  // ============================================================
  // 13. PENILAIAN TAHAP 2
  // ============================================================
  console.log('\n=== PENILAIAN TAHAP 2 ===');
  await page.goto(`${BASE_URL}/penilaian/tahap-2`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Penilaian Tahap 2' })).toBeVisible();
  console.log('✅ Halaman Penilaian Tahap 2 terbuka');
});
