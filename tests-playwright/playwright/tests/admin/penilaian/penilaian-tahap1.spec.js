import { test, expect } from '@playwright/test';

const BASE_URL = 'http://localhost:8000';
const ADMIN_EMAIL = 'admin@admin.com';
const ADMIN_PASSWORD = 'password';

test.describe('Penilaian Tahap 1 - Admin', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE_URL}/sign-in`);
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/index`);
    await page.goto(`${BASE_URL}/penilaian/tahap-1`);
  });

  test('1. Halaman Penilaian Tahap 1 terbuka', async ({ page }) => {
    await expect(page.locator('h3').filter({ hasText: 'Penilaian Tahap 1' })).toBeVisible();
    console.log('✅ Halaman Penilaian Tahap 1 terbuka');
  });

});
