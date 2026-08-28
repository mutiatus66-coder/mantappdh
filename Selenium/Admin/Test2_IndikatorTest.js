import { Builder, By, until } from 'selenium-webdriver';
import chrome from 'selenium-webdriver/chrome.js';

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

(async function test2Indikator() {
    let options = new chrome.Options();
    options.addArguments('--window-size=1920,1080');

    let driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    const executeScriptClick = async (selector) => {
        const el = await driver.findElement(By.css(selector));
        await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", el);
    };

    try {
        console.log("[2] INDIKATOR DATA (Selenium)");
        
        console.log("   -> Login Admin...");
        await driver.get('http://127.0.0.1:8000/');
        await sleep(1000);
        await driver.findElement(By.xpath("//a[contains(text(), 'Login')]")).click();
        await sleep(2000);
        await driver.wait(until.elementLocated(By.name('email')), 10000).sendKeys('admin@admin.com');
        await driver.findElement(By.name('password')).sendKeys('password');
        await driver.findElement(By.css('button[type="submit"]')).click();
        await sleep(3000);

        // INDIKATOR TAHAP 1
        console.log("   -> Indikator Tahap 1...");
        await executeScriptClick('a.ri-menu-item[href="/indikator/tahap-1"]');
        await sleep(2000);
        await driver.executeScript(`
            let btn = document.querySelector('.btn-open-formulasi1'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        
        let formulasi1Visible = await driver.executeScript("return document.querySelector('#modalFormulasi1') !== null;");
        if (formulasi1Visible) {
            await driver.executeScript(`
                document.getElementById('inputNilaiMakalah').value = '40'; 
                document.getElementById('inputNilaiMakalah').dispatchEvent(new Event('input'));
                document.getElementById('inputNilaiSubstansi').value = '60'; 
                document.getElementById('inputNilaiSubstansi').dispatchEvent(new Event('input'));
            `);
            await sleep(500);
            await driver.executeScript(`
                let btn = document.getElementById('btnSimpan1'); 
                if(btn && !btn.disabled) btn.click();
            `);
            await sleep(1500);
        }

        // INDIKATOR TAHAP 2
        console.log("   -> Indikator Tahap 2...");
        await executeScriptClick('a.ri-menu-item[href="/indikator/tahap-2"]');
        await sleep(2000);
        await driver.executeScript(`
            let btn2 = document.querySelector('.btn-open-formulasi'); 
            if(btn2) { btn2.scrollIntoView({block:'center'}); btn2.click(); }
        `);
        await sleep(800);
        
        let formulasi2Visible = await driver.executeScript("return document.querySelector('#modalFormulasi') !== null;");
        if (formulasi2Visible) {
            await driver.executeScript(`
                document.getElementById('inputNilaiInovasi').value = '50'; 
                document.getElementById('inputNilaiInovasi').dispatchEvent(new Event('input'));
                document.getElementById('inputNilaiPeragaan').value = '50'; 
                document.getElementById('inputNilaiPeragaan').dispatchEvent(new Event('input'));
            `);
            await sleep(500);
            await driver.executeScript(`
                let btn = document.getElementById('btnSimpan2'); 
                if(btn && !btn.disabled) btn.click();
            `);
            await sleep(1500);
        }

        // LOGOUT
        console.log("   -> Logout...");
        await driver.executeScript(`
            let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); 
            if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }
        `);
        await sleep(1000);
        await driver.executeScript(`
            let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out')); 
            if(signOut) { signOut.click(); }
        `);
        await sleep(3000);

        console.log("✅ INDIKATOR TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await driver.quit();
    }
})();
