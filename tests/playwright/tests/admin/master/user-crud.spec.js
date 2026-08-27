import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

// 🔥 SET TIMEOUT JADI 60 DETIK
test.setTimeout(60000);

test('CRUD User - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN USER
  // ============================================================
  await page.goto(`${BASE_URL}/user`);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('h3').filter({ hasText: 'Data User' })).toBeVisible();
  console.log('✅ Halaman User terbuka');

  // ============================================================
  // 3. TAMBAH USER
  // ============================================================
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

  // ============================================================
  // 4. CARI USER DENGAN SEARCH
  // ============================================================
  await page.reload();
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(2000);

  let searchInput = page.locator('#tabelUser_filter input, .dataTables_filter input, input[type="search"]').first();
  await searchInput.waitFor({ state: 'visible', timeout: 15000 });
  await searchInput.fill(emailUser);
  await page.waitForTimeout(3000);

  await page.waitForSelector(`#tabelUserBody tr:has-text("${emailUser}")`, { timeout: 10000 });
  await expect(page.locator('#tabelUserBody')).toContainText(namaUser);
  console.log(`✅ User ditambahkan: ${namaUser}`);

  // ============================================================
  // 5. UBAH USER
  // ============================================================
  const namaUserBaru = `User Updated ${Date.now()}`;

  const editResult = await page.evaluate((email) => {
    const rows = document.querySelectorAll('#tabelUserBody tr');
    for (const row of rows) {
      if (row.textContent.includes(email)) {
        const btn = row.querySelector('.btn-edit-user');
        if (btn) {
          btn.click();
          return 'success';
        }
      }
    }
    return 'not found';
  }, emailUser);

  console.log(`🔍 Hasil cari tombol Ubah: ${editResult}`);

  if (editResult === 'not found') {
    const anyEditBtn = page.locator('.btn-edit-user').first();
    if (await anyEditBtn.isVisible()) {
      await anyEditBtn.click({ force: true });
      console.log('⚠️ Klik tombol Ubah pertama (force)');
    }
  }

  await page.waitForSelector('#modalUser', { state: 'visible', timeout: 10000 });
  await page.fill('#inputNama', namaUserBaru);
  await page.selectOption('#inputHakAkses', 'penilai');
  await page.click('#statusNonaktif');
  await page.click('#btnSimpanUser');
  await page.waitForSelector('#modalUser', { state: 'hidden', timeout: 10000 });

  await searchInput.fill(emailUser);
  await page.waitForTimeout(3000);
  await page.waitForSelector(`#tabelUserBody tr:has-text("${emailUser}")`, { timeout: 10000 });
  await expect(page.locator('#tabelUserBody')).toContainText(namaUserBaru);
  console.log(`✅ User diubah: ${namaUserBaru}`);

  // ============================================================
  // 6. HAPUS USER
  // ============================================================
  const hapusResult = await page.evaluate((email) => {
    const rows = document.querySelectorAll('#tabelUserBody tr');
    for (const row of rows) {
      if (row.textContent.includes(email)) {
        const btn = row.querySelector('.btn-hapus-user');
        if (btn) {
          btn.click();
          return 'success';
        }
      }
    }
    return 'not found';
  }, emailUser);

  console.log(`🔍 Hasil cari tombol Hapus: ${hapusResult}`);

  if (hapusResult === 'not found') {
    const anyHapusBtn = page.locator('.btn-hapus-user').first();
    if (await anyHapusBtn.isVisible()) {
      await anyHapusBtn.click({ force: true });
      console.log('⚠️ Klik tombol Hapus pertama (force)');
    }
  }

  await page.waitForSelector('#modalHapusUser', { state: 'visible', timeout: 5000 });
  await page.click('#btnHapusUser');

  try {
    await page.waitForSelector('#modalHapusUser', { state: 'hidden', timeout: 10000 });
    console.log('✅ Modal hapus tertutup');
  } catch (e) {
    console.log('⚠️ Modal hapus tidak tertutup, klik batal');
    await page.click('#btnBatalHapusUser');
    await page.waitForSelector('#modalHapusUser', { state: 'hidden', timeout: 5000 });
  }

  // ============================================================
  // 7. VERIFIKASI HAPUS
  // ============================================================
  await searchInput.fill(emailUser);
  await page.waitForTimeout(1000);

  // 🔥 CEK APAKAH MASIH ADA
  const isExist = await page.locator(`#tabelUserBody tr:has-text("${emailUser}")`).isVisible({ timeout: 3000 });
  expect(isExist).toBe(false);
  console.log(`✅ User dihapus: ${namaUserBaru}`);

  await searchInput.fill('');

  console.log('\n🎉 CRUD User SELESAI!');
});
