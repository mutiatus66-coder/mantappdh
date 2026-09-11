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

console.log("MEMULAI PLAYWRIGHT SEBAGAI ADMIN MENYELURUH");

for (const test of tests) {
    console.log(`\n▶️  Menjalankan: ${test}...`);
    try {
        execSync(`node ${path.join(__dirname, 'Admin', test)}`, { stdio: 'inherit' });
    } catch (err) {
        console.error(`\n❌ Pengujian ${test} GAGAL! Menghentikan eksekusi selanjutnya.`);
        process.exit(1);
    }
}

console.log("Workflow E2E Playwright untuk Admin selesai tanpa error!");