import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();
    const baseUrl = "http://mantappdh.test";

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

        console.log("   -> Detail Indikator Tahap 1...");
        const detailBtn1 = page.locator('a.btn-primary[href*="/inovasi"]').first();
        if (await detailBtn1.count() > 0) {
            await detailBtn1.click();
            await page.waitForTimeout(2000);

            console.log("      -> Tambah Indikator Tahap 1...");
            await page.click('#btnTambahIndikator');
            await page.waitForTimeout(1000);
            await page.fill('#inputNamaIndikator', 'Test Indikator PW');
            await page.selectOption('#selectJenis', 'substansi');
            await page.click('#btnSimpanIndikator');
            await page.waitForTimeout(2000);

            console.log("      -> Ubah Indikator Tahap 1...");
            const btnEdit = page.locator('.btn-edit-indikator').last();
            if (await btnEdit.count() > 0) {
                await btnEdit.click();
                await page.waitForTimeout(1000);
                await page.fill('#inputNamaIndikator', 'Test Indikator PW Edit');
                await page.click('#btnSimpanIndikator');
                await page.waitForTimeout(2000);
                
                console.log("      -> Tambah Keterangan Tahap 1...");
                await page.locator('a.btn-primary[href*="/detail/"]').last().click();
                await page.waitForTimeout(2000);

                await page.click('#btnTambahKeterangan');
                await page.waitForTimeout(1000);
                await page.fill('#inputKeterangan', 'Keterangan Uji Coba');
                await page.fill('#inputNilaiMinimal', '10');
                await page.fill('#inputNilaiMaksimal', '90');
                await page.click('#formKeterangan button[type="submit"]');
                await page.waitForTimeout(2000);
                
                await page.locator('a.btn-dark').first().click();
                await page.waitForTimeout(3000);
                
                console.log("      -> Hapus Indikator Tahap 1...");
                await page.locator('.btn-hapus-indikator').last().click();
                await page.waitForTimeout(1000);
                await page.click('#formHapusIndikator button[type="submit"]');
                await page.waitForTimeout(2000);
            }
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

        console.log("   -> Detail Indikator Tahap 2...");
        const detailBtn2 = page.locator('a.btn-primary[href*="/indikator"]').first();
        if (await detailBtn2.count() > 0) {
            await detailBtn2.click();
            await page.waitForTimeout(2000);

            console.log("      -> Tambah Indikator Tahap 2...");
            await page.click('#btnTambahIndikator');
            await page.waitForTimeout(1000);
            await page.fill('#inputNamaIndikator', 'Test Indikator T2 PW');
            await page.selectOption('#inputJenis', 'Subtansi Inovasi');
            await page.fill('#inputKeterangan', 'Keterangan T2 PW');
            await page.fill('#inputNilaiMinimal', '0');
            await page.fill('#inputNilaiMaksimal', '100');
            await page.click('#formIndikator button[type="submit"]');
            await page.waitForTimeout(2000);

            console.log("      -> Ubah Indikator Tahap 2...");
            const btnEdit2 = page.locator('.btn-edit-indikator').last();
            if (await btnEdit2.count() > 0) {
                await btnEdit2.click();
                await page.waitForTimeout(1000);
                await page.fill('#inputKeterangan', 'Keterangan T2 PW Edit');
                await page.click('#formIndikator button[type="submit"]');
                await page.waitForTimeout(2000);

                console.log("      -> Hapus Indikator Tahap 2...");
                await page.locator('.btn-hapus-indikator').last().click();
                await page.waitForTimeout(1000);
                await page.click('#formHapus button[type="submit"]');
                await page.waitForTimeout(2000);
            }
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

        console.log("✅ Workflow Indikator E2E (Playwright) Selesai dengan Sukses!");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
