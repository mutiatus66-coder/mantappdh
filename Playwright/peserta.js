import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Karena menggunakan ES Module, kita buat __dirname secara manual
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

(async () => {
    // Setup Playwright
    // Set headless: true jika ingin berjalan di latar belakang (tanpa UI)
    const browser = await chromium.launch({ headless: false }); 
    const context = await browser.newContext();
    const page = await context.newPage();

    const baseUrl = "http://mantappdh.test";
    const uniqueEmail = `peserta_${Date.now()}@test.com`;

    // Helpers untuk membuat file dummy (PDF & JPG)
    const pdfPath = path.join(__dirname, 'contoh.pdf');
    const jpgPath = path.join(__dirname, 'contoh.jpg');
    fs.writeFileSync(pdfPath, Buffer.from("255044462D312E340A", 'hex'));
    fs.writeFileSync(jpgPath, Buffer.from("FFD8FFE000104A4649460001", 'hex'));

    try {
        console.log("1. Membuka landing page...");
        await page.goto(baseUrl);
        await page.waitForTimeout(1000);

        console.log("2. Menekan tombol PENDAFTARAN...");
        await page.locator('.btn-register').click();
        await page.waitForTimeout(1000);

        console.log("3. Mengisi form pendaftaran...");
        await page.fill('input[name="name"]', 'Peserta Baru Real');
        await page.fill('input[name="email"]', uniqueEmail);
        await page.fill('input[name="password"]', 'Password123!');
        await page.fill('input[name="password_confirmation"]', 'Password123!');
        
        // Centang captcha
        await page.check('input[name="captcha_verified"]');
        await page.waitForTimeout(2000); // Tunggu animasi

        console.log("   -> Menekan Daftar...");
        // Menggunakan locators modern Playwright untuk mencari tombol berdasarkan Role dan Nama
        await page.getByRole('button', { name: 'Daftar', exact: true }).click();
        await page.waitForTimeout(3000);

        console.log("4. Ke halaman Riwayat melalui sidebar...");
        await page.locator('a:has-text("Riwayat")').first().click();
        await page.waitForTimeout(2000);

        console.log("5. Menekan Kelola Usulan...");
        await page.locator('a:has-text("Kelola Usulan") >> visible=true').first().click();
        await page.waitForTimeout(2000);

        console.log("6. Menekan Tambah Usulan...");
        await page.getByRole('button', { name: 'Tambah Usulan' }).click();
        await page.waitForTimeout(1000);

        console.log("7. Mengisi Form Langkah 1...");
        await page.fill('input[name="nama_inovasi"]', 'Inovasi E2E Test');
        await page.fill('input[name="judul"]', 'Judul Inovasi Test');
        
        // Pilih opsi kedua di bidang_id (index 1) karena opsi pertama biasanya placeholder
        const bidangOptions = await page.locator('select[name="bidang_id"] option').all();
        if (bidangOptions.length > 1) {
            const valueToSelect = await bidangOptions[1].getAttribute('value');
            await page.selectOption('select[name="bidang_id"]', valueToSelect);
        }
        
        await page.fill('input[name="interaksi"]', 'Aplikasi Web');
        await page.selectOption('select[name="kategori"]', 'umum');
        
        await page.fill('input[name="inovator"]', 'Instansi Test');
        await page.fill('input[name="ketua_nama"]', 'Budi Test');
        await page.fill('input[name="ketua_email"]', uniqueEmail);
        await page.fill('input[name="ketua_wa"]', '081234567890');
        await page.fill('input[name="alamat_ketua"]', 'Jl. Test No. 123');
        await page.fill('input[name="ktp"]', '1234567890123456');
        
        await page.getByRole('button', { name: 'Selanjutnya' }).click();
        await page.waitForTimeout(1000);

        console.log("8. Mengisi Form Langkah 2...");
        await page.locator('[name="latar_belakang"]').fill('Latar Belakang ...');
        await page.locator('[name="kondisi_sebelumnya"]').fill('Kondisi Sebelumnya ...');
        await page.locator('[name="sasaran_tujuan"]').fill('Sasaran Tujuan ...');
        await page.locator('[name="deskripsi"]').fill('Deskripsi ...');
        await page.locator('[name="cara_kerja"]').fill('Cara Kerja ...');
        await page.locator('[name="keunggulan"]').fill('Keunggulan ...');
        await page.locator('[name="hasil_diharapkan"]').fill('Hasil yang Diharapkan ...');
        await page.locator('[name="manfaat"]').fill('Manfaat ...');
        await page.locator('[name="rencana_berkelanjutan"]').fill('Rencana Berkelanjutan ...');
        
        await page.getByRole('button', { name: 'Selanjutnya' }).click();
        await page.waitForTimeout(1000);

        console.log("9. Mengisi Form Langkah 3 (Upload File)...");
        await page.setInputFiles('input[name="file_surat_pernyataan"]', pdfPath);
        await page.setInputFiles('input[name="file_proposal"]', pdfPath);
        await page.setInputFiles('input[name="file_gambar"]', jpgPath);
        await page.fill('input[name="link_video"]', 'https://youtube.com/watch?v=123');
        await page.waitForTimeout(2000);
        
        console.log("   -> Menekan Simpan Usulan...");
        await page.getByRole('button', { name: 'Simpan Usulan' }).click();
        await page.waitForTimeout(3000);

        console.log("10. Kembali ke menu Riwayat...");
        await page.locator('a:has-text("Kembali")').first().click({ force: true });
        await page.waitForTimeout(2000);

        console.log("11. Log Out...");
        // Avatar symbol bisa di klik langsung via class
        await page.locator('.cursor-pointer.symbol').first().click({ force: true });
        await page.waitForTimeout(1000);
        await page.getByRole('link', { name: 'Sign Out' }).click({ force: true });
        await page.waitForTimeout(2000);

        console.log("✅ Testing Playwright Selesai dengan Sukses!");

    } catch (error) {
        console.error("❌ Terjadi kesalahan:", error);
        await page.screenshot({ path: 'error_playwright.png', fullPage: true });
        console.log("📸 Screenshot error telah disimpan sebagai 'error_playwright.png'");
    } finally {
        await browser.close();
        // Hapus dummy files
        if (fs.existsSync(pdfPath)) fs.unlinkSync(pdfPath);
        if (fs.existsSync(jpgPath)) fs.unlinkSync(jpgPath);
    }
})();
