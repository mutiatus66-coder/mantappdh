import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();
    const baseUrl = "http://mantappdh.test";

    try {
        console.log("[3] INOVASI DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto(baseUrl);
        await page.locator('.btn-login').first().click();
        await page.locator('input[name="email"]').fill('admin@demo.test');
        await page.locator('input[name="password"]').fill('password');
        await page.locator('button[type="submit"]').click();
        await page.waitForURL('**/index');

        // RIWAYAT INOVASI
        console.log("   -> Riwayat Inovasi...");
        await page.goto(baseUrl + '/inovasi/riwayat');
        await page.waitForTimeout(2000);
        await page.locator('text="Lihat Usulan"').first().click();
        await page.waitForTimeout(2000);
        
        const searchInput = page.locator('.dt-search input');
        await searchInput.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await searchInput.isVisible()) {
            await searchInput.fill('a');
            await page.waitForTimeout(1000); // Tunggu filter tabel bereaksi
        }
        
        await page.locator('text="Kembali"').first().click();
        await page.waitForTimeout(1500);

        // REKAP NILAI
        console.log("   -> Rekap Nilai (Export)...");
        await page.goto(baseUrl + '/inovasi/rekap-nilai');
        await page.waitForTimeout(2000);
        await page.locator('text="Lihat Nilai"').first().click();
        await page.waitForTimeout(2000);
        
        // Export buttons
        const btnPdf = page.locator('.buttons-pdf');
        await btnPdf.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnPdf.isVisible()) {
            await btnPdf.click();
            await page.waitForTimeout(1000);
        }
        
        const btnExcel = page.locator('.buttons-excel');
        await btnExcel.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnExcel.isVisible()) {
            await btnExcel.click();
            await page.waitForTimeout(1000);
        }
        
        await page.locator('text="Kembali"').first().click();
        await page.waitForTimeout(1500);
        
        // AUDIT LOG (RIWAYAT HALAMAN)
        console.log("   -> Buka Audit Log / Riwayat Halaman...");
        const btnHistory = page.locator('button[onclick*="toggleHistoryPanel"]');
        await btnHistory.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnHistory.isVisible()) {
            await btnHistory.click();
            await page.waitForTimeout(2000); // Tunggu panel animasi terbuka
            
            // Tutup menggunakan tombol X atau eval jika tidak ada elemen jelas
            await page.evaluate(() => {
                if(typeof window.closeHistoryPanel === 'function') window.closeHistoryPanel();
            });
            await page.waitForTimeout(1000);
        }

        // LOGOUT
        console.log("   -> Logout...");
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

        console.log("✅ Workflow Inovasi E2E (Playwright) Selesai dengan Sukses!");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
