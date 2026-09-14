import { By, Key } from 'selenium-webdriver';

export const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

import fs from 'fs';
import path from 'path';

export const takeScreenshotOnError = async (driver, errorName) => {
    try {
        const errorDir = path.join(process.cwd(), 'selenium', 'screenshot');
        if (!fs.existsSync(errorDir)) {
            fs.mkdirSync(errorDir, { recursive: true });
        }
        const cleanErrorName = errorName.replace(/_\d{10,}$/, '');
        const filename = path.join(errorDir, `error_${cleanErrorName}.png`);
        const image = await driver.takeScreenshot();
        fs.writeFileSync(filename, image, 'base64');
        console.log(`📸 Screenshot error disimpan di: ${filename}`);
    } catch (err) {
        console.error("Gagal mengambil screenshot:", err);
    }
};

export const takeScreenshotSession = async (driver, sessionName) => {
    try {
        const screenshotDir = path.join(process.cwd(), 'selenium', 'screenshot');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
        const cleanSessionName = sessionName.replace(/_\d{10,}$/, '');
        const filename = path.join(screenshotDir, `${cleanSessionName}.png`);
        const image = await driver.takeScreenshot();
        fs.writeFileSync(filename, image, 'base64');
        console.log(`📸 Screenshot sesi disimpan di: ${filename}`);
    } catch (err) {
        console.error("Gagal mengambil screenshot sesi:", err);
    }
};

export const clickSmartLink = async (driver, text) => {
    console.log(`      -> Mencari tombol pintar: '${text}' pada sub-event yang berisi usulan...`);
    const found = await driver.executeScript(`
        let text = arguments[0];
        let cards = Array.from(document.querySelectorAll('.ec-card, .card'));
        let inotekCard = cards.find(c => c.innerText.includes('INOTEK 2025') && c.innerText.includes(text));
        if(inotekCard) {
            let a = inotekCard.querySelector('a.btn') || inotekCard.querySelector('a');
            if(a) {
                a.scrollIntoView({block:'center'});
                a.click();
                return true;
            }
        }

        let validCards = cards.filter(c => 
            !c.innerText.includes('/ 0 sudah dinilai') && 
            !c.innerText.includes('/ 0 dinilai') && 
            !c.innerText.includes(' 0 total usulan') &&
            c.innerText.includes(text)
        );
        
        let target = null;
        if(validCards.length > 0) {
            let a = validCards[0].querySelector('a.btn');
            if(!a) a = validCards[0].querySelector('a');
            if(a) target = a;
        }
        
        if(!target) {
            let allA = Array.from(document.querySelectorAll('a'));
            target = allA.find(a => a.innerText.includes(text));
        }
        
        if(target) {
            target.scrollIntoView({block:'center'});
            target.click();
            return true;
        }
        return false;
    `, text);
    
    if(!found) {
        console.log(`      -> (Peringatan: Tidak menemukan tombol pintar untuk '${text}', fallback ke by XPath)`);
        const el = await driver.wait(driver.findElement(By.xpath(`//a[contains(., '${text}')]`)), 10000);
        await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", el);
    }
};

export const clearInput = async (driver, element) => {
    try {
        await driver.executeScript("arguments[0].focus();", element);
        await element.clear();
        await element.sendKeys(Key.CONTROL, 'a', Key.BACK_SPACE);
        await driver.executeScript(`
            arguments[0].value = '';
            arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
            arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        `, element);
        await sleep(200);
    } catch (e) {
        try {
            await driver.executeScript("arguments[0].value = ''; arguments[0].dispatchEvent(new Event('input', { bubbles: true }));", element);
        } catch (err) {}
    }
};

