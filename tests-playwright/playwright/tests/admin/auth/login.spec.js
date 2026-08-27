import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';

test('Login Admin - full flow', async ({ page }) => {

  // 1. Buka landing page
  await page.goto(BASE_URL);
  await expect(page.locator('h1')).toContainText('RUMAHINOVASI');
  console.log('✅ Landing page terbuka');

  // 2. Klik Login
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await expect(page.locator('h1')).toContainText('Masuk');
  console.log('✅ Redirect ke halaman login berhasil');

  // 3. Isi form login
  await page.fill('input[name="email"]', 'admin@admin.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');

  // 4. Tunggu redirect ke dashboard admin
  await page.waitForURL(`${BASE_URL}/index`);
  await expect(page.locator('body')).toContainText('Selamat Datang');
  console.log('✅ Login Admin berhasil, masuk ke dashboard');

  // 5. Screenshot dashboard
  await page.screenshot({ path: 'screenshots/admin-dashboard.png' });
  console.log('✅ Screenshot dashboard tersimpan');

});
