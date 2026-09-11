const fs = require('fs');

function patchFile(file) {
    let c = fs.readFileSync(file, 'utf8');
    c = c.replace("import {, takeScreenshotSession } from '../helpers.js';", "import { testDataTables, takeScreenshotSession } from '../helpers.js';");
    c = c.replace("import {, takeScreenshotSession } from './helpers.js';", "import { testDataTables, takeScreenshotSession } from './helpers.js';");
    c = c.replace("import { sleep, testDataTables, takeScreenshotOnError } from '../helpers.js';", "import { sleep, testDataTables, takeScreenshotOnError, takeScreenshotSession } from '../helpers.js';");
    fs.writeFileSync(file, c);
}

patchFile('Selenium/peserta.js');
patchFile('Selenium/penilai.js');
patchFile('Selenium/Admin/Test1_MasterTest.js');
patchFile('Selenium/Admin/Test2_IndikatorTest.js');
patchFile('Selenium/Admin/Test3_InovasiTest.js');
patchFile('Selenium/Admin/Test4_PenilaianTest.js');

console.log('Fixed imports 2');
