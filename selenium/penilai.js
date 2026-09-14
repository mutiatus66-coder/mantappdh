import { Builder, By, until } from 'selenium-webdriver';
import chrome from 'selenium-webdriver/chrome.js';
import { testDataTables, takeScreenshotSession, clickSmartLink, clearInput } from './helpers.js';

(async function runPenilaiWorkflow() {
    // Setup WebDriver
    let options = new chrome.Options();
    options.addArguments('--window-size=1920,1080');
    options.addArguments('--no-sandbox');
    options.addArguments('--disable-dev-shm-usage');
    options.addArguments('--ignore-certificate-errors');
    options.addArguments('--log-level=3');
    options.addArguments('--disable-gpu');
    if (process.env.HEADLESS === 'true') {
        options.addArguments('--headless=new');
    }
    
    let driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    const baseUrl = process.env.BASE_URL || "https://mantappdh.test";
    const emailPenilai = "ahmad.fauzi@example.com"; // Email dari PenilaiSeeder
    const passwordPenilai = "password";

    // Helper: sleep untuk jeda
    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    // Helper: press_button (mencari tombol berdasarkan text atau value)
    async function pressButton(text, required = true) {
        const xpath = `//button[contains(normalize-space(), '${text}')] | //input[@type='submit' and @value='${text}'] | //input[@type='button' and @value='${text}']`;
        for (let i = 0; i < 3; i++) {
            try {
                const timeout = required ? 10000 : 3000;
                const btn = await driver.wait(until.elementLocated(By.xpath(xpath)), timeout);
                await driver.wait(until.elementIsVisible(btn), timeout);
                await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", btn);
                return true;
            } catch (err) {
                if (err.name !== 'StaleElementReferenceError') {
                    if (!required) {
                        console.log(`   -> (Tombol '${text}' tidak ditemukan, dilewati)`);
                        return false;
                    }
                    throw err;
                }
                await sleep(500);
            }
        }
        if (!required) {
            console.log(`   -> (Tombol '${text}' tidak ditemukan, dilewati)`);
            return false;
        }
        throw new Error(`Gagal menekan tombol ${text} setelah retries.`);
    }

    // Helper: click_link (mencari tag <a> berdasarkan text)
    async function clickLink(text) {
        const xpath = `//a[contains(normalize-space(), '${text}')]`;
        for (let i = 0; i < 3; i++) {
            try {
                const link = await driver.wait(until.elementLocated(By.xpath(xpath)), 10000);
                await driver.wait(until.elementIsVisible(link), 10000);
                await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", link);
                return;
            } catch (err) {
                if (err.name !== 'StaleElementReferenceError') throw err;
                await sleep(500);
            }
        }
        throw new Error(`Gagal mengklik link ${text} setelah retries.`);
    }

    try {
        console.log("1. Membuka landing page...");
        await driver.get(baseUrl);
        await sleep(1000);

        console.log("2. Menekan tombol Login...");
        await clickLink('Login');
        await sleep(2000);

        console.log("3. Memasukkan kredensial login penilai...");
        await driver.wait(until.elementLocated(By.name('email')), 10000).sendKeys(emailPenilai);
        await driver.findElement(By.name('password')).sendKeys(passwordPenilai);
        
        console.log("   -> Menekan tombol Masuk...");
        await pressButton('Masuk');
        await sleep(3000);
        await takeScreenshotSession(driver, 'penilai_Masuk');

        console.log("4. Ke halaman Riwayat melalui sidebar...");
        await clickLink('Riwayat');
        await sleep(2000);

        console.log("5. Menekan Lihat Usulan...");
        await clickSmartLink(driver, 'Lihat Usulan');
        await sleep(2000);
        await testDataTables(driver, 'Riwayat Usulan', 'Diskominfo');

        console.log("6. Menggunakan search Datatables di Riwayat Usulan...");
        const searchInputRiwayat = await driver.wait(until.elementLocated(By.css('.dt-search input')), 5000);
        await clearInput(driver, searchInputRiwayat);
        await searchInputRiwayat.sendKeys('Diskominfo');
        await sleep(1000);

        console.log("7. Menekan interaksi + dari Datatables...");
        try {
            const dtrControlRiwayat = await driver.wait(until.elementLocated(By.css('td.dt-control, td.dtr-control')), 5000);
            await dtrControlRiwayat.click();
            await sleep(2000);
        } catch(e) {
            console.log("   -> Tombol + tidak ditemukan (mungkin layar tidak sempit)");
        }

        // Kembalikan filter pencarian agar tabel normal
        await clearInput(driver, searchInputRiwayat);
        await sleep(1000);

        console.log("8. Menekan tombol Kembali...");
        await clickLink('Kembali');
        await sleep(2000);

        console.log("9. Ke halaman Rekap Nilai melalui sidebar...");
        await clickLink('Rekap Nilai');
        await sleep(2000);

        console.log("10. Menekan Lihat Nilai...");
        await clickSmartLink(driver, 'Lihat Nilai');
        await sleep(2000);
        await testDataTables(driver, 'Rekap Nilai', 'Diskominfo');

        console.log("11. Menggunakan search Datatables di Rekap Nilai...");
        const searchInputRekap = await driver.wait(until.elementLocated(By.css('.dt-search input')), 5000);
        await clearInput(driver, searchInputRekap);
        await searchInputRekap.sendKeys('Diskominfo');
        await sleep(1000);

        console.log("12. Menekan interaksi + dari Datatables...");
        try {
            const dtrControlRekap = await driver.wait(until.elementLocated(By.css('td.dt-control, td.dtr-control')), 5000);
            await dtrControlRekap.click();
            await sleep(2000);
        } catch(e) {
            console.log("   -> Tombol + tidak ditemukan");
        }

        // Kembalikan filter pencarian agar tabel normal
        await clearInput(driver, searchInputRekap);
        await sleep(1000);

        console.log("13. Menekan tombol Kembali...");
        await clickLink('Kembali');
        await sleep(2000);

        console.log("14. Ke halaman Penilaian Tahap 1 melalui sidebar...");
        await clickLink('Penilaian Tahap 1');
        await sleep(2000);

        console.log("15. Menekan Lihat Nilai Verifikasi...");
        await clickSmartLink(driver, 'Lihat Nilai Verifikasi');
        await sleep(2000);
        await testDataTables(driver, 'Penilaian Tahap 1', 'BPBD');

        console.log("16. Memberi nilai kepada 10 inovator...");
        // Beri delay untuk memastikan DataTables render selesai
        await sleep(1000);
        let btnNilai = await driver.findElements(By.css('.btn-input-nilai'));
        let loopCount = Math.min(10, btnNilai.length);
        
        for (let i = 0; i < loopCount; i++) {
            // Re-fetch untuk mencegah StaleElementReferenceError jika tabel ter-render ulang
            btnNilai = await driver.findElements(By.css('.btn-input-nilai'));
            if (btnNilai[i]) {
                await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", btnNilai[i]);
                await sleep(1500);
                
                // Isi form nilai jika kosong (hanya yang terlihat di modal aktif)
                const inputs = await driver.findElements(By.css('.input-nilai-item'));
                for (let j = 0; j < inputs.length; j++) {
                    try {
                        if (await inputs[j].isDisplayed()) {
                            await inputs[j].clear();
                            const minVal = await inputs[j].getAttribute('min');
                            await inputs[j].sendKeys(minVal || '10');
                        }
                    } catch(e){}
                }
                
                await pressButton('Simpan Nilai');
                await sleep(1500); // Jeda ekstra agar modal tertutup sempurna
            }
            await takeScreenshotSession(driver, `penilai_Tahap1_SimpanNilai_Inovator_${i+1}`);
            
            let btnCatatan = await driver.findElements(By.css('.btn-catatan'));
            if (btnCatatan[i]) {
                // Klik modal catatan
                await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", btnCatatan[i]);
                await sleep(1500); // Tambah jeda agar modal termuat sempurna
                
                const textareas = await driver.findElements(By.css('textarea.form-control'));
                let success = false;
                // Ambil textarea yang terlihat (berada di modal aktif)
                for (const ta of textareas) {
                    try {
                        if (await ta.isDisplayed()) {
                            // Fokuskan kursor ke textarea dan gunakan executeScript fallback jika clear() gagal
                            await driver.executeScript("arguments[0].focus();", ta);
                            await ta.clear();
                            await ta.sendKeys('Catatan otomatis dari Selenium untuk inovator ke-' + (i+1));
                            success = true;
                            break;
                        }
                    } catch (e) {}
                }
                
                if (!success) {
                    console.log(`   -> [WARNING] Textarea catatan tidak ditemukan untuk index ${i}`);
                }
                
                await pressButton('Simpan Catatan');
                await sleep(1500); // Jeda ekstra agar modal tertutup sempurna
            }
            await takeScreenshotSession(driver, `penilai_Tahap1_SimpanCatatan_Inovator_${i+1}`);
        }

        console.log("17. Filter Total Nilai (Klik header Total Nilai)...");
        const thTotalNilai = await driver.findElement(By.xpath("//th[contains(normalize-space(), 'Total Nilai')]"));
        await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", thTotalNilai);
        await sleep(1500);
        // Klik dua kali agar urut dari yang terbesar (Descending)
        await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", thTotalNilai);
        await sleep(2000);

        console.log("18. Menekan check box select all (chk-all)...");
        const chkAll = await driver.wait(until.elementLocated(By.css('.chk-all')), 5000);
        await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", chkAll);
        await sleep(1500);

        console.log("19. Menekan tombol Simpan di Tahap 1...");
        await pressButton('Simpan');
        await sleep(3000);
        await takeScreenshotSession(driver, 'penilai_Tahap1_SimpanFinal');

        console.log("20. Menekan tombol Kembali...");
        await clickLink('Kembali');
        await sleep(2000);

        console.log("21. Ke halaman Penilaian Tahap 2 melalui sidebar...");
        await clickLink('Penilaian Tahap 2');
        await sleep(2000);

        console.log("22. Menekan Lihat Nilai Nominator...");
        await clickSmartLink(driver, 'Lihat Nilai Nominator');
        await sleep(2000);
        await testDataTables(driver, 'Penilaian Tahap 2', 'LaporKita');

        console.log("23. Menekan tombol Ranking...");
        await pressButton('Ranking');
        await sleep(2000);
        await takeScreenshotSession(driver, 'penilai_Tahap2_Ranking');

        console.log("24. Menekan tombol Simpan Ranking...");
        await pressButton('Simpan Ranking', false);
        await sleep(3000);
        await takeScreenshotSession(driver, 'penilai_Tahap2_SimpanRanking');

        console.log("25. Menekan tombol Kembali...");
        await clickLink('Kembali');
        await sleep(2000);

        console.log("26. Ke halaman Rekap Nilai...");
        await clickLink('Rekap Nilai');
        await sleep(2000);

        console.log("27. Menekan tombol Lihat Nilai (kembali ke Rekap Pendaftar)...");
        await clickSmartLink(driver, 'Lihat Nilai');
        await sleep(3000);

        console.log("28. Mencoba export PDF...");
        try {
            await driver.findElement(By.css('.buttons-pdf')).click();
            await sleep(2000);
        } catch(e) { console.log("   -> Tombol PDF tidak ditemukan"); }

        console.log("29. Mencoba export Excel...");
        try {
            await driver.findElement(By.css('.buttons-excel')).click();
            await sleep(2000);
        } catch(e) { console.log("   -> Tombol Excel tidak ditemukan"); }

        console.log("30. Menekan tombol Kembali...");
        await clickLink('Kembali');
        await sleep(2000);

        console.log("31. Log Out...");
        const profileBtn = await driver.wait(until.elementLocated(By.css('.cursor-pointer.symbol')), 5000);
        await driver.executeScript("arguments[0].click();", profileBtn);
        await sleep(1000);
        await clickLink('Sign Out');
        await sleep(2000);

        console.log("✅ Workflow Penilai E2E (Selenium) Selesai dengan Sukses!");

    } catch (err) {
        console.error("Terjadi kesalahan:", err);
    } finally {
        await driver.quit();
    }
})();
