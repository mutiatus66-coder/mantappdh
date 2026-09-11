const fs = require('fs');

function patchFile(file) {
    let lines = fs.readFileSync(file, 'utf8').split('\n');
    let patched = false;
    let screenshotCounter = 1;
    
    for (let i = 0; i < lines.length; i++) {
        // Look for typical action verbs
        if (lines[i].includes('Simpan') || lines[i].match(/btn(Tambah|Simpan|Hapus|Kirim)/) || lines[i].includes('pressButton') || lines[i].includes('executeScriptClick')) {
            // Check if next line is a sleep
            if (i + 1 < lines.length && lines[i+1].includes('await sleep(')) {
                // Check if line after sleep is already a screenshot
                if (i + 2 < lines.length && !lines[i+2].includes('takeScreenshotSession(')) {
                    let actionName = "Aksi";
                    let match = lines[i].match(/(Tambah|Simpan|Hapus|Kirim|Daftar|Masuk|Login)/i);
                    if (match) actionName = match[0];
                    let prefix = file.split('/').pop().replace('.js', '');
                    let ssCode = "        await takeScreenshotSession(driver, " + prefix + "_" + actionName + "_" + "\);";
                    lines.splice(i + 2, 0, ssCode);
                    patched = true;
                    i++; // skip the newly inserted line
                }
            }
        }
    }
    
    if (patched) {
        fs.writeFileSync(file, lines.join('\n'));
        console.log('Patched ' + file);
    }
}

patchFile('Selenium/peserta.js');
patchFile('Selenium/penilai.js');
patchFile('Selenium/Admin/Test1_MasterTest.js');
patchFile('Selenium/Admin/Test2_IndikatorTest.js');
patchFile('Selenium/Admin/Test3_InovasiTest.js');
patchFile('Selenium/Admin/Test4_PenilaianTest.js');
