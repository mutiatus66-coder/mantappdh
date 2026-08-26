import { test as base, expect } from '@playwright/test';

/**
 * Kredensial user dari database/seeders/UserSeeder.php + DatabaseSeeder.php.
 * Jalankan `php artisan migrate:fresh --seed` dulu supaya user ini ada.
 * Semua password default = "password".
 */
export const USERS = {
  admin: {
    email: 'admin@bapperida.test',
    password: 'password',
    role: 'admin_bapperida',
  },
  penilai: {
    email: 'penilai1@inovasi.test',
    password: 'password',
    role: 'penilai',
  },
  peserta: {
    email: 'peserta1@inovasi.test',
    password: 'password',
    role: 'peserta',
  },
};

/** Login lewat form UI di /sign-in (bukan lewat API), sesuai alur user asli. */
export async function login(page, { email, password }) {
  await page.goto('/sign-in');
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await page.click('#kt_sign_in_submit');
}

/**
 * Navigasi ke halaman "Usulan Inovasi" (/inovasi/usulan/{id}) milik sub event
 * PERTAMA yang tersedia di /inovasi/riwayat — dicari secara dinamis lewat UI
 * (bukan hardcode ID), supaya test tetap jalan berapa pun/apapun data seeder-nya.
 * Melempar error yang jelas kalau belum ada sub event sama sekali.
 */
export async function gotoFirstUsulanPage(page) {
  await page.goto('/inovasi/riwayat');
  const firstCard = page.getByRole('link', { name: /Kelola Usulan/i }).first();
  await expect(firstCard, 'Tidak ada sub event tersedia di /inovasi/riwayat — pastikan SubEventSeeder sudah jalan.').toBeVisible();
  await firstCard.click();
  await expect(page).toHaveURL(/\/inovasi\/usulan\/\d+/);
}

/**
 * Fixture `authedPage`: page yang sudah login sebagai admin_bapperida.
 */
export const test = base.extend({
  authedPage: async ({ page }, use) => {
    await login(page, USERS.admin);
    await expect(page).toHaveURL(/\/index/);
    await use(page);
  },

  /** Page yang sudah login sebagai peserta. */
  pesertaPage: async ({ page }, use) => {
    await login(page, USERS.peserta);
    await expect(page).toHaveURL(/\/index/);
    await use(page);
  },
});

export { expect };