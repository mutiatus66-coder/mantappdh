import { Builder, By, until } from 'selenium-webdriver';
import chrome from 'selenium-webdriver/chrome.js';

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

(async function test3Inovasi() {
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
        console.log("[3] INOVASI DATA (Selenium)");
        
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

        // RIWAYAT INOVASI
        console.log("   -> Riwayat Inovasi...");
        await executeScriptClick('a.ri-menu-item[href="/inovasi/riwayat"]');
        await sleep(2000);
        await clickByText('Lihat Usulan');
        await sleep(2000);
        await driver.executeScript(`
            let input = document.querySelector('.dt-search input'); 
            if(input) { input.value = 'a'; input.dispatchEvent(new Event('input')); }
        `);
        await sleep(1000);
        await clickByText('Kembali');
        await sleep(1500);

        // REKAP NILAI
        console.log("   -> Rekap Nilai (Export)...");
        await executeScriptClick('a.ri-menu-item[href="/inovasi/rekap-nilai"]');
        await sleep(2000);
        await clickByText('Lihat Nilai');
        await sleep(3000);
        await driver.executeScript(`
            let btnPdf = document.querySelector('.buttons-pdf'); 
            if(btnPdf) { btnPdf.scrollIntoView({block:'center'}); btnPdf.click(); }
        `);
        await sleep(2000);
        await driver.executeScript(`
            let btnExcel = document.querySelector('.buttons-excel'); 
            if(btnExcel) { btnExcel.scrollIntoView({block:'center'}); btnExcel.click(); }
        `);
        await sleep(2000);
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

        console.log("✅ INOVASI TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await driver.quit();
    }
})();
