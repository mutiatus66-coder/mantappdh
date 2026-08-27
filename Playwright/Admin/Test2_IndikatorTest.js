import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false, args: ['--start-maximized'] });
    const context = await browser.newContext({ viewport: null });
    const page = await context.newPage();

    try {
        console.log("[2] INDIKATOR DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto('http://mantappdh.test/');
        await page.waitForTimeout(1000);
        await page.click('text="Login"');
        await page.waitForTimeout(2000);
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        // INDIKATOR TAHAP 1
        console.log("   -> Indikator Tahap 1...");
        await page.click('a.ri-menu-item[href="/indikator/tahap-1"]');
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let btn = document.querySelector('.btn-open-formulasi1'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        
        let formulasi1Visible = await page.evaluate(() => {
            return document.querySelector('#modalFormulasi1') !== null;
        });
        
        if (formulasi1Visible) {
            await page.evaluate(() => {
                document.getElementById('inputNilaiMakalah').value = '40'; 
                document.getElementById('inputNilaiMakalah').dispatchEvent(new Event('input'));
                document.getElementById('inputNilaiSubstansi').value = '60'; 
                document.getElementById('inputNilaiSubstansi').dispatchEvent(new Event('input'));
            });
            await page.waitForTimeout(500);
            await page.evaluate(() => {
                let btn = document.getElementById('btnSimpan1'); 
                if(btn && !btn.disabled) btn.click();
            });
            await page.waitForTimeout(1500);
        }

        // INDIKATOR TAHAP 2
        console.log("   -> Indikator Tahap 2...");
        await page.click('a.ri-menu-item[href="/indikator/tahap-2"]');
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let btn2 = document.querySelector('.btn-open-formulasi'); 
            if(btn2) { btn2.scrollIntoView({block:'center'}); btn2.click(); }
        });
        await page.waitForTimeout(800);
        
        let formulasi2Visible = await page.evaluate(() => {
            return document.querySelector('#modalFormulasi') !== null;
        });
        
        if (formulasi2Visible) {
            await page.evaluate(() => {
                document.getElementById('inputNilaiInovasi').value = '50'; 
                document.getElementById('inputNilaiInovasi').dispatchEvent(new Event('input'));
                document.getElementById('inputNilaiPeragaan').value = '50'; 
                document.getElementById('inputNilaiPeragaan').dispatchEvent(new Event('input'));
            });
            await page.waitForTimeout(500);
            await page.evaluate(() => {
                let btn = document.getElementById('btnSimpan2'); 
                if(btn && !btn.disabled) btn.click();
            });
            await page.waitForTimeout(1500);
        }

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

        console.log("✅ INDIKATOR TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
    } finally {
        await browser.close();
    }
})();
