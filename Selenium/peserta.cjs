const { Builder, By, until } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');
const fs = require('fs');
const path = require('path');

(async function runPesertaWorkflow() {
    // Setup WebDriver
    let options = new chrome.Options();
    // options.addArguments('--headless'); // Uncomment untuk mode tanpa UI (Headless Mode)
    options.addArguments('--log-level=3');
    options.addArguments('--disable-logging');
    
    let driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    const baseUrl = "http://mantappdh.test";
    const uniqueEmail = `peserta_${Date.now()}@test.com`;

    // Helpers untuk membuat file dummy (PDF & JPG berdasarkan magic bytes)
    const pdfPath = path.join(__dirname, 'contoh.pdf');
    const jpgPath = path.join(__dirname, 'contoh.jpg');
    fs.writeFileSync(pdfPath, Buffer.from("255044462D312E340A", 'hex'));
    fs.writeFileSync(jpgPath, Buffer.from("FFD8FFE000104A4649460001", 'hex'));

    // Helper: sleep untuk jeda
    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    // Helper: press_button (mencari tombol berdasarkan text atau value)
    async function pressButton(text) {
        const xpath = `//button[contains(normalize-space(), '${text}')] | //input[@type='submit' and @value='${text}'] | //input[@type='button' and @value='${text}']`;
        for (let i = 0; i < 3; i++) {
            try {
                const btn = await driver.wait(until.elementLocated(By.xpath(xpath)), 10000);
                await driver.wait(until.elementIsVisible(btn), 10000);
                await driver.executeScript("arguments[0].scrollIntoView({block: 'center'}); arguments[0].click();", btn);
                return;
            } catch (err) {
                if (err.name !== 'StaleElementReferenceError') throw err;
                await sleep(500);
            }
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

        console.log("2. Menekan tombol PENDAFTARAN...");
        const btnRegister = await driver.wait(until.elementLocated(By.css('.btn-register')), 10000);
        await btnRegister.click();
        await sleep(1000);

        console.log("3. Mengisi form pendaftaran...");
        await driver.wait(until.elementLocated(By.name('name')), 10000).sendKeys('Peserta Baru Real');
        await driver.findElement(By.name('email')).sendKeys(uniqueEmail);
        await driver.findElement(By.name('password')).sendKeys('Password123!');
        await driver.findElement(By.name('password_confirmation')).sendKeys('Password123!');
        
        await driver.findElement(By.name('captcha_verified')).click();
        await sleep(2000);

        console.log("   -> Menekan Daftar...");
        await pressButton('Daftar');
        await sleep(3000);

        console.log("4. Ke halaman Riwayat melalui sidebar...");
        await clickLink('Riwayat');
        await sleep(2000);

        console.log("5. Menekan Kelola Usulan...");
        await clickLink('Kelola Usulan');
        await sleep(2000);

        console.log("6. Menekan Tambah Usulan...");
        await pressButton('Tambah Usulan');
        await sleep(1000);

        console.log("7. Mengisi Form Langkah 1...");
        await driver.wait(until.elementLocated(By.name('nama_inovasi')), 10000).sendKeys('Inovasi E2E Test');
        await driver.findElement(By.name('judul')).sendKeys('Judul Inovasi Test');
        
        // Select bidang_id (ambil option kedua, yang index 1 setelah placeholder)
        await driver.findElement(By.css('select[name="bidang_id"] option:nth-child(2)')).click();
        
        await driver.findElement(By.name('interaksi')).sendKeys('Aplikasi Web');
        
        // Select kategori by value
        await driver.findElement(By.css('select[name="kategori"] option[value="umum"]')).click();
        
        await driver.findElement(By.name('inovator')).sendKeys('Instansi Test');
        await driver.findElement(By.name('ketua_nama')).sendKeys('Budi Test');
        await driver.findElement(By.name('ketua_email')).sendKeys(uniqueEmail);
        await driver.findElement(By.name('ketua_wa')).sendKeys('081234567890');
        await driver.findElement(By.name('alamat_ketua')).sendKeys('Jl. Test No. 123');
        await driver.findElement(By.name('ktp')).sendKeys('1234567890123456');
        
        await pressButton('Selanjutnya');
        await sleep(1000);

        console.log("8. Mengisi Form Langkah 2...");
        await driver.wait(until.elementLocated(By.name('latar_belakang')), 10000).sendKeys('Latar Belakang ...');
        await driver.findElement(By.name('kondisi_sebelumnya')).sendKeys('Kondisi Sebelumnya ...');
        await driver.findElement(By.name('sasaran_tujuan')).sendKeys('Sasaran Tujuan ...');
        await driver.findElement(By.name('deskripsi')).sendKeys('Deskripsi ...');
        await driver.findElement(By.name('cara_kerja')).sendKeys('Cara Kerja ...');
        await driver.findElement(By.name('keunggulan')).sendKeys('Keunggulan ...');
        await driver.findElement(By.name('hasil_diharapkan')).sendKeys('Hasil yang Diharapkan ...');
        await driver.findElement(By.name('manfaat')).sendKeys('Manfaat ...');
        await driver.findElement(By.name('rencana_berkelanjutan')).sendKeys('Rencana Berkelanjutan ...');
        
        await pressButton('Selanjutnya');
        await sleep(1000);

        console.log("9. Mengisi Form Langkah 3 (Upload File)...");
        await driver.wait(until.elementLocated(By.name('file_surat_pernyataan')), 10000).sendKeys(pdfPath);
        await driver.findElement(By.name('file_proposal')).sendKeys(pdfPath);
        await driver.findElement(By.name('file_gambar')).sendKeys(jpgPath);
        await driver.findElement(By.name('link_video')).sendKeys('https://youtube.com/watch?v=123');
        await sleep(2000);
        
        console.log("   -> Menekan Simpan Usulan...");
        await pressButton('Simpan Usulan');
        await sleep(3000);

        console.log("10. Kembali ke menu Riwayat...");
        await clickLink('Kembali');
        await sleep(2000);

        console.log("11. Log Out...");
        await driver.findElement(By.css('.cursor-pointer.symbol')).click();
        await sleep(1000);
        await clickLink('Sign Out');
        await sleep(2000);

        console.log("✅ Testing Selenium (Node.js) Selesai dengan Sukses!");

    } catch (err) {
        console.error("❌ Terjadi kesalahan:", err);
    } finally {
        await driver.quit();
        // Hapus dummy files
        if (fs.existsSync(pdfPath)) fs.unlinkSync(pdfPath);
        if (fs.existsSync(jpgPath)) fs.unlinkSync(jpgPath);
    }
})();
