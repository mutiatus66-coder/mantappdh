import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();
    const baseUrl = "http://127.0.0.1:8000";

    try {
        console.log("[2] INDIKATOR DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto(baseUrl);
        await page.locator('.btn-login').first().click();
        await page.locator('input[name="email"]').fill('admin@demo.test');
        await page.locator('input[name="password"]').fill('password');
        await page.locator('button[type="submit"]').click();
        await page.waitForURL('**/index');

        // INDIKATOR TAHAP 1
        console.log("   -> Indikator Tahap 1...");
        await page.goto(baseUrl + '/indikator/tahap-1');
        await page.waitForTimeout(2000);
        
        // Klik tombol formulasi
        const btnFormulasi1 = page.locator('.btn-open-formulasi1').first();
        if (await btnFormulasi1.count() > 0) {
            await page.evaluate(() => {
                let btn = document.querySelector('.btn-open-formulasi1'); 
                if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
            });
            await page.waitForTimeout(1000);
            
            await page.evaluate(() => {
                let m = document.getElementById('inputNilaiMakalah');
                let s = document.getElementById('inputNilaiSubstansi');
                let b = document.getElementById('btnSimpan1');
                if(m) { m.value = '40'; m.dispatchEvent(new Event('input', {bubbles: true})); }
                if(s) { s.value = '60'; s.dispatchEvent(new Event('input', {bubbles: true})); }
                if(b) b.click();
            });
            await page.waitForTimeout(2000);
        }

        // INDIKATOR TAHAP 2
        console.log("   -> Indikator Tahap 2...");
        await page.goto(baseUrl + '/indikator/tahap-2');
        await page.waitForTimeout(2000);
        
        const btnFormulasi2 = page.locator('.btn-open-formulasi').first();
        if (await btnFormulasi2.count() > 0) {
            await page.evaluate(() => {
                let btn = document.querySelector('.btn-open-formulasi'); 
                if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
            });
            await page.waitForTimeout(1000);
            
            await page.evaluate(() => {
                let i = document.getElementById('inputNilaiInovasi');
                let p = document.getElementById('inputNilaiPeragaan');
                let b = document.getElementById('btnSimpan2');
                if(i) { i.value = '50'; i.dispatchEvent(new Event('input', {bubbles: true})); }
                if(p) { p.value = '50'; p.dispatchEvent(new Event('input', {bubbles: true})); }
                if(b) b.click();
            });
            await page.waitForTimeout(2000);
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

        console.log("✅ INDIKATOR TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
