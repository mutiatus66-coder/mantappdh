import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests-playwright/playwright/tests',
  timeout: 600000,
  fullyParallel: false,
  workers: 1,
  retries: 0,

  use: {
    baseURL: 'http://localhost:8000',
    headless: false,
    viewport: { width: 1280, height: 720 },
  },

  // 🔥 CUMA CHROMIUM - HAPUS FIREFOX & WEBKIT
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
