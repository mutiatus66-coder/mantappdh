import { execSync } from 'child_process';
import { fileURLToPath } from 'url';
import path from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const tests = [
    'Test1_MasterTest.js',
    'Test2_IndikatorTest.js',
    'Test3_InovasiTest.js',
    'Test4_PenilaianTest.js'
];

console.log("Mulai menjalankan seluruh bagian E2E Admin (Playwright)...");

for (const test of tests) {
    console.log(`\n▶️  Menjalankan: ${test}...`);
    try {
        execSync(`node ${path.join(__dirname, 'Admin', test)}`, { stdio: 'inherit' });
    } catch (err) {
        console.error(`\n❌ Pengujian ${test} GAGAL! Menghentikan eksekusi selanjutnya.`);
        await page.screenshot({ path: 'playwright/Error/admin_playwright.png', fullPage: true });
        console.log("📸 Screenshot error telah disimpan sebagai 'admin_playwright.png'");
        process.exit(1);
    }
}

console.log("✅ Workflow Seluruh Bagian E2E Admin (Playwright) Selesai dengan Sukses!");