export const testDataTables = async (driver, tableNameContext = "Data", searchKeyword = null) => {
    console.log(`      -> Uji DataTables (Sorting, Search, Paging) pada: ${tableNameContext}...`);
    try {
        // Keyword berdasarkan data seeder masing-masing halaman
        const defaultSeederKeywords = {
            'Riwayat Usulan': 'Diskominfo',
            'Rekap Nilai': 'Diskominfo',
            'Penilaian Tahap 1': 'BPBD',
            'Penilaian Tahap 2': 'LaporKita',
            'Kelola Usulan': 'Inovasi',
            'Event': 'INOTEK',
            'Sub Event': 'INOTEK',
            'Bidang': 'Teknologi',
            'User': 'Admin',
            'Penilai': 'Fauzi',
            'Pengumuman': 'Jadwal',
            'Indikator Tahap 1': 'INOTEK',
            'Detail Inovasi': 'Orisinalitas',
            'Detail Keterangan': 'Inovasi',
            'Indikator Tahap 2': 'INOTEK',
            'Detail Indikator Tahap 2': 'Presentasi',
            'Riwayat Inovasi': 'Diskominfo',
        };
        const keyword = searchKeyword || defaultSeederKeywords[tableNameContext] || 'a';

        // 1. Search
        let searchInputs = await driver.findElements(By.css('.dt-search input, .dataTables_filter input'));
        if (searchInputs.length > 0 && await searchInputs[0].isDisplayed()) {
            await clearInput(driver, searchInputs[0]);
            await searchInputs[0].sendKeys(keyword);
            await sleep(1000);
            await takeScreenshotSession(driver, `${tableNameContext.replace(/\s+/g, '_')}_Search`);
            await clearInput(driver, searchInputs[0]);
            await sleep(1000);
        }

        // 2. Length Menu
        let lengthSelects = await driver.findElements(By.css('.dt-length select, .dataTables_length select'));
        if (lengthSelects.length > 0 && await lengthSelects[0].isDisplayed()) {
            await lengthSelects[0].sendKeys('25');
            await sleep(1000);
            await lengthSelects[0].sendKeys('10');
            await sleep(1000);
        }

        // 3. Sorting (Click first sortable column header twice)
        let headers = await driver.findElements(By.css('table.dataTable thead th.dt-orderable-asc, table.dataTable thead th.sorting, table.dataTable thead th.dt-ordering-asc'));
        if (headers.length > 0 && await headers[0].isDisplayed()) {
            await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", headers[0]);
            await sleep(1000);
            await takeScreenshotSession(driver, `${tableNameContext.replace(/\s+/g, '_')}_Sort_1`);
            await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", headers[0]);
            await sleep(1000);
        }

        // 4. Pagination
        let nextBtns = await driver.findElements(By.css('.dt-paging .next, .paginate_button.next'));
        if (nextBtns.length > 0 && await nextBtns[0].isDisplayed()) {
            let className = await nextBtns[0].getAttribute('class');
            if (!className.includes('disabled')) {
                await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", nextBtns[0]);
                await sleep(1000);
                let prevBtns = await driver.findElements(By.css('.dt-paging .previous, .paginate_button.previous'));
                if (prevBtns.length > 0 && await prevBtns[0].isDisplayed()) {
                    await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", prevBtns[0]);
                    await sleep(1000);
                }
            }
        }

        // 5. Column Visibility (ColVis)
        let colVisBtns = await driver.findElements(By.xpath("//button[contains(., 'Column visibility') or contains(@class, 'buttons-colvis')]"));
        if (colVisBtns.length > 0 && await colVisBtns[0].isDisplayed()) {
            await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", colVisBtns[0]);
            await sleep(1000);
            
            // Click the first item in the dropdown
            let colVisItems = await driver.findElements(By.css('.dt-button-collection button.dt-button'));
            if (colVisItems.length > 0) {
                await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", colVisItems[0]);
                await sleep(500);
                // Click again to toggle back
                await driver.executeScript("arguments[0].scrollIntoView({block:'center'}); arguments[0].click();", colVisItems[0]);
                await sleep(500);
            }
            
            // Close the dropdown (click background, hit ESC, and remove overlay)
            await driver.executeScript(`
                let bg = document.querySelector('.dt-button-background');
                if (bg) bg.click();
            `);
            await driver.findElement(By.css('body')).sendKeys(Key.ESCAPE);
            await sleep(500);
            await driver.executeScript(`
                document.querySelectorAll('.dt-button-background, .dt-button-collection').forEach(el => el.remove());
            `);
            await sleep(500);
        }
        // Final cleanup for any lingering overlays
        await driver.executeScript(`
            document.querySelectorAll('.dt-button-background, .dt-button-collection').forEach(el => el.remove());
        `);
    } catch (e) {
        console.log(`      -> (DataTables features not fully tested: ${e.message.split('\n')[0]})`);
    }
};
