import { test, expect } from '@playwright/test';

// ── KONFIGURASI ──
const BASE_URL = 'http://localhost:8000';           // URL aplikasi Laravel
const PENILAI_EMAIL = 'ahmad.fauzi@example.com';     // Email login penilai
const PENILAI_PASSWORD = 'password';                 // Password login penilai

// Timeout global 15 menit — flow panjang & banyak wait
test.setTimeout(900000);

test('Full CRUD Penilai - Penilaian Tahap 1 & 2', async ({ page }) => {

  // ── STATISTIK TEST ──
  let totalStep = 0;            // Total step dijalankan
  let totalBerhasil = 0;        // Total step sukses
  let totalGagal = 0;           // Total step gagal
  let startTime = Date.now();   // Waktu mulai test
  let modulStats = {};          // Statistik per modul

  // ── FORMAT DURASI: <1s tampil ms, >=1s tampil detik ──
  const formatTime = (ms) => ms < 1000 ? `${ms}ms` : `${(ms / 1000).toFixed(2)}s`;

  // ── PRINT HEADER MODUL ──
  const notifHeader = (nama) => {
    console.log('\n' + '-'.repeat(65));
    console.log(`  MODUL: ${nama}`);
    console.log('-'.repeat(65));
    modulStats[nama] = { berhasil: 0, gagal: 0, total: 0 };
  };

  // ── CATAT STEP SUKSES ──
  const notifSukses = (modul, aksi, detail, duration) => {
    totalStep++; totalBerhasil++;
    if (modulStats[modul]) { modulStats[modul].berhasil++; modulStats[modul].total++; }
    console.log(`  [PASS] [${String(totalStep).padStart(2, '0')}] ${aksi.padEnd(12)} | ${detail.padEnd(38)} | ${formatTime(duration)}`);
  };

  // ── CATAT STEP GAGAL ──
  const notifGagal = (modul, aksi, detail, error, duration) => {
    totalStep++; totalGagal++;
    if (modulStats[modul]) { modulStats[modul].gagal++; modulStats[modul].total++; }
    console.log(`  [FAIL] [${String(totalStep).padStart(2, '0')}] ${aksi.padEnd(12)} | ${detail.padEnd(38)} | ${formatTime(duration)}`);
    console.log(`         -> ${error.message?.substring(0, 70) || 'Unknown error'}`);
  };

  // ── TIMER: let t = timer(); ...; t() → durasi ms ──
  const timer = () => { const s = Date.now(); return () => Date.now() - s; };

  // ══════════════════════════════════════════════════════════════════
  // HELPER TAHAP 1 — INPUT NILAI VIA MODAL (tombol pencil)
  // ══════════════════════════════════════════════════════════════════
  const inputNilaiTahap1 = async (nilaiArr = []) => {
    console.log('         -> Cari tombol Input Nilai (btn-input-nilai)...');

    // Cari tombol pencil di tab aktif (class .btn-input-nilai dari Blade)
    const inputBtn = page.locator('.tab-pane.active button.btn-input-nilai').first();
    await inputBtn.waitFor({ state: 'visible', timeout: 10000 });
    console.log('         -> Tombol Input Nilai ditemukan');

    // Klik tombol → tunggu modal terbuka
    await inputBtn.scrollIntoViewIfNeeded();
    await inputBtn.click();
    await page.waitForTimeout(1500);

    // Cari modal Bootstrap yang aktif (.modal.show)
    const modal = page.locator('.modal.show').first();
    await modal.waitFor({ state: 'visible', timeout: 5000 });
    console.log('         -> Modal Input Nilai terbuka');

    // Hitung jumlah input nilai di modal (class .input-nilai-item)
    const inputs = modal.locator('input.input-nilai-item');
    const count = await inputs.count();
    console.log(`         -> Jumlah input nilai: ${count}`);
    if (count === 0) throw new Error('Tidak ada input nilai di modal');

    // Loop isi semua input nilai
    for (let i = 0; i < count; i++) {
      const inp = inputs.nth(i);

      // Ambil min/max dari atribut input → buat clamp nilai
      const min = parseInt(await inp.getAttribute('min') || '0', 10);
      const max = parseInt(await inp.getAttribute('max') || '100', 10);

      // Ambil nilai dari array; kalau tidak ada, pakai nilai tengah
      let val = nilaiArr[i];
      if (val === undefined || val === null) val = Math.floor((min + max) / 2);
      if (val < min) val = min;
      if (val > max) val = max;

      await inp.scrollIntoViewIfNeeded();
      await inp.fill(String(val));
      await page.waitForTimeout(100);
    }

    // Klik tombol "Simpan Nilai" di dalam modal
    const simpanBtn = modal.locator('button.btn-simpan-nilai-modal');
    await simpanBtn.scrollIntoViewIfNeeded();
    await simpanBtn.click();
    console.log('         -> Tombol Simpan Nilai diklik');

    // Tunggu AJAX simpan selesai (modal akan close kalau sukses)
    await page.waitForTimeout(2500);

    // Cek toast error — kalau ada, berarti server nolak
    const errorToast = page.locator('.ri-toast-error');
    if (await errorToast.isVisible({ timeout: 1000 }).catch(() => false)) {
      const msg = await errorToast.innerText();
      throw new Error(`Server menolak: ${msg}`);
    }

    return count;
  };

  // ══════════════════════════════════════════════════════════════════
  // HELPER TAHAP 1 — INPUT CATATAN VIA MODAL (tombol chat)
  // ══════════════════════════════════════════════════════════════════
  const inputCatatanTahap1 = async (catatan) => {
    console.log('         -> Cari tombol Catatan (btn-catatan)...');

    // Cari tombol catatan di tab aktif (class .btn-catatan dari Blade)
    const catatanBtn = page.locator('.tab-pane.active button.btn-catatan').first();
    await catatanBtn.waitFor({ state: 'visible', timeout: 10000 });
    console.log('         -> Tombol Catatan ditemukan');

    // Klik tombol → tunggu modal catatan muncul
    await catatanBtn.scrollIntoViewIfNeeded();
    await catatanBtn.click();
    await page.waitForTimeout(1500);

    const modal = page.locator('.modal.show').first();
    await modal.waitFor({ state: 'visible', timeout: 5000 });
    console.log('         -> Modal Catatan terbuka');

    // Isi textarea catatan
    const textarea = modal.locator('textarea').first();
    await textarea.scrollIntoViewIfNeeded();
    await textarea.fill(catatan);
    await page.waitForTimeout(300);
    console.log('         -> Catatan diisi');

    // Klik tombol "Simpan Catatan"
    const simpanBtn = modal.locator('button.btn-simpan-catatan');
    await simpanBtn.scrollIntoViewIfNeeded();
    await simpanBtn.click();
    console.log('         -> Tombol Simpan Catatan diklik');
    await page.waitForTimeout(2000);

    // Cek error toast
    const errorToast = page.locator('.ri-toast-error');
    if (await errorToast.isVisible({ timeout: 1000 }).catch(() => false)) {
      const msg = await errorToast.innerText();
      throw new Error(`Server menolak: ${msg}`);
    }

    return true;
  };

  // ══════════════════════════════════════════════════════════════════
  // HELPER TAHAP 1 — LOLOSKAN (centang) + SIMPAN → halaman reload
  // ══════════════════════════════════════════════════════════════════
  const loloskanDanSimpanTahap1 = async (maxPilih = 7, group = 'umum') => {
    console.log(`         -> Cari checkbox chk-row yang enabled (max: ${maxPilih})...`);

    // Tunggu tabel & checkbox muncul dulu
    await page.waitForSelector(`.tab-pane.active input.chk-row[data-group="${group}"]`, { timeout: 10000 });
    await page.waitForTimeout(1000);

    // Ambil checkbox yang ENABLED saja (:not([disabled]))
    // Checkbox disabled = baris belum "Lengkap" (belum semua penilai nilai)
    const checkboxes = page.locator(`.tab-pane.active input.chk-row[data-group="${group}"]:not([disabled])`);
    const total = await checkboxes.count();
    console.log(`         -> Checkbox bisa dicentang: ${total}`);

    if (total === 0) {
      throw new Error('Tidak ada checkbox yang bisa dicentang (semua baris belum Lengkap)');
    }

    // Centang sebanyak maxPilih atau sebanyak yang tersedia
    const jumlahCentang = Math.min(maxPilih, total);
    for (let i = 0; i < jumlahCentang; i++) {
      const chk = checkboxes.nth(i);

      const isVisible = await chk.isVisible().catch(() => false);
      if (!isVisible) continue;

      // Skip kalau sudah checked
      const isChecked = await chk.isChecked().catch(() => false);
      if (!isChecked) {
        await chk.scrollIntoViewIfNeeded();
        await chk.check({ force: true });  // force: hindari overlay
        await page.waitForTimeout(250);
      }
    }
    console.log(`         -> ${jumlahCentang} checkbox dicentang`);

    // Cari tombol Simpan (btn-rv-simpan) di header card
    const simpanBtn = page.locator(`.tab-pane.active button.btn-rv-simpan[data-group="${group}"]`).first();
    await simpanBtn.scrollIntoViewIfNeeded();

    // Baca counter di tombol (misal "Simpan (1/7)")
    const btnText = await simpanBtn.innerText();
    console.log(`         -> Tombol Simpan: "${btnText.trim()}"`);

    // 🔥 KLIK SIMPAN + TUNGGU RELOAD
    // Laravel panggil location.reload() setelah 1.5s → tunggu navigasi
    console.log('         -> Klik Simpan & tunggu reload...');

    await Promise.all([
      page.waitForLoadState('networkidle', { timeout: 30000 }).catch(() => {}),
      simpanBtn.click(),
    ]);

    // Extra buffer — supaya reload benar-benar selesai
    await page.waitForTimeout(3000);

    // Cek toast error setelah reload (kalau ada)
    const errorToast = page.locator('.ri-toast-error');
    if (await errorToast.isVisible({ timeout: 1500 }).catch(() => false)) {
      const msg = await errorToast.innerText();
      throw new Error(`Server menolak: ${msg}`);
    }

    // Pastikan halaman sudah kembali normal (tabel muncul lagi)
    await page.waitForSelector('.tab-pane.active table', { timeout: 15000 }).catch(() => {});
    console.log('         -> Reload selesai, halaman kembali normal');

    return jumlahCentang;
  };

  // ══════════════════════════════════════════════════════════════════
  // HELPER TAHAP 2 — AUTO RANKING (tombol "Ranking")
  // ══════════════════════════════════════════════════════════════════
  const autoRankingTahap2 = async (group = 'umum') => {
    console.log(`         -> Klik tombol Ranking (group: ${group})...`);

    // Tombol "Ranking" (btn-auto-ranking) di header card
    const btn = page.locator(`.tab-pane.active button.btn-auto-ranking[data-group="${group}"]`).first();
    await btn.waitFor({ state: 'visible', timeout: 10000 });
    await btn.scrollIntoViewIfNeeded();
    await btn.click();
    await page.waitForTimeout(2000);  // Tunggu JS auto-ranking selesai
    console.log('         -> Ranking otomatis terisi');

    return true;
  };

  // ══════════════════════════════════════════════════════════════════
  // HELPER TAHAP 2 — EDIT RANKING MANUAL
  // ══════════════════════════════════════════════════════════════════
  const editRankingTahap2 = async (rankValue = 1) => {
    console.log('         -> Edit ranking manual baris pertama...');

    // Input ranking di kolom "Ranking Saya"
    const input = page.locator('.tab-pane.active input.input-ranking').first();
    await input.waitFor({ state: 'visible', timeout: 5000 });
    await input.scrollIntoViewIfNeeded();
    await input.fill(String(rankValue));
    await page.waitForTimeout(300);
    console.log(`         -> Ranking baris pertama diset: ${rankValue}`);

    return true;
  };

  // ══════════════════════════════════════════════════════════════════
  // HELPER TAHAP 2 — SIMPAN RANKING
  // ══════════════════════════════════════════════════════════════════
  const simpanRankingTahap2 = async (group = 'umum') => {
    console.log(`         -> Klik tombol Simpan Ranking (group: ${group})...`);

    // Tombol "Simpan Ranking" di header card
    const btn = page.locator(`.tab-pane.active button.btn-simpan-ranking[data-group="${group}"]`).first();
    await btn.waitFor({ state: 'visible', timeout: 10000 });
    await btn.scrollIntoViewIfNeeded();
    await btn.click();
    await page.waitForTimeout(2500);  // Tunggu AJAX simpan selesai
    console.log('         -> Tombol Simpan Ranking diklik');

    // Cek toast error
    const errorToast = page.locator('.ri-toast-error');
    if (await errorToast.isVisible({ timeout: 1500 }).catch(() => false)) {
      const msg = await errorToast.innerText();
      throw new Error(`Server menolak: ${msg}`);
    }

    // Cek toast sukses (opsional, cuma log)
    const successToast = page.locator('.ri-toast-success');
    if (await successToast.isVisible({ timeout: 1000 }).catch(() => false)) {
      console.log('         -> Toast sukses muncul');
    }

    return true;
  };

  // ══════════════════════════════════════════════════════════════════
  // HELPER TAHAP 2 — LIHAT RANKING (badge di kolom Total Rank)
  // ══════════════════════════════════════════════════════════════════
  const lihatRankingTahap2 = async () => {
    console.log('         -> Cek kolom Total Rank...');

    // Badge ranking = span.rv-rank-badge
    const rankBadges = page.locator('.tab-pane.active .rv-rank-badge');
    const count = await rankBadges.count();

    if (count === 0) throw new Error('Tidak ada badge ranking di tabel');

    const firstBadge = await rankBadges.first().innerText();
    console.log(`         -> Ditemukan ${count} badge ranking, contoh: "${firstBadge.trim()}"`);

    return count;
  };

  // ══════════════════════════════════════════════════════════════════
  // MULAI TEST
  // ══════════════════════════════════════════════════════════════════
  console.log('\n' + '='.repeat(65));
  console.log('  PENILAI FULL TEST - TAHAP 1 & 2');
  console.log(`  ${new Date().toLocaleString('id-ID')}`);
  console.log('='.repeat(65));

  // ─────────────────────────────────────────────────────────────────
  // STEP 1 : LOGIN
  // ─────────────────────────────────────────────────────────────────
  notifHeader('AUTH');
  let t = timer();
  try {
    // Buka homepage → klik Login
    await page.goto(BASE_URL);
    await page.click('a.btn-login:has-text("Login")');
    await page.waitForURL(`${BASE_URL}/sign-in`);

    // Isi form login
    await page.fill('input[name="email"]', PENILAI_EMAIL);
    await page.fill('input[name="password"]', PENILAI_PASSWORD);
    await page.click('button[type="submit"]');

    // Tunggu redirect ke /index (berhasil login)
    await page.waitForURL(`${BASE_URL}/index`);
    notifSukses('AUTH', 'LOGIN', 'Penilai berhasil masuk', t());
  } catch (e) {
    notifGagal('AUTH', 'LOGIN', 'Penilai gagal masuk', e, t());
    return;  // Stop kalau login gagal
  }

  // ─────────────────────────────────────────────────────────────────
  // STEP 2 : RIWAYAT
  // ─────────────────────────────────────────────────────────────────
  notifHeader('RIWAYAT');
  t = timer();
  try {
    await page.goto(`${BASE_URL}/inovasi/riwayat`);
    await page.waitForLoadState('networkidle');
    notifSukses('RIWAYAT', 'BUKA', 'Halaman Riwayat terbuka', t());
  } catch (e) {
    notifGagal('RIWAYAT', 'BUKA', 'Gagal buka Riwayat', e, t());
  }

  // ─────────────────────────────────────────────────────────────────
  // STEP 3 : REKAP NILAI
  // ─────────────────────────────────────────────────────────────────
  notifHeader('REKAP NILAI');
  t = timer();
  try {
    await page.goto(`${BASE_URL}/inovasi/rekap-nilai`);
    await page.waitForLoadState('networkidle');
    notifSukses('REKAP NILAI', 'BUKA', 'Halaman Rekap Nilai terbuka', t());
  } catch (e) {
    notifGagal('REKAP NILAI', 'BUKA', 'Gagal buka Rekap Nilai', e, t());
  }

  // ═════════════════════════════════════════════════════════════════
  // STEP 4 : TAHAP 1 — Nilai + Catatan + Loloskan & Simpan
  // ═════════════════════════════════════════════════════════════════
  notifHeader('PENILAIAN TAHAP 1');
  t = timer();
  try {
    // Buka halaman index Tahap 1
    await page.goto(`${BASE_URL}/penilaian/tahap-1`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1500);
    notifSukses('PENILAIAN TAHAP 1', 'BUKA', 'Halaman Penilaian Tahap 1 terbuka', t());

    // Klik "Lihat Nilai Verifikasi" pada card pertama
    t = timer();
    const lihatVerifikasi = page.locator('a.btn-info:has-text("Lihat Nilai Verifikasi")').first();
    await lihatVerifikasi.scrollIntoViewIfNeeded();
    await lihatVerifikasi.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);
    notifSukses('PENILAIAN TAHAP 1', 'DETAIL', 'Halaman Detail Nilai Verifikasi terbuka', t());

    // Tunggu tabel & DataTables selesai render
    await page.waitForSelector('.tab-pane.active table', { timeout: 10000 });
    await page.waitForTimeout(1500);

    // ── INPUT NILAI ──
    let nilai1OK = false;
    t = timer();
    try {
      const limit = await inputNilaiTahap1([85, 80, 90, 85, 80, 88, 82, 90, 85, 80]);
      notifSukses('PENILAIAN TAHAP 1', 'ISI NILAI', `Isi ${limit} nilai indikator`, t());
      nilai1OK = true;
    } catch (e) {
      notifGagal('PENILAIAN TAHAP 1', 'ISI NILAI', 'Gagal input nilai', e, t());
    }

    // ── INPUT CATATAN (hanya kalau nilai OK — fail-fast) ──
    if (nilai1OK) {
      t = timer();
      try {
        const catatanText = `Catatan otomatis - ${new Date().toLocaleString('id-ID')}. Inovasi ini sudah cukup baik.`;
        await inputCatatanTahap1(catatanText);
        notifSukses('PENILAIAN TAHAP 1', 'CATATAN', 'Catatan penilai disimpan', t());
      } catch (e) {
        notifGagal('PENILAIAN TAHAP 1', 'CATATAN', 'Gagal input catatan', e, t());
      }
    } else {
      console.log('         -> SKIP catatan (nilai gagal)');
    }

    // ── LOLOSKAN + SIMPAN (halaman reload setelah ini) ──
    t = timer();
    try {
      const jumlah = await loloskanDanSimpanTahap1(7, 'umum');
      notifSukses('PENILAIAN TAHAP 1', 'LOLOSKAN', `${jumlah} inovasi diloloskan & disimpan`, t());
    } catch (e) {
      notifGagal('PENILAIAN TAHAP 1', 'LOLOSKAN', 'Gagal loloskan & simpan', e, t());
    }

  } catch (e) {
    notifGagal('PENILAIAN TAHAP 1', 'BUKA', 'Gagal buka Penilaian Tahap 1', e, t());
  }

  // ═════════════════════════════════════════════════════════════════
  // STEP 5 : TAHAP 2 — Ranking + Simpan + Lihat
  // ═════════════════════════════════════════════════════════════════
  notifHeader('PENILAIAN TAHAP 2');
  t = timer();
  try {
    // 🔥 PENTING: Navigasi ULANG ke Tahap 2
    // Alasan: Tahap 1 baru reload → konteks halaman beda.
    // Kalau tidak goto, test cari elemen di halaman yang salah.
    await page.goto(`${BASE_URL}/penilaian/tahap-2`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1500);
    notifSukses('PENILAIAN TAHAP 2', 'BUKA', 'Halaman Penilaian Tahap 2 terbuka', t());

    // Klik "Lihat Nilai Nominator"
    t = timer();
    const lihatNominator = page.locator('a.btn-info:has-text("Lihat Nilai Nominator")').first();
    await lihatNominator.scrollIntoViewIfNeeded();
    await lihatNominator.click();
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);
    notifSukses('PENILAIAN TAHAP 2', 'DETAIL', 'Halaman Detail Nilai Nominator terbuka', t());

    // Tunggu tabel siap
    await page.waitForSelector('.tab-pane.active table', { timeout: 10000 });
    await page.waitForTimeout(1500);

    // ── AUTO RANKING ──
    let rankingOK = false;
    t = timer();
    try {
      await autoRankingTahap2('umum');
      notifSukses('PENILAIAN TAHAP 2', 'RANKING', 'Auto-ranking berhasil diisi', t());
      rankingOK = true;
    } catch (e) {
      notifGagal('PENILAIAN TAHAP 2', 'RANKING', 'Gagal auto-ranking', e, t());
    }

    // ── EDIT RANKING MANUAL (hanya kalau auto-ranking OK) ──
    if (rankingOK) {
      t = timer();
      try {
        await editRankingTahap2(1);
        notifSukses('PENILAIAN TAHAP 2', 'EDIT', 'Edit ranking manual berhasil', t());
      } catch (e) {
        notifGagal('PENILAIAN TAHAP 2', 'EDIT', 'Gagal edit ranking', e, t());
      }
    }

    // ── SIMPAN RANKING ──
    if (rankingOK) {
      t = timer();
      try {
        await simpanRankingTahap2('umum');
        notifSukses('PENILAIAN TAHAP 2', 'SIMPAN', 'Ranking berhasil disimpan', t());
      } catch (e) {
        notifGagal('PENILAIAN TAHAP 2', 'SIMPAN', 'Gagal simpan ranking', e, t());
      }
    }

    // ── LIHAT RANKING (verifikasi badge muncul) ──
    t = timer();
    try {
      const count = await lihatRankingTahap2();
      notifSukses('PENILAIAN TAHAP 2', 'LIHAT', `${count} badge ranking tampil`, t());
    } catch (e) {
      notifGagal('PENILAIAN TAHAP 2', 'LIHAT', 'Gagal lihat ranking', e, t());
    }

  } catch (e) {
    notifGagal('PENILAIAN TAHAP 2', 'BUKA', 'Gagal buka Penilaian Tahap 2', e, t());
  }

  // ═════════════════════════════════════════════════════════════════
  // SUMMARY
  // ═════════════════════════════════════════════════════════════════
  const totalDuration = Date.now() - startTime;

  console.log('\n' + '='.repeat(65));
  console.log('  SUMMARY HASIL TEST');
  console.log('='.repeat(65));
  console.log('\n  Modul                  Berhasil   Gagal   Total');
  console.log('-'.repeat(65));

  // Print statistik per modul
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

  // Print status akhir
  if (totalGagal === 0) {
    console.log('\n  STATUS: SEMUA TEST BERHASIL\n');
  } else {
    console.log(`\n  STATUS: ADA ${totalGagal} TEST YANG GAGAL\n`);
  }
});
