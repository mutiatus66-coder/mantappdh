import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';

test('Debug Penilai', async ({ page }) => {

  await page.goto(BASE_URL);
  await page.click('a.btn-login:has-text("Login")');
  await page.waitForURL(`${BASE_URL}/sign-in`);
  await page.fill('input[name="email"]', 'ahmad.fauzi@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL(`${BASE_URL}/index`);
  console.log('✅ Login Penilai berhasil');

  // Cek Penilaian Tahap 1
  await page.goto(`${BASE_URL}/penilaian/tahap-1`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  await page.screenshot({ path: 'screenshots/debug-penilai-tahap1.png', fullPage: true });

  const allLinks = await page.locator('a').evaluateAll(els =>
    els.map(el => ({ text: el.textContent.trim().substring(0, 50), href: el.href }))
  );
  console.log('📌 LINK DI PENILAIAN TAHAP 1:');
  allLinks.forEach(l => console.log(`   "${l.text}" -> ${l.href}`));

  // Cek Penilaian Tahap 2
  await page.goto(`${BASE_URL}/penilaian/tahap-2`);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(3000);

  await page.screenshot({ path: 'screenshots/debug-penilai-tahap2.png', fullPage: true });

  const allLinks2 = await page.locator('a').evaluateAll(els =>
    els.map(el => ({ text: el.textContent.trim().substring(0, 50), href: el.href }))
  );
  console.log('📌 LINK DI PENILAIAN TAHAP 2:');
  allLinks2.forEach(l => console.log(`   "${l.text}" -> ${l.href}`));
});

