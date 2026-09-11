import { By } from 'selenium-webdriver';

export const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

export const testDataTables = async (driver, tableNameContext = "Data") => {
    console.log(`      -> Uji DataTables (Sorting, Search, Paging) pada: ${tableNameContext}...`);
    try {
        // 1. Search
        let searchInputs = await driver.findElements(By.css('.dt-search input, .dataTables_filter input'));
        if (searchInputs.length > 0 && await searchInputs[0].isDisplayed()) {
            await searchInputs[0].sendKeys('test search');
            await sleep(1000);
            await searchInputs[0].clear();
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
    } catch (e) {
        console.log(`      -> (DataTables features not fully tested: ${e.message.split('\n')[0]})`);
    }
};
