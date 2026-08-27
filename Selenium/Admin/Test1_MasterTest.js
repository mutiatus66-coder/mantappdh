import { Builder, By, until } from 'selenium-webdriver';
import chrome from 'selenium-webdriver/chrome.js';

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

(async function test1Master() {
    let options = new chrome.Options();
    options.addArguments('--window-size=1920,1080');
    // options.addArguments('--headless'); // headless disabled for debugging

    let driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    const waktu = Date.now();

    const executeScriptClick = async (selector) => {
        const el = await driver.findElement(By.css(selector));
        await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", el);
    };

    try {
        console.log("[1] MASTER DATA (Selenium)");
        
        console.log("   -> Login Admin...");
        await driver.get('http://mantappdh.test/');
        await sleep(1000);
        await driver.findElement(By.xpath("//a[contains(text(), 'Login')]")).click();
        await sleep(2000);
        await driver.wait(until.elementLocated(By.name('email')), 10000).sendKeys('admin@admin.com');
        await driver.findElement(By.name('password')).sendKeys('password');
        await driver.findElement(By.css('button[type="submit"]')).click();
        await sleep(3000);

        // TEST TEMA
        console.log("   -> Test Tema Gelap & Terang...");
        await driver.executeScript(`
            let darkBtn = document.querySelector('[data-kt-element="mode"][data-kt-value="dark"]');
            if(darkBtn) darkBtn.click();
        `);
        await sleep(1500);
        await driver.executeScript(`
            let lightBtn = document.querySelector('[data-kt-element="mode"][data-kt-value="light"]');
            if(lightBtn) lightBtn.click();
        `);
        await sleep(1500);

        // 1. EVENT
        console.log("   -> Event: Tambah, Ubah, Hapus...");
        await executeScriptClick('a.ri-menu-item[href="/event"]');
        await sleep(2000);
        await executeScriptClick('#btnTambahEvent');
        await sleep(800);
        await driver.findElement(By.css('#inputNamaEvent')).sendKeys(`Event ${waktu}`);
        await driver.findElement(By.css('#inputJenis')).sendKeys('INOTEK');
        await executeScriptClick('#btnSimpanEvent');
        await sleep(2000);

        await driver.executeScript(`
            let btns = document.querySelectorAll('.btn-edit-event'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        let inputEvent = await driver.findElement(By.css('#inputNamaEvent'));
        await inputEvent.clear();
        await inputEvent.sendKeys(`Event Edit ${waktu}`);
        await executeScriptClick('#btnSimpanEvent');
        await sleep(2000);

        await driver.executeScript(`
            let btns = document.querySelectorAll('.btn-hapus-event'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await executeScriptClick('#btnHapusEvent');
        await sleep(2000);

        // 2. SUB EVENT
        console.log("   -> Sub Event: Tambah, Ubah, Hapus...");
        await executeScriptClick('a.ri-menu-item[href="/sub-event"]');
        await sleep(2000);
        await executeScriptClick('#btnTambahSubEvent');
        await sleep(800);
        await driver.findElement(By.css('#seTahun')).sendKeys(String(new Date().getFullYear() + 1));
        await driver.executeScript(`
            let sel = document.getElementById('seEvent'); 
            if(sel && sel.options.length > 1) sel.selectedIndex = 1;
        `);
        await driver.findElement(By.css('#seSubEvent')).sendKeys(`Sub Event ${waktu}`);
        await driver.findElement(By.css('#seKategori')).sendKeys('Kategori Test');
        await driver.findElement(By.css('#seMulai')).sendKeys('2025-01-01');
        await driver.findElement(By.css('#seBerakhir')).sendKeys('2025-12-31');
        await executeScriptClick('#btnSimpanSE');
        await sleep(2000);
        
        await driver.executeScript(`
            let btns = document.querySelectorAll('.btn-hapus-se'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await executeScriptClick('#btnHapusSE');
        await sleep(2000);

        // 3. BIDANG
        console.log("   -> Bidang: Tambah, Hapus...");
        await executeScriptClick('a.ri-menu-item[href="/bidang"]');
        await sleep(2000);
        await driver.executeScript(`
            let acc = document.querySelector('.bidang-accordion-btn'); 
            if(acc) { acc.scrollIntoView({block:'center'}); acc.click(); }
        `);
        await sleep(1500);
        await driver.executeScript(`
            let btn = document.querySelector('.btn-tambah-bidang'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await driver.findElement(By.css('#bidangNama')).sendKeys(`Bidang ${waktu}`);
        await executeScriptClick('#statusAktifBidang');
        await executeScriptClick('#btnSimpanBidang');
        await sleep(2000);
        
        await driver.executeScript(`
            let btns = document.querySelectorAll('.btn-hapus-bidang'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await executeScriptClick('#btnHapusBidang');
        await sleep(2000);

        // 4. USER
        console.log("   -> User: Tambah, Hapus...");
        await executeScriptClick('a.ri-menu-item[href="/user"]');
        await sleep(2000);
        await executeScriptClick('#btnTambahUser');
        await sleep(800);
        await driver.findElement(By.css('#inputNama')).sendKeys('User Test');
        await driver.findElement(By.css('#inputEmail')).sendKeys(`user_${waktu}@test.com`);
        await driver.findElement(By.css('#inputHakAkses')).sendKeys('peserta');
        await driver.findElement(By.css('#inputPassword')).sendKeys('Password123!');
        await executeScriptClick('#btnSimpanUser');
        await sleep(2000);
        
        await driver.findElement(By.css('.dt-search input')).sendKeys('User Test');
        await sleep(1000);
        await driver.executeScript(`
            let btn = document.querySelector('.btn-hapus-user'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await executeScriptClick('#btnHapusUser');
        await sleep(2000);

        // 5. PENGUMUMAN
        console.log("   -> Pengumuman: Tambah, Hapus...");
        await executeScriptClick('a.ri-menu-item[href="/pengumuman"]');
        await sleep(2000);
        await executeScriptClick('#btnTambahPengumuman');
        await sleep(800);
        await driver.findElement(By.css('#pJudul')).sendKeys(`Pengumuman ${waktu}`);
        await driver.findElement(By.css('#pDeskripsi')).sendKeys('Deskripsi');
        await driver.findElement(By.css('#pStatus')).sendKeys('Draft');
        await executeScriptClick('#btnSimpanPengumuman');
        await sleep(2000);
        
        await driver.executeScript(`
            let btns = document.querySelectorAll('.btn-hapus-pengumuman'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await executeScriptClick('#btnHapusPengumuman');
        await sleep(2000);

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

        console.log("✅ MASTER TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await driver.quit();
    }
})();
