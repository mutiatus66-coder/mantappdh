import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';

test('Debug Peserta', async ({ page }) => {

  await page.goto(BASE_URL);
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await page.fill('input[name="email"]', 'peserta1@inovasi.test');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/index`);
  console.log('✅ Login Peserta berhasil');

  await page.goto(`${BASE_URL}/inovasi/riwayat`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  await page.screenshot({ path: 'screenshots/debug-peserta.png', fullPage: true });

  // Semua link
  const allLinks = await page.locator('a').evaluateAll(els =>
    els.map(el => ({ text: el.textContent.trim().substring(0, 50), href: el.href }))
  );
  console.log('📌 SEMUA LINK:');
  allLinks.forEach(l => console.log(`   "${l.text}" -> ${l.href}`));

  // Semua button
  const allButtons = await page.locator('button').allTextContents();
  console.log('📌 SEMUA BUTTON:', allButtons.filter(t => t.trim()));
});
