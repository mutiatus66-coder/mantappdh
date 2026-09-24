import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@demo.test';
const ADMIN_PASSWORD = 'password';

test.setTimeout(900000);

test('Full CRUD Admin - Semua Modul', async ({ page }) => {

  // ══════════════════════════════════════════════════════════════════════
  // STATISTIK & HELPER GLOBAL
  // ══════════════════════════════════════════════════════════════════════
  let totalStep = 0;
  let totalBerhasil = 0;
  let totalGagal = 0;
  let startTime = Date.now();
  let modulStats = {};

  const formatTime = (ms) => ms < 1000 ? `${ms}ms` : `${(ms / 1000).toFixed(2)}s`;

  const notifHeader = (nama) => {
    console.log('\n' + '-'.repeat(65));
    console.log(`  MODUL: ${nama}`);
    console.log('-'.repeat(65));
    modulStats[nama] = { berhasil: 0, gagal: 0, total: 0 };
  };

  const notifSukses = (modul, aksi, detail, duration) => {
    totalStep++; totalBerhasil++;
    if (modulStats[modul]) { modulStats[modul].berhasil++; modulStats[modul].total++; }
    console.log(`  [PASS] [${String(totalStep).padStart(2, '0')}] ${aksi.padEnd(11)} | ${detail.padEnd(42)} | ${formatTime(duration)}`);
  };

  const notifGagal = (modul, aksi, detail, error, duration) => {
    totalStep++; totalGagal++;
    if (modulStats[modul]) { modulStats[modul].gagal++; modulStats[modul].total++; }
    console.log(`  [FAIL] [${String(totalStep).padStart(2, '0')}] ${aksi.padEnd(11)} | ${detail.padEnd(42)} | ${formatTime(duration)}`);
    console.log(`         -> ${error.message?.substring(0, 80) || 'Unknown error'}`);
  };

  const timer = () => { const s = Date.now(); return () => Date.now() - s; };

  // ── HELPER: Search DataTables v2.x ──
  const getSearchBox = () => page.locator('input[type="search"]:visible').first();

  // ── HELPER: Buka halaman + cek 403 ──
  const bukaHalaman = async (url) => {
    await page.goto(url);
    await page.waitForLoadState('networkidle');
    const body = await page.locator('body').innerText().catch(() => '');
    if (body.includes('403') || body.includes('Akses ditolak')) {
      throw new Error(`403 Forbidden di ${url}`);
    }
  };

  // ══════════════════════════════════════════════════════════════════════
  // START
  // ══════════════════════════════════════════════════════════════════════
  console.log('\n' + '='.repeat(65));
  console.log('  ADMIN FULL CRUD TEST');
  console.log(`  ${new Date().toLocaleString('id-ID')}`);
  console.log('='.repeat(65));

  // ══════════════════════════════════════════════════════════════════════
  // 1. LOGIN
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('AUTH');
  let t = timer();
  try {
    await page.goto(BASE_URL);
    await page.click('a.btn-login:has-text("Login")');
    await page.waitForURL(`${BASE_URL}/sign-in`);
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/index`);
    notifSukses('AUTH', 'LOGIN', 'Admin berhasil masuk', t());
  } catch (e) {
    notifGagal('AUTH', 'LOGIN', 'Admin gagal masuk', e, t());
    return;
  }

  // ══════════════════════════════════════════════════════════════════════
  // 2. DASHBOARD
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('DASHBOARD');
  t = timer();
  try {
    await bukaHalaman(`${BASE_URL}/index`);
    notifSukses('DASHBOARD', 'BUKA', 'Dashboard terbuka', t());
  } catch (e) { notifGagal('DASHBOARD', 'BUKA', 'Dashboard gagal', e, t()); }

  // ══════════════════════════════════════════════════════════════════════
  // 3. EVENT
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('EVENT');
  await bukaHalaman(`${BASE_URL}/event`);
  const namaEvent = `Event Test ${Date.now()}`;
  const namaEventBaru = `Event Updated ${Date.now()}`;

  t = timer();
  try {
    await page.click('#btnTambahEvent');
    await page.waitForSelector('#modalEvent', { state: 'visible' });
    await page.fill('#inputNamaEvent', namaEvent);
    await page.selectOption('#inputJenis', 'INOTEK');
    await page.click('#btnSimpanEvent');
    await page.waitForSelector('#modalEvent', { state: 'hidden', timeout: 10000 });
    notifSukses('EVENT', 'TAMBAH', namaEvent, t());
  } catch (e) { notifGagal('EVENT', 'TAMBAH', namaEvent, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.fill(namaEvent);
    await page.waitForTimeout(1500);

    const editBtn = page.locator('#tabelEventBody tr .btn-edit-event').first();
    await editBtn.waitFor({ state: 'visible', timeout: 5000 });
    await editBtn.click();
    await page.waitForSelector('#modalEvent', { state: 'visible' });
    await page.fill('#inputNamaEvent', namaEventBaru);
    await page.selectOption('#inputJenis', 'INODA');
    await page.click('#btnSimpanEvent');
    await page.waitForSelector('#modalEvent', { state: 'hidden', timeout: 10000 });
    notifSukses('EVENT', 'UBAH', namaEventBaru, t());
  } catch (e) { notifGagal('EVENT', 'UBAH', namaEvent, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.fill('');
    await page.waitForTimeout(1000);
    await searchBox.fill(namaEventBaru);
    await page.waitForTimeout(1500);

    const hapusBtn = page.locator('#tabelEventBody tr .btn-hapus-event').first();
    await hapusBtn.waitFor({ state: 'visible', timeout: 5000 });
    await hapusBtn.click();
    await page.waitForSelector('#modalHapusEvent', { state: 'visible' });
    await page.click('#btnHapusEvent');
    await page.waitForSelector('#modalHapusEvent', { state: 'hidden', timeout: 10000 });
    notifSukses('EVENT', 'HAPUS', namaEventBaru, t());
  } catch (e) { notifGagal('EVENT', 'HAPUS', namaEventBaru, e, t()); }

  // ══════════════════════════════════════════════════════════════════════
  // 4. SUB EVENT
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('SUB EVENT');
  await bukaHalaman(`${BASE_URL}/sub-event`);
  const namaSubEvent = `Sub Event Test ${Date.now()}`;
  const namaSubEventBaru = `Sub Event Updated ${Date.now()}`;

  t = timer();
  try {
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
    notifSukses('SUB EVENT', 'TAMBAH', namaSubEvent, t());
  } catch (e) { notifGagal('SUB EVENT', 'TAMBAH', namaSubEvent, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.fill(namaSubEvent);
    await page.waitForTimeout(1500);

    const editBtn = page.locator('#tabelSubEventBody tr .btn-edit-se').first();
    await editBtn.waitFor({ state: 'visible', timeout: 5000 });
    await editBtn.click();
    await page.waitForSelector('#modalSubEvent', { state: 'visible' });
    await page.fill('#seSubEvent', namaSubEventBaru);
    await page.click('#btnSimpanSE');
    await page.waitForSelector('#modalSubEvent', { state: 'hidden', timeout: 10000 });
    notifSukses('SUB EVENT', 'UBAH', namaSubEventBaru, t());
  } catch (e) { notifGagal('SUB EVENT', 'UBAH', namaSubEvent, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.fill('');
    await page.waitForTimeout(1000);
    await searchBox.fill(namaSubEventBaru);
    await page.waitForTimeout(1500);

    const hapusBtn = page.locator('#tabelSubEventBody tr .btn-hapus-se').first();
    await hapusBtn.waitFor({ state: 'visible', timeout: 5000 });
    await hapusBtn.click();
    await page.waitForSelector('#modalHapusSE', { state: 'visible' });
    await page.click('#btnHapusSE');
    await page.waitForSelector('#modalHapusSE', { state: 'hidden', timeout: 10000 });
    notifSukses('SUB EVENT', 'HAPUS', namaSubEventBaru, t());
  } catch (e) { notifGagal('SUB EVENT', 'HAPUS', namaSubEventBaru, e, t()); }

  // ══════════════════════════════════════════════════════════════════════
  // 5. BIDANG
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('BIDANG');
  await bukaHalaman(`${BASE_URL}/bidang`);
  await page.waitForSelector('.accordion', { timeout: 10000 });

  const accordionBtn = page.locator('.bidang-accordion-btn').first();
  await accordionBtn.scrollIntoViewIfNeeded();
  if ((await accordionBtn.getAttribute('aria-expanded')) !== 'true') await accordionBtn.click();
  await page.waitForTimeout(3000);

  const namaBidang = `Bidang Test ${Date.now()}`;
  const namaBidangBaru = `Bidang Updated ${Date.now()}`;

  t = timer();
  try {
    await page.locator('.btn-tambah-bidang').first().click();
    await page.waitForSelector('#modalBidang', { state: 'visible' });
    await page.fill('#bidangNama', namaBidang);
    await page.check('#statusAktifBidang');
    await page.click('#btnSimpanBidang');
    await page.waitForSelector('#modalBidang', { state: 'hidden', timeout: 10000 });
    await page.waitForTimeout(2000);
    notifSukses('BIDANG', 'TAMBAH', namaBidang, t());
  } catch (e) { notifGagal('BIDANG', 'TAMBAH', namaBidang, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.scrollIntoViewIfNeeded();
    await searchBox.fill(namaBidang);
    await page.waitForTimeout(1500);

    const editBtn = page.locator('.btn-ubah-bidang:visible').first();
    await editBtn.waitFor({ state: 'visible', timeout: 5000 });
    await editBtn.click();
    await page.waitForSelector('#modalBidang', { state: 'visible' });
    await page.fill('#bidangNama', namaBidangBaru);
    await page.check('#statusNonaktifBidang');
    await page.click('#btnSimpanBidang');
    await page.waitForSelector('#modalBidang', { state: 'hidden', timeout: 10000 });
    await page.waitForTimeout(2000);
    notifSukses('BIDANG', 'UBAH', namaBidangBaru, t());
  } catch (e) { notifGagal('BIDANG', 'UBAH', namaBidang, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.fill('');
    await page.waitForTimeout(1000);
    await searchBox.fill(namaBidangBaru);
    await page.waitForTimeout(1500);

    const hapusBtn = page.locator('.btn-hapus-bidang:visible').first();
    await hapusBtn.waitFor({ state: 'visible', timeout: 5000 });
    await hapusBtn.click();
    await page.waitForSelector('#modalHapusBidang', { state: 'visible' });
    await page.click('#btnHapusBidang');
    await page.waitForSelector('#modalHapusBidang', { state: 'hidden', timeout: 10000 });
    await page.waitForTimeout(2000);
    notifSukses('BIDANG', 'HAPUS', namaBidangBaru, t());
  } catch (e) { notifGagal('BIDANG', 'HAPUS', namaBidangBaru, e, t()); }

  // ══════════════════════════════════════════════════════════════════════
  // 6. USER
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('USER');
  await bukaHalaman(`${BASE_URL}/user`);
  const ts = Date.now();
  const namaUser = `User Test ${ts}`;
  const emailUser = `user${ts}@test.com`;
  const namaUserBaru = `User Updated ${Date.now()}`;

  t = timer();
  try {
    await page.click('#btnTambahUser');
    await page.waitForSelector('#modalUser', { state: 'visible' });
    await page.fill('#inputNama', namaUser);
    await page.fill('#inputEmail', emailUser);
    await page.selectOption('#inputHakAkses', 'peserta');
    await page.fill('#inputPassword', 'password123');
    await page.check('#statusAktif');
    await page.click('#btnSimpanUser');
    await page.waitForSelector('#modalUser', { state: 'hidden', timeout: 10000 });
    notifSukses('USER', 'TAMBAH', namaUser, t());
  } catch (e) { notifGagal('USER', 'TAMBAH', namaUser, e, t()); }

  t = timer();
  try {
    await page.reload();
    await page.waitForTimeout(2000);
    const searchBox = getSearchBox();
    await searchBox.fill(emailUser);
    await page.waitForTimeout(2000);

    const editBtn = page.locator('#tabelUserBody tr .btn-edit-user').first();
    await editBtn.waitFor({ state: 'visible', timeout: 5000 });
    await editBtn.click({ force: true });
    await page.waitForSelector('#modalUser', { state: 'visible' });
    await page.fill('#inputNama', namaUserBaru);
    await page.selectOption('#inputHakAkses', 'penilai');
    await page.check('#statusNonaktif');
    await page.click('#btnSimpanUser');
    await page.waitForSelector('#modalUser', { state: 'hidden', timeout: 10000 });
    notifSukses('USER', 'UBAH', namaUserBaru, t());
  } catch (e) { notifGagal('USER', 'UBAH', namaUser, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.fill('');
    await page.waitForTimeout(1000);
    await searchBox.fill(emailUser);
    await page.waitForTimeout(2000);

    const hapusBtn = page.locator('#tabelUserBody tr .btn-hapus-user').first();
    await hapusBtn.waitFor({ state: 'visible', timeout: 5000 });
    await hapusBtn.click({ force: true });
    await page.waitForSelector('#modalHapusUser', { state: 'visible' });
    await page.click('#btnHapusUser');
    await page.waitForSelector('#modalHapusUser', { state: 'hidden', timeout: 10000 });
    notifSukses('USER', 'HAPUS', namaUserBaru, t());
  } catch (e) { notifGagal('USER', 'HAPUS', namaUserBaru, e, t()); }

  // ══════════════════════════════════════════════════════════════════════
  // 7. PENILAI — TAMBAH → GANTI → HAPUS
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('PENILAI');
  await bukaHalaman(`${BASE_URL}/penilai`);

  t = timer();
  try {
    const detailBtn = page.locator('a.btn-primary:has-text("Detail")').first();
    await detailBtn.scrollIntoViewIfNeeded();
    await detailBtn.click();
    await page.waitForURL(/penilai\/\d+/, { timeout: 10000 });
    await page.waitForSelector('#tabelPenilai', { timeout: 10000 });
    await page.waitForTimeout(1500);

    await page.click('#btnTambahPenilai');
    await page.waitForSelector('#modalPenilai', { state: 'visible' });
    const optCount = await page.locator('#penilaiUserId option:not([disabled])').count();
    if (optCount <= 1) throw new Error('Tidak ada user penilai tersedia');

    await page.selectOption('#penilaiUserId', { index: 1 });
    await page.waitForTimeout(500);
    await page.click('#btnSimpanPenilai');
    await page.waitForSelector('#modalPenilai', { state: 'hidden', timeout: 15000 });
    await page.waitForTimeout(1500);
    notifSukses('PENILAI', 'TAMBAH', 'Penilai ditambahkan', t());

    t = timer();
    const editBtn = page.locator('.btn-edit-penilai').last();
    await editBtn.scrollIntoViewIfNeeded();
    await editBtn.click();
    await page.waitForSelector('#modalPenilai', { state: 'visible' });
    if (await page.locator('#penilaiUserId option:not([disabled])').count() > 2) {
      await page.selectOption('#penilaiUserId', { index: 2 });
      await page.waitForTimeout(500);
    }
    await page.click('#btnSimpanPenilai');
    await page.waitForSelector('#modalPenilai', { state: 'hidden', timeout: 15000 });
    await page.waitForTimeout(1500);
    notifSukses('PENILAI', 'GANTI', 'Penilai diganti', t());

    t = timer();
    const hapusBtn = page.locator('.btn-hapus-penilai').last();
    await hapusBtn.scrollIntoViewIfNeeded();
    await hapusBtn.click();
    await page.waitForSelector('#modalHapusPenilai', { state: 'visible' });
    await page.click('#btnHapusPenilai');
    await page.waitForSelector('#modalHapusPenilai', { state: 'hidden', timeout: 15000 });
    await page.waitForTimeout(1500);
    notifSukses('PENILAI', 'HAPUS', 'Penilai dihapus', t());

  } catch (e) {
    notifGagal('PENILAI', 'CRUD', 'Gagal', e, t());
  }

  // ══════════════════════════════════════════════════════════════════════
  // 8. PENGUMUMAN
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('PENGUMUMAN');
  await bukaHalaman(`${BASE_URL}/pengumuman`);
  const judulPengumuman = `Pengumuman Test ${Date.now()}`;
  const judulBaru = `Pengumuman Updated ${Date.now()}`;

  t = timer();
  try {
    await page.click('#btnTambahPengumuman');
    await page.waitForSelector('#modalPengumuman', { state: 'visible' });
    await page.fill('#pJudul', judulPengumuman);
    await page.fill('#pDeskripsi', 'Deskripsi test');
    await page.selectOption('#pStatus', 'Published');
    await page.click('#btnSimpanPengumuman');
    await page.waitForSelector('#modalPengumuman', { state: 'hidden', timeout: 10000 });
    notifSukses('PENGUMUMAN', 'TAMBAH', judulPengumuman, t());
  } catch (e) { notifGagal('PENGUMUMAN', 'TAMBAH', judulPengumuman, e, t()); }

  t = timer();
  try {
    await page.reload();
    await page.waitForTimeout(2000);
    const searchBox = getSearchBox();
    await searchBox.fill(judulPengumuman);
    await page.waitForTimeout(2000);

    const editBtn = page.locator('#tabelPengumumanBody tr .btn-edit-pengumuman').first();
    await editBtn.waitFor({ state: 'visible', timeout: 5000 });
    await editBtn.click({ force: true });
    await page.waitForSelector('#modalPengumuman', { state: 'visible' });
    await page.fill('#pJudul', judulBaru);
    await page.selectOption('#pStatus', 'Draft');
    await page.click('#btnSimpanPengumuman');
    await page.waitForSelector('#modalPengumuman', { state: 'hidden', timeout: 10000 });
    notifSukses('PENGUMUMAN', 'UBAH', judulBaru, t());
  } catch (e) { notifGagal('PENGUMUMAN', 'UBAH', judulPengumuman, e, t()); }

  t = timer();
  try {
    const searchBox = getSearchBox();
    await searchBox.fill('');
    await page.waitForTimeout(1000);
    await searchBox.fill(judulBaru);
    await page.waitForTimeout(2000);

    const hapusBtn = page.locator('#tabelPengumumanBody tr .btn-hapus-pengumuman').first();
    await hapusBtn.waitFor({ state: 'visible', timeout: 5000 });
    await hapusBtn.click({ force: true });
    await page.waitForSelector('#modalHapusPengumuman', { state: 'visible' });
    await page.click('#btnHapusPengumuman');
    await page.waitForSelector('#modalHapusPengumuman', { state: 'hidden', timeout: 10000 });
    notifSukses('PENGUMUMAN', 'HAPUS', judulBaru, t());
  } catch (e) { notifGagal('PENGUMUMAN', 'HAPUS', judulBaru, e, t()); }

  // ══════════════════════════════════════════════════════════════════════
  // 9. INDIKATOR TAHAP 1 — Indikator CRUD + Keterangan CRUD (via search)
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('INDIKATOR TAHAP 1');
  await bukaHalaman(`${BASE_URL}/indikator/tahap-1`);

  let ind1Nama = null;
  let ketNama = null;
  let masukKeterangan = false;

  // ── TAMBAH INDIKATOR ──
  t = timer();
  try {
    const detailBtn = page.locator('a.btn-primary:has-text("Detail")').first();
    await detailBtn.scrollIntoViewIfNeeded();
    await detailBtn.click();
    await page.waitForURL(/indikator\/tahap-1\/\d+\/inovasi/, { timeout: 10000 });
    await page.waitForSelector('#tabelDetailInovasi', { timeout: 10000 });
    await page.waitForTimeout(1500);

    ind1Nama = `Indikator T1 ${Date.now()}`;
    await page.click('#btnTambahIndikator');
    await page.waitForSelector('#modalIndikator', { state: 'visible' });
    await page.fill('#inputNamaIndikator', ind1Nama);
    await page.selectOption('#selectJenis', 'substansi');
    await page.click('#btnSimpanIndikator');
    await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
    await page.waitForTimeout(2000);
    notifSukses('INDIKATOR TAHAP 1', 'IND+TAMBAH', ind1Nama, t());
  } catch (e) { notifGagal('INDIKATOR TAHAP 1', 'IND+TAMBAH', 'Gagal', e, t()); }

  // ── SEARCH + UBAH INDIKATOR ──
  if (ind1Nama) {
    t = timer();
    try {
      const searchBox = getSearchBox();
      await searchBox.scrollIntoViewIfNeeded();
      await searchBox.fill(ind1Nama);
      await page.waitForTimeout(1500);

      const namaBaru = `${ind1Nama} Upd`;
      const editBtn = page.locator('#tabelDetailInovasiBody tr .btn-edit-indikator').first();
      await editBtn.waitFor({ state: 'visible', timeout: 5000 });
      await editBtn.click();
      await page.waitForSelector('#modalIndikator', { state: 'visible' });
      await page.fill('#inputNamaIndikator', namaBaru);
      await page.selectOption('#selectJenis', 'makalah');
      await page.click('#btnSimpanIndikator');
      await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
      await page.waitForTimeout(2000);
      ind1Nama = namaBaru;
      notifSukses('INDIKATOR TAHAP 1', 'IND+UBAH', namaBaru, t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 1', 'IND+UBAH', 'Gagal', e, t()); }
  }

  // ── SEARCH + BUKA DETAIL KETERANGAN ──
  if (ind1Nama) {
    t = timer();
    try {
      const searchBox = getSearchBox();
      await searchBox.fill('');
      await page.waitForTimeout(1000);
      await searchBox.fill(ind1Nama);
      await page.waitForTimeout(1500);

      const detailKetBtn = page.locator('#tabelDetailInovasiBody tr a.btn-primary:has-text("Detail")').first();
      await detailKetBtn.waitFor({ state: 'visible', timeout: 5000 });
      await detailKetBtn.click();
      await page.waitForURL(/indikator\/tahap-1\/\d+\/detail\/\d+/, { timeout: 10000 });
      await page.waitForSelector('#tabelKeterangan', { timeout: 10000 });
      await page.waitForTimeout(1500);
      masukKeterangan = true;
      notifSukses('INDIKATOR TAHAP 1', 'KET+BUKA', 'Halaman keterangan terbuka', t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 1', 'KET+BUKA', 'Gagal buka detail', e, t()); }
  }

  // ── TAMBAH KETERANGAN ──
  if (masukKeterangan) {
    t = timer();
    try {
      ketNama = `Ket Test ${Date.now()}`;
      await page.click('#btnTambahKeterangan');
      await page.waitForSelector('#modalKeterangan', { state: 'visible' });
      await page.fill('#inputKeterangan', ketNama);
      await page.fill('#inputNilaiMinimal', '1');
      await page.fill('#inputNilaiMaksimal', '5');
      await page.click('#formKeterangan button[type="submit"]');
      await page.waitForSelector('#modalKeterangan', { state: 'hidden', timeout: 10000 });
      await page.waitForTimeout(2000);
      notifSukses('INDIKATOR TAHAP 1', 'KET+TAMBAH', ketNama, t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 1', 'KET+TAMBAH', 'Gagal', e, t()); }
  }

  // ── SEARCH + UBAH KETERANGAN ──
  if (ketNama) {
    t = timer();
    try {
      const searchBox = getSearchBox();
      await searchBox.scrollIntoViewIfNeeded();
      await searchBox.fill(ketNama);
      await page.waitForTimeout(1500);

      const ketBaru = `${ketNama} Upd`;
      const editBtn = page.locator('#tabelKeteranganBody tr .btn-edit-keterangan').first();
      await editBtn.waitFor({ state: 'visible', timeout: 5000 });
      await editBtn.click();
      await page.waitForSelector('#modalKeterangan', { state: 'visible' });
      await page.fill('#inputKeterangan', ketBaru);
      await page.fill('#inputNilaiMinimal', '2');
      await page.fill('#inputNilaiMaksimal', '4');
      await page.click('#formKeterangan button[type="submit"]');
      await page.waitForSelector('#modalKeterangan', { state: 'hidden', timeout: 10000 });
      await page.waitForTimeout(2000);
      ketNama = ketBaru;
      notifSukses('INDIKATOR TAHAP 1', 'KET+UBAH', ketBaru, t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 1', 'KET+UBAH', 'Gagal', e, t()); }
  }

  // ── SEARCH + HAPUS KETERANGAN ──
  if (ketNama) {
    t = timer();
    try {
      const searchBox = getSearchBox();
      await searchBox.fill('');
      await page.waitForTimeout(1000);
      await searchBox.fill(ketNama);
      await page.waitForTimeout(1500);

      const hapusBtn = page.locator('#tabelKeteranganBody tr .btn-hapus-keterangan').first();
      await hapusBtn.waitFor({ state: 'visible', timeout: 5000 });
      await hapusBtn.click();
      await page.waitForSelector('#modalHapusKeterangan', { state: 'visible' });
      await page.click('#formHapusKeterangan button[type="submit"]');
      await page.waitForSelector('#modalHapusKeterangan', { state: 'hidden', timeout: 10000 });
      await page.waitForTimeout(2000);
      notifSukses('INDIKATOR TAHAP 1', 'KET+HAPUS', ketNama, t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 1', 'KET+HAPUS', 'Gagal', e, t()); }
  }

  // ── KEMBALI + SEARCH + HAPUS INDIKATOR ──
  if (ind1Nama && masukKeterangan) {
    t = timer();
    try {
      const kembaliBtn = page.locator('a.btn-dark:has-text("Kembali")').first();
      await kembaliBtn.scrollIntoViewIfNeeded();
      await kembaliBtn.click();
      await page.waitForURL(/indikator\/tahap-1\/\d+\/inovasi/, { timeout: 10000 });
      await page.waitForSelector('#tabelDetailInovasi', { timeout: 10000 });
      await page.waitForTimeout(1500);

      const searchBox = getSearchBox();
      await searchBox.scrollIntoViewIfNeeded();
      await searchBox.fill('');
      await page.waitForTimeout(1000);
      await searchBox.fill(ind1Nama);
      await page.waitForTimeout(1500);

      const hapusIndBtn = page.locator('#tabelDetailInovasiBody tr .btn-hapus-indikator').first();
      await hapusIndBtn.waitFor({ state: 'visible', timeout: 5000 });
      await hapusIndBtn.click();
      await page.waitForSelector('#modalHapusIndikator', { state: 'visible' });
      await page.click('#formHapusIndikator button[type="submit"]');
      await page.waitForSelector('#modalHapusIndikator', { state: 'hidden', timeout: 10000 });
      await page.waitForTimeout(2000);
      notifSukses('INDIKATOR TAHAP 1', 'IND+HAPUS', ind1Nama, t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 1', 'IND+HAPUS', 'Gagal', e, t()); }
  }

  // ══════════════════════════════════════════════════════════════════════
  // 10. INDIKATOR TAHAP 2 — TAMBAH → UBAH → HAPUS (via search)
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('INDIKATOR TAHAP 2');
  await bukaHalaman(`${BASE_URL}/indikator/tahap-2`);

  let ind2Nama = null;

  t = timer();
  try {
    const detailBtn = page.locator('a.btn-primary:has-text("Detail")').first();
    await detailBtn.scrollIntoViewIfNeeded();
    await detailBtn.click();
    await page.waitForURL(/indikator\/tahap-2\/\d+\/indikator/, { timeout: 10000 });
    await page.waitForSelector('#tabelTahap2Detail', { timeout: 10000 });
    await page.waitForTimeout(1500);

    ind2Nama = `Indikator T2 ${Date.now()}`;
    await page.click('#btnTambahIndikator');
    await page.waitForSelector('#modalIndikator', { state: 'visible' });
    await page.fill('#inputNamaIndikator', ind2Nama);
    await page.selectOption('#inputJenis', 'Subtansi Inovasi');
    await page.fill('#inputKeterangan', 'Ket T2 test');
    await page.fill('#inputNilaiMinimal', '0');
    await page.fill('#inputNilaiMaksimal', '100');
    await page.click('#modalIndikator .btn-success:has-text("Simpan")');
    await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
    await page.waitForTimeout(2000);
    notifSukses('INDIKATOR TAHAP 2', 'TAMBAH', ind2Nama, t());
  } catch (e) { notifGagal('INDIKATOR TAHAP 2', 'TAMBAH', 'Gagal', e, t()); }

  if (ind2Nama) {
    t = timer();
    try {
      const searchBox = getSearchBox();
      await searchBox.scrollIntoViewIfNeeded();
      await searchBox.fill(ind2Nama);
      await page.waitForTimeout(1500);

      const namaBaru = `${ind2Nama} Upd`;
      const editBtn = page.locator('#tabelTahap2DetailBody tr .btn-edit-indikator').first();
      await editBtn.waitFor({ state: 'visible', timeout: 5000 });
      await editBtn.click();
      await page.waitForSelector('#modalIndikator', { state: 'visible' });
      await page.fill('#inputNamaIndikator', namaBaru);
      await page.selectOption('#inputJenis', 'Peragaan');
      await page.fill('#inputKeterangan', 'Ket updated');
      await page.click('#modalIndikator .btn-success:has-text("Simpan")');
      await page.waitForSelector('#modalIndikator', { state: 'hidden', timeout: 10000 });
      await page.waitForTimeout(2000);
      ind2Nama = namaBaru;
      notifSukses('INDIKATOR TAHAP 2', 'UBAH', namaBaru, t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 2', 'UBAH', 'Gagal', e, t()); }
  }

  if (ind2Nama) {
    t = timer();
    try {
      const searchBox = getSearchBox();
      await searchBox.fill('');
      await page.waitForTimeout(1000);
      await searchBox.fill(ind2Nama);
      await page.waitForTimeout(1500);

      const hapusBtn = page.locator('#tabelTahap2DetailBody tr .btn-hapus-indikator').first();
      await hapusBtn.waitFor({ state: 'visible', timeout: 5000 });
      await hapusBtn.click();
      await page.waitForSelector('#modalHapus', { state: 'visible' });
      await page.click('#formHapus button[type="submit"]');
      await page.waitForSelector('#modalHapus', { state: 'hidden', timeout: 10000 });
      await page.waitForTimeout(2000);
      notifSukses('INDIKATOR TAHAP 2', 'HAPUS', ind2Nama, t());
    } catch (e) { notifGagal('INDIKATOR TAHAP 2', 'HAPUS', 'Gagal', e, t()); }
  }

  // ══════════════════════════════════════════════════════════════════════
  // 11. MENU LAIN
  // ══════════════════════════════════════════════════════════════════════
  notifHeader('MENU LAIN');
  const menuLain = [
    { nama: 'Riwayat', url: `${BASE_URL}/inovasi/riwayat` },
    { nama: 'Rekap Nilai', url: `${BASE_URL}/inovasi/rekap-nilai` },
    { nama: 'Penilaian Tahap 1', url: `${BASE_URL}/penilaian/tahap-1` },
    { nama: 'Penilaian Tahap 2', url: `${BASE_URL}/penilaian/tahap-2` },
  ];

  for (const menu of menuLain) {
    t = timer();
    try {
      await page.goto(menu.url);
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(1500);
      const curUrl = page.url();
      if (curUrl.includes('/index') && !menu.url.includes('/index')) throw new Error(`Redirect ke /index`);
      const body = await page.locator('body').innerText().catch(() => '');
      if (body.includes('403') || body.includes('Akses ditolak')) throw new Error(`403 Forbidden`);
      notifSukses('MENU LAIN', 'BUKA', menu.nama, t());
    } catch (e) { notifGagal('MENU LAIN', 'BUKA', menu.nama, e, t()); }
  }

  // ══════════════════════════════════════════════════════════════════════
  // SUMMARY
  // ══════════════════════════════════════════════════════════════════════
  const totalDuration = Date.now() - startTime;

  console.log('\n' + '='.repeat(65));
  console.log('  SUMMARY HASIL TEST');
  console.log('='.repeat(65));
  console.log('\n  Modul                    Berhasil   Gagal   Total');
  console.log('-'.repeat(65));

  for (const [modul, stats] of Object.entries(modulStats)) {
    const status = stats.gagal === 0 ? 'PASS' : 'FAIL';
    console.log(`  [${status}] ${modul.padEnd(20)} ${String(stats.berhasil).padStart(6)}   ${String(stats.gagal).padStart(5)}   ${String(stats.total).padStart(5)}`);
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
    for (const [modul, stats] of Object.entries(modulStats)) {
      if (stats.gagal > 0) console.log(`    - ${modul}: ${stats.gagal} gagal dari ${stats.total}`);
    }
    console.log('');
  }
}); 
