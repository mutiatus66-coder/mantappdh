import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

test('CRUD Event - Admin', async ({ page }) => {

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
  // 2. BUKA HALAMAN EVENT
  // ============================================================
  await page.goto(`${BASE_URL}/event`);
  await expect(page.locator('h3').filter({ hasText: 'Data Event' })).toBeVisible();
  console.log('✅ Halaman Event terbuka');

  // ============================================================
  // 3. TAMBAH EVENT
  // ============================================================
  const namaEvent = `Event Test ${Date.now()}`;
  await page.click('#btnTambahEvent');
  await page.waitForSelector('#modalEvent', { state: 'visible' });
  await page.fill('#inputNamaEvent', namaEvent);
  await page.selectOption('#inputJenis', 'INOTEK');
  await page.click('#btnSimpanEvent');
  await page.waitForSelector('#modalEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelEventBody')).toContainText(namaEvent);
  console.log(`✅ Event ditambahkan: ${namaEvent}`);

  // ============================================================
  // 4. UBAH EVENT
  // ============================================================
  const namaEventBaru = `Event Updated ${Date.now()}`;
  const editBtn = page.locator(`#tabelEventBody tr:has-text("${namaEvent}") .btn-edit-event`).first();
  await editBtn.scrollIntoViewIfNeeded();
  await editBtn.click();
  await page.waitForSelector('#modalEvent', { state: 'visible' });
  await page.fill('#inputNamaEvent', namaEventBaru);
  await page.selectOption('#inputJenis', 'INODA');
  await page.click('#btnSimpanEvent');
  await page.waitForSelector('#modalEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelEventBody')).toContainText(namaEventBaru);
  console.log(`✅ Event diubah: ${namaEventBaru}`);

  // ============================================================
  // 5. HAPUS EVENT
  // ============================================================
  const hapusBtn = page.locator(`#tabelEventBody tr:has-text("${namaEventBaru}") .btn-hapus-event`).first();
  await hapusBtn.scrollIntoViewIfNeeded();
  await hapusBtn.click();
  await page.waitForSelector('#modalHapusEvent', { state: 'visible', timeout: 5000 });
  await page.click('#btnHapusEvent');
  await page.waitForSelector('#modalHapusEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelEventBody')).not.toContainText(namaEventBaru);
  console.log(`✅ Event dihapus: ${namaEventBaru}`);

  console.log('\n🎉 CRUD Event SELESAI!');
});
