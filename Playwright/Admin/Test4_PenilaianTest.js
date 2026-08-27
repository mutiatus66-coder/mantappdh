import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false, args: ['--start-maximized'] });
    const context = await browser.newContext({ viewport: null });
    const page = await context.newPage();

    try {
        console.log("[4] PENILAIAN DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto('http://mantappdh.test/');
        await page.waitForTimeout(1000);
        await page.click('text="Login"');
        await page.waitForTimeout(2000);
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        // PENILAIAN TAHAP 1
        console.log("   -> Penilaian Tahap 1 (Simpan Kurasi)...");
        await page.click('a.ri-menu-item[href="/penilaian/tahap-1"]');
        await page.waitForTimeout(2000);
        await page.click('text="Lihat Nilai Verifikasi"');
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let chk = document.querySelector('.chk-all'); 
            if(chk && !chk.checked) { chk.scrollIntoView({block:'center'}); chk.click(); }
        });
        await page.waitForTimeout(1000);
        await page.evaluate(() => {
            let btn = document.querySelector('.btn-rv-simpan'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(3000);
        await page.click('text="Kembali"');
        await page.waitForTimeout(1500);

        // PENILAIAN TAHAP 2
        console.log("   -> Penilaian Tahap 2 (Auto Ranking)...");
        await page.click('a.ri-menu-item[href="/penilaian/tahap-2"]');
        await page.waitForTimeout(2000);
        await page.click('text="Lihat Nilai Nominator"');
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let btn = document.querySelector('.btn-auto-ranking'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let btn2 = document.querySelector('.btn-simpan-ranking'); 
            if(btn2) { btn2.scrollIntoView({block:'center'}); btn2.click(); }
        });
        await page.waitForTimeout(3000);
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

        console.log("✅ PENILAIAN TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
    } finally {
        await browser.close();
    }
})();
