import { Builder, By, until } from 'selenium-webdriver';
import chrome from 'selenium-webdriver/chrome.js';

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

(async function test4Penilaian() {
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
        console.log("[4] PENILAIAN DATA (Selenium)");
        
        console.log("   -> Login Admin...");
        await driver.get('http://mantappdh.test/');
        await sleep(1000);
        await driver.findElement(By.xpath("//a[contains(text(), 'Login')]")).click();
        await sleep(2000);
        await driver.wait(until.elementLocated(By.name('email')), 10000).sendKeys('admin@admin.com');
        await driver.findElement(By.name('password')).sendKeys('password');
        await driver.findElement(By.css('button[type="submit"]')).click();
        await sleep(3000);

        const clickByText = async (text) => {
            const el = await driver.wait(until.elementLocated(By.xpath(`//a[contains(., '${text}')]`)), 10000);
            await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", el);
        };

        // PENILAIAN TAHAP 1
        console.log("   -> Penilaian Tahap 1 (Simpan Kurasi)...");
        await executeScriptClick('a.ri-menu-item[href="/penilaian/tahap-1"]');
        await sleep(2000);
        await clickByText('Lihat Nilai Verifikasi');
        await sleep(2000);
        await driver.executeScript(`
            let chk = document.querySelector('.chk-all'); 
            if(chk && !chk.checked) { chk.scrollIntoView({block:'center'}); chk.click(); }
        `);
        await sleep(1000);
        await driver.executeScript(`
            let btn = document.querySelector('.btn-rv-simpan'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(3000);
        await clickByText('Kembali');
        await sleep(1500);

        // PENILAIAN TAHAP 2
        console.log("   -> Penilaian Tahap 2 (Auto Ranking)...");
        await executeScriptClick('a.ri-menu-item[href="/penilaian/tahap-2"]');
        await sleep(2000);
        await clickByText('Lihat Nilai Nominator');
        await sleep(2000);
        await driver.executeScript(`
            let btn = document.querySelector('.btn-auto-ranking'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(2000);
        await driver.executeScript(`
            let btn2 = document.querySelector('.btn-simpan-ranking'); 
            if(btn2) { btn2.scrollIntoView({block:'center'}); btn2.click(); }
        `);
        await sleep(3000);
        await clickByText('Kembali');
        await sleep(1500);

        // AUDIT LOG (RIWAYAT HALAMAN)
        console.log("   -> Buka Audit Log / Riwayat Halaman...");
        await driver.executeScript(`
            if(typeof window.toggleHistoryPanel === 'function') {
                window.toggleHistoryPanel(); 
            } else { 
                let btn = document.querySelector('button[onclick="toggleHistoryPanel()"]'); 
                if(btn) btn.click(); 
            }
        `);
        await sleep(2000);
        await driver.executeScript(`
            if(typeof window.closeHistoryPanel === 'function') window.closeHistoryPanel();
        `);
        await sleep(1000);

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

        console.log("✅ PENILAIAN TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await driver.quit();
    }
})();
