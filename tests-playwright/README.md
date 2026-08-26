# E2E Tests (Playwright)

Folder ini terpisah dari `tests/Browser` (Laravel Dusk) yang sudah ada — keduanya
boleh jalan berdampingan, tidak saling mengganggu.

## Setup (sekali saja)

```bash
npm install
npx playwright install        # download browser Chromium/Firefox/WebKit
php artisan migrate:fresh --seed   # bikin user login, lihat fixtures.js
```

Server Laravel harus sudah jalan sendiri (mis. lewat Herd/Valet di
`http://mantappdh.test`, sesuai `APP_URL` di `.env`) — `playwright.config.js`
belum mengaktifkan `webServer` otomatis. Kalau base URL kamu berbeda, override
lewat env var:

```bash
PLAYWRIGHT_BASE_URL=http://mantappdh.test npm run test:e2e
```

## Menjalankan test

```bash
npm run test:e2e          # semua test, headless
npm run test:e2e:ui       # mode UI interaktif, enak buat debug
npm run test:e2e:headed   # browser kelihatan
npm run test:e2e:report   # buka laporan HTML hasil run terakhir
```

## Struktur

- `fixtures.js` — kredensial user seeder (`USERS`), helper `login()`, dan
  fixture `authedPage` (page yang sudah login sebagai admin) supaya test
  berikutnya tidak perlu mengulang langkah login manual.
- `login.spec.js` — test original `user can login` tetap dipertahankan, plus
  tambahan: login peserta & penilai, login gagal (password salah / email tidak
  terdaftar), proteksi route saat belum login, dan logout.

## Menambah test baru

Untuk test yang butuh precondition "sudah login", pakai fixture `authedPage`:

```js
import { test, expect } from './fixtures';

test('admin bisa lihat daftar event', async ({ authedPage }) => {
  await authedPage.goto('/event');
  await expect(authedPage.locator('h1')).toContainText('Event');
});
```
