import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: false, args: ['--start-maximized'] });
    const context = await browser.newContext({ viewport: null });
    const page = await context.newPage();
    const waktu = Date.now();

    try {
        console.log("[1] MASTER DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto('http://mantappdh.test/');
        await page.waitForTimeout(1000);
        await page.click('text="Login"');
        await page.waitForTimeout(2000);
        await page.fill('input[name="email"]', 'admin@admin.com');
        await page.fill('input[name="password"]', 'password');
        await page.click('button[type="submit"]');
        await page.waitForTimeout(3000);

        // TEST TEMA
        console.log("   -> Test Tema Gelap & Terang...");
        await page.evaluate(() => {
            let darkBtn = document.querySelector('[data-kt-element="mode"][data-kt-value="dark"]');
            if(darkBtn) darkBtn.click();
        });
        await page.waitForTimeout(1500);
        await page.evaluate(() => {
            let lightBtn = document.querySelector('[data-kt-element="mode"][data-kt-value="light"]');
            if(lightBtn) lightBtn.click();
        });
        await page.waitForTimeout(1500);

        // 1. EVENT
        console.log("   -> Event: Tambah, Ubah, Hapus...");
        await page.click('a.ri-menu-item[href="/event"]');
        await page.waitForTimeout(2000);
        await page.click('#btnTambahEvent');
        await page.waitForTimeout(800);
        await page.fill('#inputNamaEvent', `Event ${waktu}`);
        await page.selectOption('#inputJenis', 'INOTEK');
        await page.click('#btnSimpanEvent');
        await page.waitForTimeout(2000);

        await page.evaluate(() => {
            let btns = document.querySelectorAll('.btn-edit-event'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        await page.fill('#inputNamaEvent', '');
        await page.fill('#inputNamaEvent', `Event Edit ${waktu}`);
        await page.click('#btnSimpanEvent');
        await page.waitForTimeout(2000);

        await page.evaluate(() => {
            let btns = document.querySelectorAll('.btn-hapus-event'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        await page.click('#btnHapusEvent');
        await page.waitForTimeout(2000);

        // 2. SUB EVENT
        console.log("   -> Sub Event: Tambah, Ubah, Hapus...");
        await page.click('a.ri-menu-item[href="/sub-event"]');
        await page.waitForTimeout(2000);
        await page.click('#btnTambahSubEvent');
        await page.waitForTimeout(800);
        await page.fill('#seTahun', String(new Date().getFullYear() + 1));
        await page.evaluate(() => {
            let sel = document.getElementById('seEvent'); 
            if(sel && sel.options.length > 1) sel.selectedIndex = 1;
        });
        await page.fill('#seSubEvent', `Sub Event ${waktu}`);
        await page.fill('#seKategori', 'Kategori Test');
        await page.fill('#seMulai', '2025-01-01');
        await page.fill('#seBerakhir', '2025-12-31');
        await page.click('#btnSimpanSE');
        await page.waitForTimeout(2000);
        
        await page.evaluate(() => {
            let btns = document.querySelectorAll('.btn-hapus-se'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        await page.click('#btnHapusSE');
        await page.waitForTimeout(2000);

        // 3. BIDANG
        console.log("   -> Bidang: Tambah, Hapus...");
        await page.click('a.ri-menu-item[href="/bidang"]');
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let acc = document.querySelector('.bidang-accordion-btn'); 
            if(acc) { acc.scrollIntoView({block:'center'}); acc.click(); }
        });
        await page.waitForTimeout(1500);
        await page.evaluate(() => {
            let btn = document.querySelector('.btn-tambah-bidang'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        await page.fill('#bidangNama', `Bidang ${waktu}`);
        await page.check('#statusAktifBidang');
        await page.click('#btnSimpanBidang');
        await page.waitForTimeout(2000);
        
        await page.evaluate(() => {
            let btns = document.querySelectorAll('.btn-hapus-bidang'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        await page.click('#btnHapusBidang');
        await page.waitForTimeout(2000);

        // 4. USER
        console.log("   -> User: Tambah, Hapus...");
        await page.click('a.ri-menu-item[href="/user"]');
        await page.waitForTimeout(2000);
        await page.click('#btnTambahUser');
        await page.waitForTimeout(800);
        await page.fill('#inputNama', 'User Test');
        await page.fill('#inputEmail', `user_${waktu}@test.com`);
        await page.selectOption('#inputHakAkses', 'peserta');
        await page.fill('#inputPassword', 'Password123!');
        await page.click('#btnSimpanUser');
        await page.waitForTimeout(2000);
        
        await page.fill('.dt-search input', 'User Test');
        await page.waitForTimeout(1000);
        await page.evaluate(() => {
            let btn = document.querySelector('.btn-hapus-user'); 
            if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        await page.click('#btnHapusUser');
        await page.waitForTimeout(2000);

        // 5. PENGUMUMAN
        console.log("   -> Pengumuman: Tambah, Hapus...");
        await page.click('a.ri-menu-item[href="/pengumuman"]');
        await page.waitForTimeout(2000);
        await page.click('#btnTambahPengumuman');
        await page.waitForTimeout(800);
        await page.fill('#pJudul', `Pengumuman ${waktu}`);
        await page.fill('#pDeskripsi', 'Deskripsi');
        await page.selectOption('#pStatus', 'Draft');
        await page.click('#btnSimpanPengumuman');
        await page.waitForTimeout(2000);
        
        await page.evaluate(() => {
            let btns = document.querySelectorAll('.btn-hapus-pengumuman'); 
            if(btns.length) { let btn = btns[btns.length-1]; btn.scrollIntoView({block:'center'}); btn.click(); }
        });
        await page.waitForTimeout(800);
        await page.click('#btnHapusPengumuman');
        await page.waitForTimeout(2000);

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

        console.log("✅ MASTER TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
    } finally {
        await browser.close();
    }
})();
