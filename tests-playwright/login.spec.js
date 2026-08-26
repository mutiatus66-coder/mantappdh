import { test, expect, login, USERS } from './fixtures';

test.describe('Login', () => {
  test('user can login', async ({ page }) => {
    await login(page, { email: 'admin@bapperida.test', password: 'password' });

    // Tunggu redirect selesai
    await page.waitForURL('**/index');

    await expect(page).toHaveURL(/.*index/);
  });

  test('peserta bisa login', async ({ page }) => {
    await login(page, USERS.peserta);
    await expect(page).toHaveURL(/\/index/);
  });

  test('penilai bisa login', async ({ page }) => {
    await login(page, USERS.penilai);
    await expect(page).toHaveURL(/\/index/);
  });

  test('gagal login dengan password salah menampilkan pesan error', async ({ page }) => {
    await login(page, { email: USERS.admin.email, password: 'password-salah' });

    // Auth::attempt gagal -> back()->withErrors(...), tetap di halaman sign-in
    await expect(page).toHaveURL(/\/sign-in/);
    await expect(page.getByText('Email atau password salah.')).toBeVisible();
  });

  test('gagal login dengan email yang tidak terdaftar', async ({ page }) => {
    await login(page, { email: 'tidak-ada@inovasi.test', password: 'password' });

    await expect(page).toHaveURL(/\/sign-in/);
    await expect(page.getByText('Email atau password salah.')).toBeVisible();
  });

  test('halaman terproteksi (/index) redirect ke login jika belum login', async ({ page }) => {
    await page.goto('/index');

    await expect(page).toHaveURL(/\/(login|sign-in)/);
  });

  test('bisa logout setelah login', async ({ authedPage }) => {
    // authedPage sudah login sebagai admin (lihat fixtures.js)

    // Dropdown avatar (KTUI) sering flaky di headless browser karena
    // animasinya sendiri (butuh timing yang beda-beda tiap browser).
    // Daripada gantungan sama UI dropdown, kita submit langsung form
    // tersembunyi yang sama persis yang dipicu tombol "Sign Out"
    // (lihat resources/views/partials/header.blade.php) — tetap
    // memvalidasi perilaku logout beneran (POST /logout + CSRF),
    // cuma gak rapuh soal timing animasi menu.
    // locator().evaluate() otomatis nunggu elemen #logout-form beneran ada
    // di DOM dulu sebelum dieksekusi — beda dari page.evaluate() polos yang
    // langsung jalan meski render halaman belum tentu selesai (race condition
    // yang bikin Firefox/WebKit kadang dapet null padahal Chromium lolos).
    await authedPage.locator('#logout-form').evaluate((form) => {
      form.submit();
    });

    // AuthController@logout redirect ke '/' (landing), bukan ke /login
    await expect(authedPage).toHaveURL(/\/$/);

    // Validasi sebenarnya: session sudah invalid, jadi halaman terproteksi
    // harus tetap terkunci dan melempar balik ke login.
    await authedPage.goto('/index');
    await expect(authedPage).toHaveURL(/\/(login|sign-in)/);
  });
});