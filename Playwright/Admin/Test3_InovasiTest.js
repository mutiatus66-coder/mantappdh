import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false, args: ['--start-maximized'] });
    const context = await browser.newContext({ viewport: null });
    const page = await context.newPage();

    try {
        console.log("[3] INOVASI DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto('http://127.0.0.1:8000/');
        await page.waitForTimeout(1000);
        await page.click('text="Login"');
        await page.waitForTimeout(2000);
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        // RIWAYAT INOVASI
        console.log("   -> Riwayat Inovasi...");
        await page.click('a.ri-menu-item[href="/inovasi/riwayat"]');
        await page.waitForTimeout(2000);
        await page.click('text="Lihat Usulan"');
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let input = document.querySelector('.dt-search input'); 
            if(input) { input.value = 'a'; input.dispatchEvent(new Event('input')); }
        });
        await page.waitForTimeout(1000);
        await page.click('text="Kembali"');
        await page.waitForTimeout(1500);

        // REKAP NILAI
        console.log("   -> Rekap Nilai (Export)...");
        await page.click('a.ri-menu-item[href="/inovasi/rekap-nilai"]');
        await page.waitForTimeout(2000);
        await page.click('text="Lihat Nilai"');
        await page.waitForTimeout(3000);
        await page.evaluate(() => {
            let btnPdf = document.querySelector('.buttons-pdf'); 
            if(btnPdf) { btnPdf.scrollIntoView({block:'center'}); btnPdf.click(); }
        });
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let btnExcel = document.querySelector('.buttons-excel'); 
            if(btnExcel) { btnExcel.scrollIntoView({block:'center'}); btnExcel.click(); }
        });
        await page.waitForTimeout(2000);
        await page.click('text="Kembali"');
        await page.waitForTimeout(1500);
        
        // AUDIT LOG (RIWAYAT HALAMAN)
        console.log("   -> Buka Audit Log / Riwayat Halaman...");
        await page.evaluate(() => {
            if(typeof window.toggleHistoryPanel === 'function') {
                window.toggleHistoryPanel(); 
            } else { 
                let btn = document.querySelector('button[onclick="toggleHistoryPanel()"]'); 
                if(btn) btn.click(); 
            }
        });
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            if(typeof window.closeHistoryPanel === 'function') window.closeHistoryPanel();
        });
        await page.waitForTimeout(1000);

        // LOGOUT
        console.log("   -> Logout...");
        await page.evaluate(() => {
            let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); 
            if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }
        });
        await page.waitForTimeout(1000);
        await page.evaluate(() => {
            let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out')); 
            if(signOut) { signOut.click(); }
        });
        await page.waitForTimeout(3000);

        console.log("✅ INOVASI TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
    } finally {
        await browser.close();
    }
})();
