const fs = require('fs');
const glob = require('fs').promises; // wait I'll just use sync

function patchFile(file, successMessage, sessionName) {
    let c = fs.readFileSync(file, 'utf8');
    if(!c.includes('takeScreenshotSession')) {
        c = c.replace(/import {([^}]+)} from '..helpers.js';/, "import {\, takeScreenshotSession } from '../helpers.js';");
        c = c.replace(/import {([^}]+)} from '.\/helpers.js';/, "import {\, takeScreenshotSession } from './helpers.js';");
        
        c = c.replace('console.log("? ' + successMessage + '");', 'console.log("? ' + successMessage + '");\n        await takeScreenshotSession(driver, "' + sessionName + '");');
        fs.writeFileSync(file, c);
    }
}

patchFile('Selenium/peserta.js', 'Testing Selenium (Node.js) Selesai dengan Sukses!', 'peserta_success');
patchFile('Selenium/penilai.js', 'Workflow Penilai E2E (Selenium) Selesai dengan Sukses!', 'penilai_success');
patchFile('Selenium/Admin/Test1_MasterTest.js', 'Workflow Master E2E (Selenium) Selesai dengan Sukses!', 'admin_master_success');
patchFile('Selenium/Admin/Test2_IndikatorTest.js', 'Workflow Indikator E2E (Selenium) Selesai dengan Sukses!', 'admin_indikator_success');
patchFile('Selenium/Admin/Test3_InovasiTest.js', 'Workflow Inovasi E2E (Selenium) Selesai dengan Sukses!', 'admin_inovasi_success');
patchFile('Selenium/Admin/Test4_PenilaianTest.js', 'Workflow Penilaian Tahap 1 E2E (Selenium) Selesai dengan Sukses!', 'admin_penilaian_success');

console.log('Fixed');
