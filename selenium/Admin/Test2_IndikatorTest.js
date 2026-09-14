import { Builder, By, until } from 'selenium-webdriver';
import chrome from 'selenium-webdriver/chrome.js';

import { sleep, testDataTables, takeScreenshotSession, takeScreenshotOnError, clearInput } from '../helpers.js';

(async function test2Indikator() {
    let options = new chrome.Options();
    options.addArguments('--window-size=1920,1080');
    options.addArguments('--no-sandbox');
    options.addArguments('--disable-dev-shm-usage');
    options.addArguments('--ignore-certificate-errors');
    if (process.env.HEADLESS === 'true') {
        options.addArguments('--headless=new');
        options.addArguments('--disable-gpu');
    }

    let driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    const baseUrl = process.env.BASE_URL || 'https://mantappdh.test';

    const executeScriptClick = async (selector) => {
        const el = await driver.findElement(By.css(selector));
        await driver.executeScript(`
            document.querySelectorAll('.dt-button-background').forEach(b => b.remove());
            arguments[0].scrollIntoView({block:'center'});
            arguments[0].click();
        `, el);
    };

    const navigateTo = async (path) => {
        try {
            let link = await driver.findElements(By.css(`a.ri-menu-item[href="${path}"]`));
            if (link.length > 0 && await link[0].isDisplayed()) {
                await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", link[0]);
                await sleep(2000);
            } else {
                await driver.get(baseUrl + path);
                await sleep(2000);
            }
        } catch (e) {
            await driver.get(baseUrl + path);
            await sleep(2000);
        }
    };

    try {
        console.log("[2] INDIKATOR DATA (Selenium - Seluruh Fungsi & Halaman)");
        
        // ── 1. LOGIN ADMIN ──────────────────────────────────────────────────
        console.log("   -> Login Admin...");
        await driver.get(baseUrl);
        await sleep(1000);
        await driver.findElement(By.xpath("//a[contains(text(), 'Login')]")).click();
        await sleep(2000);
        await driver.wait(until.elementLocated(By.name('email')), 10000).sendKeys('admin@demo.test');
        await driver.findElement(By.name('password')).sendKeys('password');
        await driver.findElement(By.css('button[type="submit"]')).click();
        await sleep(2000);
        await driver.wait(until.elementLocated(By.css('#kt_header_user_menu_toggle, .cursor-pointer.symbol')), 10000);
        await sleep(1000);

        // ── 2. INDIKATOR TAHAP 1 — LIST & FORMULASI ──────────────────────────
        console.log("   -> [Tahap 1] Navigasi Halaman Utama...");
        await navigateTo('/indikator/tahap-1');
        await driver.wait(until.elementLocated(By.id('tabelTahap1')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_Index');

        console.log("   -> [Tahap 1] Uji DataTables Sub Event...");
        await testDataTables(driver, 'Indikator Tahap 1');

        console.log("   -> [Tahap 1] Uji Formulasi Bobot Nilai...");
        await driver.executeScript(`
            let trs = Array.from(document.querySelectorAll('#tabelTahap1 tbody tr'));
            let targetTr = trs.find(tr => tr.innerText.includes('INOTEK 2025')) || trs[0];
            let btn = targetTr ? targetTr.querySelector('.btn-open-formulasi1') : null;
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(1000);

        let formulasi1Visible = await driver.executeScript("return document.querySelector('#modalFormulasi1') !== null;");
        if (formulasi1Visible) {
            await driver.executeScript(`
                let mak = document.getElementById('inputNilaiMakalah');
                let sub = document.getElementById('inputNilaiSubstansi');
                if (mak && sub) {
                    mak.value = '40';
                    mak.dispatchEvent(new Event('input', { bubbles: true }));
                    sub.value = '60';
                    sub.dispatchEvent(new Event('input', { bubbles: true }));
                }
            `);
            await sleep(500);
            await takeScreenshotSession(driver, 'Test2_Tahap1_Formulasi_Modal');
            await driver.executeScript(`
                let btn = document.getElementById('btnSimpan1');
                if (btn && !btn.disabled) btn.click();
            `);
            await sleep(2000);
            await takeScreenshotSession(driver, 'Test2_Tahap1_Formulasi_Saved');
        }

        // ── 3. INDIKATOR TAHAP 1 — DETAIL INOVASI (CRUD INDIKATOR) ──────────
        console.log("   -> [Tahap 1] Masuk ke Detail Inovasi...");
        await driver.executeScript(`
            let trs = Array.from(document.querySelectorAll('#tabelTahap1 tbody tr'));
            let targetTr = trs.find(tr => tr.innerText.includes('INOTEK 2025')) || trs[0];
            let btnDetail = targetTr ? targetTr.querySelector('a.btn-primary[href*="/inovasi"]') : null;
            if (btnDetail) {
                btnDetail.scrollIntoView({block:'center'});
                btnDetail.click();
            }
        `);
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelDetailInovasi')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_DetailInovasi_Index');

        console.log("   -> [Tahap 1] Uji DataTables Detail Inovasi...");
        await testDataTables(driver, 'Detail Inovasi');

        // Tambah Indikator Baru
        console.log("   -> [Tahap 1] Tambah Indikator Inovasi...");
        await executeScriptClick('#btnTambahIndikator');
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalIndikator.show')), 5000);
        await sleep(400);

        let inputNamaIndikator = await driver.findElement(By.id('inputNamaIndikator'));
        await clearInput(driver, inputNamaIndikator);
        await inputNamaIndikator.sendKeys('Indikator Uji Coba Selenium');

        let selectJenis = await driver.findElement(By.id('selectJenis'));
        await selectJenis.sendKeys('substansi');
        await sleep(500);
        await takeScreenshotSession(driver, 'Test2_Tahap1_TambahIndikator_Modal');

        await executeScriptClick('#btnSimpanIndikator');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelDetailInovasi')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_TambahIndikator_Success');

        // Ubah Indikator
        console.log("   -> [Tahap 1] Ubah Indikator Inovasi...");
        let searchInovasi = await driver.findElements(By.css('.dt-search input, .dataTables_filter input'));
        if (searchInovasi.length > 0) {
            await clearInput(driver, searchInovasi[0]);
            await searchInovasi[0].sendKeys('Indikator Uji Coba Selenium');
            await sleep(800);
        }

        await driver.executeScript(`
            let btn = Array.from(document.querySelectorAll('.btn-edit-indikator'))
                .find(b => b.dataset.indikator && b.dataset.indikator.includes('Indikator Uji Coba Selenium'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalIndikator.show')), 5000);
        await sleep(400);

        inputNamaIndikator = await driver.findElement(By.id('inputNamaIndikator'));
        await clearInput(driver, inputNamaIndikator);
        await inputNamaIndikator.sendKeys('Indikator Uji Coba Selenium (Updated)');

        selectJenis = await driver.findElement(By.id('selectJenis'));
        await selectJenis.sendKeys('makalah');
        await sleep(500);
        await takeScreenshotSession(driver, 'Test2_Tahap1_UbahIndikator_Modal');

        await executeScriptClick('#btnSimpanIndikator');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelDetailInovasi')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_UbahIndikator_Success');

        // ── 4. INDIKATOR TAHAP 1 — DETAIL KETERANGAN (CRUD PARAMETER) ────────
        console.log("   -> [Tahap 1] Masuk ke Detail Keterangan Parameter...");
        searchInovasi = await driver.findElements(By.css('.dt-search input, .dataTables_filter input'));
        if (searchInovasi.length > 0) {
            await clearInput(driver, searchInovasi[0]);
            await searchInovasi[0].sendKeys('Indikator Uji Coba Selenium');
            await sleep(800);
        }

        await driver.executeScript(`
            let trs = Array.from(document.querySelectorAll('#tabelDetailInovasi tbody tr'));
            let tr = trs.find(r => r.innerText.includes('Indikator Uji Coba Selenium'));
            if (tr) {
                let link = tr.querySelector('a.btn-primary[href*="/detail/"]');
                if (link) { link.scrollIntoView({block:'center'}); link.click(); }
            }
        `);
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelKeterangan')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_DetailKeterangan_Index');

        console.log("   -> [Tahap 1] Uji DataTables Keterangan...");
        await testDataTables(driver, 'Detail Keterangan');

        // Tambah Keterangan
        console.log("   -> [Tahap 1] Tambah Keterangan Parameter...");
        await executeScriptClick('#btnTambahKeterangan');
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalKeterangan.show')), 5000);
        await sleep(400);

        let inputKet = await driver.findElement(By.id('inputKeterangan'));
        await clearInput(driver, inputKet);
        await inputKet.sendKeys('Keterangan Parameter Uji Coba');

        let inputMin = await driver.findElement(By.id('inputNilaiMinimal'));
        await clearInput(driver, inputMin);
        await inputMin.sendKeys('10');

        let inputMax = await driver.findElement(By.id('inputNilaiMaksimal'));
        await clearInput(driver, inputMax);
        await inputMax.sendKeys('90');
        await sleep(500);
        await takeScreenshotSession(driver, 'Test2_Tahap1_TambahKeterangan_Modal');

        await executeScriptClick('#formKeterangan button[type="submit"]');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelKeterangan')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_TambahKeterangan_Success');

        // Ubah Keterangan
        console.log("   -> [Tahap 1] Ubah Keterangan Parameter...");
        await driver.executeScript(`
            let btn = Array.from(document.querySelectorAll('.btn-edit-keterangan'))
                .find(b => b.dataset.keterangan && b.dataset.keterangan.includes('Keterangan Parameter Uji Coba'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalKeterangan.show')), 5000);
        await sleep(400);

        inputKet = await driver.findElement(By.id('inputKeterangan'));
        await clearInput(driver, inputKet);
        await inputKet.sendKeys('Keterangan Parameter Uji Coba (Updated)');

        inputMin = await driver.findElement(By.id('inputNilaiMinimal'));
        await clearInput(driver, inputMin);
        await inputMin.sendKeys('20');

        inputMax = await driver.findElement(By.id('inputNilaiMaksimal'));
        await clearInput(driver, inputMax);
        await inputMax.sendKeys('95');
        await sleep(500);
        await takeScreenshotSession(driver, 'Test2_Tahap1_UbahKeterangan_Modal');

        await executeScriptClick('#formKeterangan button[type="submit"]');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelKeterangan')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_UbahKeterangan_Success');

        // Hapus Keterangan
        console.log("   -> [Tahap 1] Hapus Keterangan Parameter...");
        await driver.executeScript(`
            let btn = Array.from(document.querySelectorAll('.btn-hapus-keterangan'))
                .find(b => b.dataset.nama && b.dataset.nama.includes('Keterangan Parameter Uji Coba'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalHapusKeterangan.show')), 5000);
        await sleep(400);
        await takeScreenshotSession(driver, 'Test2_Tahap1_HapusKeterangan_Modal');
        await executeScriptClick('#formHapusKeterangan button[type="submit"]');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelKeterangan')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_HapusKeterangan_Success');

        // Navigasi Kembali ke Detail Inovasi
        console.log("   -> [Tahap 1] Navigasi Kembali ke Detail Inovasi...");
        await driver.executeScript(`
            let back = document.querySelector('a.btn-dark[href*="/inovasi"]') ||
                       Array.from(document.querySelectorAll('a')).find(a => a.innerText.includes('Kembali'));
            if (back) { back.scrollIntoView({block:'center'}); back.click(); }
        `);
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelDetailInovasi')), 10000);

        // ── 5. INDIKATOR TAHAP 1 — HAPUS INDIKATOR TEST & KEMBALI ────────────
        console.log("   -> [Tahap 1] Hapus Indikator Test...");
        searchInovasi = await driver.findElements(By.css('.dt-search input, .dataTables_filter input'));
        if (searchInovasi.length > 0) {
            await clearInput(driver, searchInovasi[0]);
            await searchInovasi[0].sendKeys('Indikator Uji Coba Selenium');
            await sleep(800);
        }

        await driver.executeScript(`
            let btn = Array.from(document.querySelectorAll('.btn-hapus-indikator'))
                .find(b => b.dataset.nama && b.dataset.nama.includes('Indikator Uji Coba Selenium'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalHapusIndikator.show')), 5000);
        await sleep(400);
        await takeScreenshotSession(driver, 'Test2_Tahap1_HapusIndikator_Modal');
        await executeScriptClick('#formHapusIndikator button[type="submit"]');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelDetailInovasi')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap1_HapusIndikator_Success');

        // Navigasi Kembali ke Tahap 1 List
        console.log("   -> [Tahap 1] Navigasi Kembali ke Halaman Indikator Tahap 1...");
        await driver.executeScript(`
            let back = document.querySelector('a.btn-dark[href*="/indikator/tahap-1"]') ||
                       Array.from(document.querySelectorAll('a')).find(a => a.innerText.includes('Kembali'));
            if (back) { back.scrollIntoView({block:'center'}); back.click(); }
        `);
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelTahap1')), 10000);

        // ── 6. INDIKATOR TAHAP 2 — LIST & FORMULASI ──────────────────────────
        console.log("   -> [Tahap 2] Navigasi Halaman Indikator Tahap 2...");
        await navigateTo('/indikator/tahap-2');
        await driver.wait(until.elementLocated(By.id('tabelTahap2')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap2_Index');

        console.log("   -> [Tahap 2] Uji DataTables Sub Event...");
        await testDataTables(driver, 'Indikator Tahap 2');

        console.log("   -> [Tahap 2] Uji Formulasi Bobot Nilai...");
        await driver.executeScript(`
            let trs = Array.from(document.querySelectorAll('#tabelTahap2 tbody tr'));
            let targetTr = trs.find(tr => tr.innerText.includes('INOTEK 2025')) || trs[0];
            let btn = targetTr ? targetTr.querySelector('.btn-open-formulasi') : null;
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(1000);

        let formulasi2Visible = await driver.executeScript("return document.querySelector('#modalFormulasi') !== null;");
        if (formulasi2Visible) {
            await driver.executeScript(`
                let inv = document.getElementById('inputNilaiInovasi');
                let per = document.getElementById('inputNilaiPeragaan');
                if (inv && per) {
                    inv.value = '50';
                    inv.dispatchEvent(new Event('input', { bubbles: true }));
                    per.value = '50';
                    per.dispatchEvent(new Event('input', { bubbles: true }));
                }
            `);
            await sleep(500);
            await takeScreenshotSession(driver, 'Test2_Tahap2_Formulasi_Modal');
            await driver.executeScript(`
                let btn = document.getElementById('btnSimpan2');
                if (btn && !btn.disabled) btn.click();
            `);
            await sleep(2000);
            await takeScreenshotSession(driver, 'Test2_Tahap2_Formulasi_Saved');
        }

        // ── 7. INDIKATOR TAHAP 2 — DETAIL INDIKATOR (CRUD) ───────────────────
        console.log("   -> [Tahap 2] Masuk ke Detail Indikator Tahap 2...");
        await driver.executeScript(`
            let trs = Array.from(document.querySelectorAll('#tabelTahap2 tbody tr'));
            let targetTr = trs.find(tr => tr.innerText.includes('INOTEK 2025')) || trs[0];
            let btnDetail = targetTr ? targetTr.querySelector('a.btn-primary[href*="/indikator"]') : null;
            if (btnDetail) {
                btnDetail.scrollIntoView({block:'center'});
                btnDetail.click();
            }
        `);
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelTahap2Detail')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap2_Detail_Index');

        console.log("   -> [Tahap 2] Uji DataTables Detail Indikator Tahap 2...");
        await testDataTables(driver, 'Detail Indikator Tahap 2');

        // Tambah Indikator Tahap 2
        console.log("   -> [Tahap 2] Tambah Indikator Nominator...");
        await executeScriptClick('#btnTambahIndikator');
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalIndikator.show')), 5000);
        await sleep(400);

        inputNamaIndikator = await driver.findElement(By.id('inputNamaIndikator'));
        await clearInput(driver, inputNamaIndikator);
        await inputNamaIndikator.sendKeys('Indikator Tahap 2 Selenium');

        let selectJenis2 = await driver.findElement(By.id('inputJenis'));
        await selectJenis2.sendKeys('Subtansi Inovasi');

        inputKet = await driver.findElement(By.id('inputKeterangan'));
        await clearInput(driver, inputKet);
        await inputKet.sendKeys('Keterangan Uji Coba Tahap 2');

        inputMin = await driver.findElement(By.id('inputNilaiMinimal'));
        await clearInput(driver, inputMin);
        await inputMin.sendKeys('50');

        inputMax = await driver.findElement(By.id('inputNilaiMaksimal'));
        await clearInput(driver, inputMax);
        await inputMax.sendKeys('100');
        await sleep(500);
        await takeScreenshotSession(driver, 'Test2_Tahap2_TambahIndikator_Modal');

        await executeScriptClick('#formIndikator button[type="submit"]');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelTahap2Detail')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap2_TambahIndikator_Success');

        // Ubah Indikator Tahap 2
        console.log("   -> [Tahap 2] Ubah Indikator Nominator...");
        let searchTahap2 = await driver.findElements(By.css('.dt-search input, .dataTables_filter input'));
        if (searchTahap2.length > 0) {
            await clearInput(driver, searchTahap2[0]);
            await searchTahap2[0].sendKeys('Indikator Tahap 2 Selenium');
            await sleep(1000);
        }

        await driver.executeScript(`
            let btn = document.querySelector('.btn-edit-indikator');
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalIndikator.show')), 5000);
        await sleep(400);

        inputNamaIndikator = await driver.findElement(By.id('inputNamaIndikator'));
        await clearInput(driver, inputNamaIndikator);
        await inputNamaIndikator.sendKeys('Indikator Tahap 2 Selenium (Updated)');

        selectJenis2 = await driver.findElement(By.id('inputJenis'));
        await selectJenis2.sendKeys('Peragaan');

        inputKet = await driver.findElement(By.id('inputKeterangan'));
        await clearInput(driver, inputKet);
        await inputKet.sendKeys('Keterangan Uji Coba Tahap 2 (Updated)');

        inputMin = await driver.findElement(By.id('inputNilaiMinimal'));
        await clearInput(driver, inputMin);
        await inputMin.sendKeys('60');

        inputMax = await driver.findElement(By.id('inputNilaiMaksimal'));
        await clearInput(driver, inputMax);
        await inputMax.sendKeys('95');
        await sleep(500);
        await takeScreenshotSession(driver, 'Test2_Tahap2_UbahIndikator_Modal');

        await executeScriptClick('#formIndikator button[type="submit"]');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelTahap2Detail')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap2_UbahIndikator_Success');

        // Hapus Indikator Tahap 2
        console.log("   -> [Tahap 2] Hapus Indikator Nominator...");
        searchTahap2 = await driver.findElements(By.css('.dt-search input, .dataTables_filter input'));
        if (searchTahap2.length > 0) {
            await clearInput(driver, searchTahap2[0]);
            await searchTahap2[0].sendKeys('Indikator Tahap 2 Selenium');
            await sleep(1000);
        }

        await driver.executeScript(`
            let btn = document.querySelector('.btn-hapus-indikator');
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        `);
        await sleep(800);
        await driver.wait(until.elementLocated(By.css('#modalHapus.show')), 5000);
        await sleep(400);
        await takeScreenshotSession(driver, 'Test2_Tahap2_HapusIndikator_Modal');
        await executeScriptClick('#formHapus button[type="submit"]');
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelTahap2Detail')), 10000);
        await takeScreenshotSession(driver, 'Test2_Tahap2_HapusIndikator_Success');

        // Navigasi Kembali ke Tahap 2 List
        console.log("   -> [Tahap 2] Navigasi Kembali ke Halaman Indikator Tahap 2...");
        await driver.executeScript(`
            let back = document.querySelector('a.btn-dark[href*="/indikator/tahap-2"]') ||
                       Array.from(document.querySelectorAll('a')).find(a => a.innerText.includes('Kembali'));
            if (back) { back.scrollIntoView({block:'center'}); back.click(); }
        `);
        await sleep(2000);
        await driver.wait(until.elementLocated(By.id('tabelTahap2')), 10000);

        // ── 8. LOGOUT ────────────────────────────────────────────────────────
        console.log("   -> Logout Admin...");
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

        console.log("✅ INDIKATOR TEST SELESAI (Semua Halaman & Fitur Berhasil Didemonstrasikan)");

    } catch (e) {
        console.error("❌ Error:", e);
        await takeScreenshotOnError(driver, 'Test2_IndikatorTest_Error');
        process.exit(1);
    } finally {
        await driver.quit();
    }
})();
