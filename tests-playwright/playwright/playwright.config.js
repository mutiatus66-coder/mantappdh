import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests',
  timeout: 30000,
  fullyParallel: true,
  retries: 2,

  use: {
    baseURL: 'http://localhost:8000',
    headless: false,  // 🔥 INI AGAR BROWSER TERBUKA
    viewport: { width: 1280, height: 720 },
    trace: 'on-first-retry',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});

