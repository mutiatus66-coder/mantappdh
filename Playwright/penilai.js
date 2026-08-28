import { chromium } from 'playwright';

(async () => {
    // Setup Playwright
    const browser = await chromium.launch({ headless: false, args: ['--start-maximized'] });
    const context = await browser.newContext({
        viewport: null
    });
    const page = await context.newPage();

    const baseUrl = "http://127.0.0.1:8000";
    const emailPenilai = "ahmad.fauzi@example.com";
    const passwordPenilai = "password";

    try {
        console.log("1. Membuka landing page...");
        await page.goto(baseUrl);

        console.log("2. Menekan tombol Login...");
        await page.click('text="Login"');

        console.log("3. Memasukkan kredensial login penilai...");
        await page.fill('input[name="email"]', emailPenilai);
        await page.fill('input[name="password"]', passwordPenilai);
        
        console.log("   -> Menekan tombol Masuk...");
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);
        await page.screenshot({ path: 'debug_after_login.png' });
        
        console.log("4. Ke halaman Riwayat melalui sidebar...");
        await page.click('a:has-text("Riwayat")');

        console.log("5. Menekan Lihat Usulan...");
        await page.click('text="Lihat Usulan"');

        console.log("6. Menggunakan search Datatables di Riwayat Usulan...");
        await page.fill('.dt-search input', 'Diskominfo');
        await page.waitForTimeout(1000);

        console.log("7. Menekan interaksi + dari Datatables...");
        try {
            const dtrControl = page.locator('td.dt-control, td.dtr-control').first();
            if (await dtrControl.isVisible()) {
                await dtrControl.click();
                await page.waitForTimeout(1000);
            }
        } catch(e) {
            console.log("   -> Tombol + tidak ditemukan");
        }

        console.log("8. Menekan tombol Kembali...");
        await page.click('text="Kembali"');

        console.log("9. Ke halaman Rekap Nilai melalui sidebar...");
        await page.click('a:has-text("Rekap Nilai")');

        console.log("10. Menekan Lihat Nilai...");
        await page.click('text="Lihat Nilai"');

        console.log("11. Menggunakan search Datatables di Rekap Nilai...");
        await page.fill('.dt-search input', 'Diskominfo');
        await page.waitForTimeout(1000);

        console.log("12. Menekan interaksi + dari Datatables...");
        try {
            const dtrControl = page.locator('td.dt-control, td.dtr-control').first();
            if (await dtrControl.isVisible()) {
                await dtrControl.click();
                await page.waitForTimeout(1000);
            }
        } catch(e) {
            console.log("   -> Tombol + tidak ditemukan");
        }

        console.log("13. Menekan tombol Kembali...");
        await page.click('text="Kembali"');

        console.log("14. Ke halaman Penilaian Tahap 1 melalui sidebar...");
        await page.click('a:has-text("Penilaian Tahap 1")');

        console.log("15. Menekan Lihat Nilai Verifikasi...");
        await page.click('text="Lihat Nilai Verifikasi"');
        await page.waitForTimeout(1000);

        console.log("16. Memberi nilai kepada inovator...");
        const btnNilai = page.locator('.btn-input-nilai');
        const btnCatatan = page.locator('.btn-catatan');
        
        const count = await btnNilai.count();
        const limit = Math.min(7, count); // Kita sesuaikan 7 sesuai MAX_LOLOS yang baru
        
        for (let i = 0; i < limit; i++) {
            await btnNilai.nth(i).scrollIntoViewIfNeeded();
            await btnNilai.nth(i).click();
            await page.waitForTimeout(500);
            
            const inputs = page.locator('.input-nilai-item');
            const inputsCount = await inputs.count();
            for (let j = 0; j < inputsCount; j++) {
                if (await inputs.nth(j).isVisible()) {
                    await inputs.nth(j).fill('10');
                }
            }
            await page.locator('button:has-text("Simpan Nilai"):visible').first().click();
            await page.waitForTimeout(1000);
            
            await btnCatatan.nth(i).scrollIntoViewIfNeeded();
            await btnCatatan.nth(i).click();
            await page.waitForTimeout(500);
            
            const textareas = page.locator('textarea.form-control');
            const taCount = await textareas.count();
            for (let k = 0; k < taCount; k++) {
                if (await textareas.nth(k).isVisible()) {
                    await textareas.nth(k).fill('Catatan otomatis dari Playwright untuk inovator ke-' + (i+1));
                    break;
                }
            }
            await page.locator('button:has-text("Simpan Catatan"):visible').first().click();
            await page.waitForTimeout(1000);
        }

        console.log("17. Filter Total Nilai (Klik header Total Nilai)...");
        const thTotalNilai = page.locator('th', { hasText: 'Total Nilai' }).first();
        await thTotalNilai.scrollIntoViewIfNeeded();
        await thTotalNilai.click();
        await page.waitForTimeout(1000);
        await thTotalNilai.click();
        await page.waitForTimeout(1000);

        console.log("18. Menekan check box select all (chk-all)...");
        const chkAll = page.locator('.chk-all').first();
        await chkAll.scrollIntoViewIfNeeded();
        await chkAll.click();
        await page.waitForTimeout(1000);

        console.log("19. Menekan tombol Simpan di Tahap 1...");
        const btnSimpanT1 = page.locator('.btn-rv-simpan').first();
        await btnSimpanT1.scrollIntoViewIfNeeded();
        await btnSimpanT1.click();
        await page.waitForTimeout(2000);

        console.log("20. Menekan tombol Kembali...");
        await page.click('text="Kembali"');

        console.log("21. Ke halaman Penilaian Tahap 2 melalui sidebar...");
        await page.click('a:has-text("Penilaian Tahap 2")');

        console.log("22. Menekan Lihat Nilai Nominator...");
        await page.click('text="Lihat Nilai Nominator"');
        await page.waitForTimeout(1000);

        console.log("23. Menekan tombol Ranking...");
        await page.click('button:has-text("Ranking")');
        await page.waitForTimeout(1000);

        console.log("24. Menekan tombol Simpan Ranking...");
        await page.click('button:has-text("Simpan Ranking")');
        await page.waitForTimeout(2000);

        console.log("24.5. Menekan tombol Download Excel...");
        const btnExcelTahap2 = page.locator('.buttons-excel');
        if (await btnExcelTahap2.count() > 0 && await btnExcelTahap2.first().isVisible()) {
            await btnExcelTahap2.first().click();
            await page.waitForTimeout(2000);
        } else {
            console.log("   -> Tombol Excel tidak ditemukan");
        }

        console.log("25. Menekan tombol Kembali...");
        await page.click('text="Kembali"');

        console.log("26. Ke halaman Rekap Nilai...");
        await page.click('a:has-text("Rekap Nilai")');

        console.log("27. Menekan tombol Lihat Nilai (kembali ke Rekap Pendaftar)...");
        await page.click('text="Lihat Nilai"');
        await page.waitForTimeout(2000);

        console.log("28. Mencoba export PDF...");
        const btnPdf = page.locator('.buttons-pdf');
        if (await btnPdf.count() > 0 && await btnPdf.first().isVisible()) {
            await btnPdf.first().click();
            await page.waitForTimeout(1000);
        } else {
            console.log("   -> Tombol PDF tidak ditemukan");
        }

        console.log("29. Mencoba export Excel...");
        const btnExcel = page.locator('.buttons-excel');
        if (await btnExcel.count() > 0 && await btnExcel.first().isVisible()) {
            await btnExcel.first().click();
            await page.waitForTimeout(1000);
        } else {
            console.log("   -> Tombol Excel tidak ditemukan");
        }

        console.log("30. Menekan tombol Kembali...");
        await page.click('text="Kembali"');

        console.log("31. Log Out...");
        await page.click('.cursor-pointer.symbol');
        await page.waitForTimeout(1000);
        await page.click('text="Sign Out"');
        await page.waitForTimeout(2000);

        console.log("✅ Workflow Penilai E2E (Playwright) Selesai dengan Sukses!");

    } catch (err) {
        console.error("❌ Terjadi kesalahan:", err);
    } finally {
        await browser.close();
    }
})();
