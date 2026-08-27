import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@bapperida.test';
const ADMIN_PASSWORD = 'password';

test('CRUD Sub Event - Admin', async ({ page }) => {

  // 1 LOGIN
  await page.goto(BASE_URL);
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await page.fill('input[name="email"]', ADMIN_EMAIL);
  await page.fill('input[name="password"]', ADMIN_PASSWORD);
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/index`);
  console.log('✅ Login Admin berhasil');

  // BUKA HALAMAN SUB EVENT
  await page.goto(`${BASE_URL}/sub-event`);
  await expect(page.locator('h3').filter({ hasText: 'Data Sub Event' })).toBeVisible();
  console.log('✅ Halaman Sub Event terbuka');

  // TAMBAH SUB EVENT
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

  // UBAH SUB EVENT
  const namaSubEventBaru = `Sub Event Updated ${Date.now()}`;
  const editBtn = page.locator(`#tabelSubEventBody tr:has-text("${namaSubEvent}") .btn-edit-se`).first();
  await editBtn.scrollIntoViewIfNeeded();
  await editBtn.click();
  await page.waitForSelector('#modalSubEvent', { state: 'visible' });
  await page.fill('#seSubEvent', namaSubEventBaru);
  await page.click('#btnSimpanSE');
  await page.waitForSelector('#modalSubEvent', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelSubEventBody')).toContainText(namaSubEventBaru);
  console.log(`✅ Sub Event diubah: ${namaSubEventBaru}`);

  // HAPUS SUB EVENT
  const hapusBtn = page.locator(`#tabelSubEventBody tr:has-text("${namaSubEventBaru}") .btn-hapus-se`).first();
  await hapusBtn.scrollIntoViewIfNeeded();
  await hapusBtn.click();
  await page.waitForSelector('#modalHapusSE', { state: 'visible', timeout: 5000 });
  await page.click('#btnHapusSE');
  await page.waitForSelector('#modalHapusSE', { state: 'hidden', timeout: 10000 });
  await expect(page.locator('#tabelSubEventBody')).not.toContainText(namaSubEventBaru);
  console.log(`✅ Sub Event dihapus: ${namaSubEventBaru}`);

  console.log('\n🎉 CRUD Sub Event SELESAI!');
});
