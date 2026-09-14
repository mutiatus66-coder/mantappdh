import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();

    const baseUrl = "http://mantappdh.test";
    const emailPenilai = "ahmad.fauzi@example.com";
    const passwordPenilai = "password";

    try {
        console.log("1. Membuka landing page...");
        await page.goto(baseUrl);

        console.log("2. Menekan tombol Login...");
        await page.locator('.btn-login').first().click();

        console.log("3. Memasukkan kredensial login penilai...");
        await page.locator('input[name="email"]').fill(emailPenilai);
        await page.locator('input[name="password"]').fill(passwordPenilai);
        
        console.log("   -> Menekan tombol Masuk...");
        await page.locator('button[type="submit"]').click();
        await page.waitForURL('**/index');
        
        console.log("4. Ke halaman Riwayat melalui sidebar...");
        await page.goto(baseUrl + '/inovasi/riwayat');
        await page.waitForTimeout(2000);

        console.log("5. Menekan Lihat Usulan...");
        const btnLihatUsulan = page.getByRole('link', { name: /Lihat Usulan/i }).first();
        await btnLihatUsulan.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnLihatUsulan.isVisible()) {
            await btnLihatUsulan.click();

            console.log("6. Menggunakan search Datatables di Riwayat Usulan...");
            const searchRiwayat = page.locator('.dt-search input');
            if (await searchRiwayat.isVisible()) {
                await searchRiwayat.fill('inovasi');
                await page.waitForTimeout(1000);
            }

            console.log("7. Menekan interaksi + dari Datatables...");
            const btnPlus = page.locator('.dtr-control').first();
            if (await btnPlus.isVisible()) {
                await btnPlus.click();
                await page.waitForTimeout(500);
            }

            console.log("8. Menekan tombol Kembali...");
            await page.getByRole('link', { name: /Kembali/i }).first().click();
        } else {
            console.log("   -> (Tombol Lihat Usulan tidak ditemukan)");
        }

        console.log("9. Ke halaman Rekap Nilai melalui sidebar...");
        await page.goto(baseUrl + '/inovasi/rekap-nilai');
        await page.waitForTimeout(2000);

        console.log("10. Menekan Lihat Nilai...");
        const btnLihatNilai2 = page.getByRole('link', { name: /Lihat Nilai/i }).first();
        await btnLihatNilai2.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnLihatNilai2.isVisible()) {
            await btnLihatNilai2.click();

            console.log("11. Menggunakan search Datatables di Rekap Nilai...");
            const searchRekap = page.locator('.dt-search input');
            if (await searchRekap.isVisible()) {
                await searchRekap.fill('inovasi');
                await page.waitForTimeout(1000);
            }

            console.log("12. Menekan interaksi + dari Datatables...");
            const btnPlusRekap = page.locator('.dtr-control').first();
            if (await btnPlusRekap.isVisible()) {
                await btnPlusRekap.click();
                await page.waitForTimeout(500);
            }

            console.log("13. Menekan tombol Kembali...");
            await page.getByRole('link', { name: /Kembali/i }).first().click();
        } else {
            console.log("   -> (Tombol Lihat Nilai tidak ditemukan)");
        }

        console.log("14. Ke halaman Penilaian Tahap 1 melalui sidebar...");
        await page.goto(baseUrl + '/penilaian/tahap-1');
        await page.waitForTimeout(2000);

        console.log("15. Menekan Lihat Nilai Verifikasi...");
        const btnVerifikasi = page.getByRole('link', { name: /Lihat Nilai Verifikasi/i }).first();
        await btnVerifikasi.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnVerifikasi.isVisible()) {
            await btnVerifikasi.click();

            console.log("16. Memberi nilai kepada inovator...");
            const btnNilai = page.locator('.tab-pane.active .btn-input-nilai');
            const btnCatatan = page.locator('.tab-pane.active .btn-catatan');
            
            await btnNilai.first().waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
            const count = await btnNilai.count();
            const limit = Math.min(7, count); 
            
            for (let i = 0; i < limit; i++) {
                await page.waitForTimeout(1000);
                await btnNilai.nth(i).click();
                
                const modalNilai = page.locator('.modal.show');
                await modalNilai.waitFor({ state: 'visible' });
                const inputs = modalNilai.locator('.input-nilai-item');
                const inputsCount = await inputs.count();
                for (let j = 0; j < inputsCount; j++) {
                    await inputs.nth(j).fill('10');
                }
                await modalNilai.locator('.btn-simpan-nilai-modal').click();
                await page.waitForTimeout(1500);
                await page.locator('.modal.show').waitFor({ state: 'hidden', timeout: 5000 }).catch(() => {});
                
                await btnCatatan.nth(i).click();
                const modalCatatan = page.locator('.modal.show');
                await modalCatatan.waitFor({ state: 'visible' });
                const textareas = modalCatatan.locator('textarea.form-control');
                const taCount = await textareas.count();
                for (let k = 0; k < taCount; k++) {
                    await textareas.nth(k).fill('Catatan otomatis dari Playwright untuk inovator ke-' + (i+1));
                }
                await modalCatatan.locator('.btn-simpan-catatan').click();
                await page.waitForTimeout(1500);
                await page.locator('.modal.show').waitFor({ state: 'hidden', timeout: 5000 }).catch(() => {});
            }

            console.log("17. Filter Total Nilai (Klik header Total Nilai)...");
            const thTotalNilai = page.locator('th', { hasText: 'Total Nilai' }).first();
            if (await thTotalNilai.isVisible()) {
                await thTotalNilai.click();
                await page.waitForTimeout(500);
                await thTotalNilai.click();
            }

            console.log("18. Menekan check box select all (chk-all)...");
            const chkAll = page.locator('.chk-all').first();
            if (await chkAll.isVisible()) {
                await chkAll.click({ force: true });
            }

            console.log("19. Menekan tombol Simpan di Tahap 1...");
            const btnSimpanT1 = page.locator('.btn-rv-simpan').first();
            if (await btnSimpanT1.isVisible()) {
                await btnSimpanT1.click();
                await page.waitForTimeout(1000);
            }

            console.log("20. Menekan tombol Kembali...");
            await page.getByRole('link', { name: /Kembali/i }).first().click();
        } else {
            console.log("   -> (Tombol Lihat Nilai Verifikasi tidak ditemukan)");
        }

        console.log("21. Ke halaman Penilaian Tahap 2 melalui sidebar...");
        await page.goto(baseUrl + '/penilaian/tahap-2');
        await page.waitForTimeout(2000);

        console.log("22. Menekan Lihat Nilai Nominator...");
        const btnNominator = page.getByRole('link', { name: /Lihat Nilai Nominator/i }).first();
        await btnNominator.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnNominator.isVisible()) {
            await btnNominator.click();

            console.log("23. Menekan tombol Ranking...");
            const btnRanking = page.getByRole('button', { name: /Ranking/i }).first();
            if (await btnRanking.isVisible()) await btnRanking.click();

            console.log("24. Menekan tombol Simpan Ranking...");
            const btnSimpanRanking = page.getByRole('button', { name: /Simpan Ranking/i }).first();
            if (await btnSimpanRanking.isVisible()) await btnSimpanRanking.click();

            console.log("25. Menekan tombol Download Excel...");
            const btnExcelTahap2 = page.locator('.buttons-excel').first();
            if (await btnExcelTahap2.isVisible()) await btnExcelTahap2.click();

            console.log("26. Menekan tombol Kembali...");
            await page.getByRole('link', { name: /Kembali/i }).first().click();
        } else {
            console.log("   -> (Tombol Lihat Nilai Nominator tidak ditemukan)");
        }

        console.log("27. Ke halaman Rekap Nilai...");
        await page.goto(baseUrl + '/inovasi/rekap-nilai');
        await page.waitForTimeout(2000);

        console.log("28. Menekan tombol Lihat Nilai (kembali ke Rekap Pendaftar)...");
        const btnLihatNilai = page.getByRole('link', { name: /Lihat Nilai/i }).first();
        await btnLihatNilai.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnLihatNilai.isVisible()) {
            await btnLihatNilai.click();

            console.log("29. Mencoba export PDF...");
            const btnPdf = page.locator('.buttons-pdf').first();
            if (await btnPdf.isVisible()) await btnPdf.click();

            console.log("30. Mencoba export Excel...");
            const btnExcel = page.locator('.buttons-excel').first();
            if (await btnExcel.isVisible()) await btnExcel.click();

            console.log("31. Menekan tombol Kembali...");
            await page.locator('text="Kembali"').first().click();
            await page.waitForTimeout(1500);
        } else {
            console.log("   -> (Tombol Lihat Nilai Rekap tidak ditemukan)");
        }

        console.log("32. Log Out...");
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

        console.log("✅ Workflow Penilai E2E (Playwright) Selesai dengan Sukses!");

    } catch (err) {
        console.error("❌ Terjadi kesalahan:", err);
        await page.screenshot({ path: 'playwright/Error/penilai_playwright.png', fullPage: true });
        console.log("📸 Screenshot error telah disimpan sebagai 'penilai_playwright.png'");
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
