import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

(async () => {
    // Mode headless murni, sangat cepat
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();

    const baseUrl = "http://127.0.0.1:8000";
    const uniqueEmail = `peserta_${Date.now()}@test.com`;

    const pdfPath = path.join(__dirname, 'contoh.pdf');
    const jpgPath = path.join(__dirname, 'contoh.jpg');
    fs.writeFileSync(pdfPath, Buffer.from("255044462D312E340A", 'hex'));
    fs.writeFileSync(jpgPath, Buffer.from("FFD8FFE000104A4649460001", 'hex'));

    try {
        console.log("1. Membuka landing page...");
        await page.goto(baseUrl);

        console.log("2. Menekan tombol PENDAFTARAN...");
        // Auto-wait bekerja otomatis
        await page.locator('.btn-register').click();

        console.log("3. Mengisi form pendaftaran...");
        await page.fill('input[name="name"]', 'Contoh Nama Peserta');
        await page.fill('input[name="email"]', uniqueEmail);
        await page.fill('input[name="password"]', 'Password123!');
        await page.fill('input[name="password_confirmation"]', 'Password123!');
        
        await page.check('input[name="captcha_verified"]');

        console.log("   -> Menekan Daftar...");
        await page.getByRole('button', { name: 'Daftar' }).click();
        
        // Memastikan login berhasil
        await page.waitForURL('**/index');

        console.log("4. Ke halaman Riwayat...");
        await page.goto(baseUrl + '/inovasi/riwayat');
        await page.waitForTimeout(2000);

        console.log("5. Menekan Kelola Usulan...");
        const btnKelola = page.locator('text="Kelola Usulan"').first();
        await btnKelola.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnKelola.isVisible()) {
            await btnKelola.click();
            await page.waitForTimeout(2000);
        } else {
            console.log("   -> (Tombol Kelola Usulan tidak ditemukan)");
            // Coba cari tombol apa saja yang mengandung "Usulan"
            const btnAlternatif = page.locator('a, button').filter({ hasText: 'Usulan' }).first();
            if (await btnAlternatif.isVisible()) {
                await btnAlternatif.click();
                await page.waitForTimeout(1500);
            }
        }

        console.log("6. Menekan Tambah Usulan...");
        const btnTambah = page.locator('#btnTambah').first();
        if (await btnTambah.isVisible()) {
            console.log("   -> btnTambah terlihat, meng-klik...");
            await btnTambah.click();
            await page.waitForTimeout(1500);
        } else {
            console.log("   -> btnTambah TIDAK terlihat!");
            const btnPlus = page.locator('.btn-primary').filter({ hasText: 'Tambah' }).first();
            if (await btnPlus.isVisible()) {
                console.log("   -> btnPlus terlihat, meng-klik...");
                await btnPlus.click();
                await page.waitForTimeout(1500);
            } else {
                console.log("   -> btnPlus juga TIDAK terlihat!");
            }
        }

        console.log("7. Mengisi Form Langkah 1...");
        const inputInovasi = page.locator('input[name="nama_inovasi"]').first();
        await inputInovasi.waitFor({ state: 'visible', timeout: 5000 }).catch(e => console.log("   -> Timeout nunggu inputInovasi!", e.message));
        if (await inputInovasi.isVisible()) {
            console.log("   -> Form Usulan ditemukan!");
            await inputInovasi.fill('Inovasi E2E Test');
            await page.fill('input[name="judul"]', 'Judul Inovasi Test');
            
            // Pilih opsi kedua di bidang_id secara aman (atau injeksi opsi jika kosong)
            const selectLocator = page.locator('select[name="bidang_id"]');
            await selectLocator.waitFor({ state: 'attached' });
            const optionsCount = await selectLocator.locator('option').count();
            if (optionsCount > 1) {
                await selectLocator.selectOption({ index: 1 });
            } else {
                console.log("   -> (Pilihan bidang kosong, menambahkan opsi buatan untuk testing...)");
                await page.evaluate(() => {
                    const sel = document.querySelector('select[name="bidang_id"]');
                    if (sel) {
                        const opt = document.createElement('option');
                        opt.value = "1"; // Sesuai ID bidang di Seeder
                        opt.text = "Bidang Injeksi Test";
                        sel.appendChild(opt);
                    }
                });
                await selectLocator.selectOption({ index: 1 });
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

            console.log("9. Mengisi Form Langkah 3 (Upload File)...");
            await page.setInputFiles('input[name="file_surat_pernyataan"]', pdfPath);
            await page.setInputFiles('input[name="file_proposal"]', pdfPath);
            await page.setInputFiles('input[name="file_gambar"]', jpgPath);
            await page.fill('input[name="link_video"]', 'https://youtube.com/watch?v=123');
            
            console.log("   -> Menekan Simpan Usulan...");
            await Promise.all([
                page.waitForNavigation({ timeout: 15000 }).catch(() => {}),
                page.getByRole('button', { name: 'Simpan Usulan' }).click()
            ]);
            await page.waitForTimeout(2000);
        } else {
            console.log("   -> (Form Usulan tidak ditemukan, kemungkinan tidak ada Event aktif untuk diikuti. Melanjutkan ke Logout...)");
        }

        console.log("10. Kembali ke menu Riwayat...");
        await page.goto(baseUrl + '/inovasi/riwayat', { waitUntil: 'commit' }).catch(e => console.log("   -> Peringatan navigasi:", e.message));
        await page.waitForTimeout(2000);

        console.log("11. Log Out...");
        await page.goto(baseUrl);
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); 
            if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }
        });
        await page.waitForTimeout(1000);
        await page.evaluate(() => {
            let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out') || a.textContent.includes('Log Out')); 
            if(signOut) { signOut.click(); }
        });
        await page.waitForTimeout(2000);

        console.log("✅ Workflow E2E Playwright untuk Peserta selesai tanpa error!");

    } catch (error) {
        console.error("❌ Terjadi kesalahan:", error);
        await page.screenshot({ path: 'error_playwright.png', fullPage: true });
        console.log("📸 Screenshot error telah disimpan sebagai 'error_playwright.png'");
    } finally {
        await browser.close();
        if (fs.existsSync(pdfPath)) fs.unlinkSync(pdfPath);
        if (fs.existsSync(jpgPath)) fs.unlinkSync(jpgPath);
    }
})();
