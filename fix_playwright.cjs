const fs = require('fs');
const files = [
    'Playwright/Admin/Test1_MasterTest.js',
    'Playwright/Admin/Test2_IndikatorTest.js',
    'Playwright/Admin/Test3_PenilaianTest.js',
    'Playwright/Admin/Test4_InovasiTest.js',
    'Playwright/penilai.js',
    'Playwright/peserta.js'
];

files.forEach(f => {
    if (fs.existsSync(f)) {
        let content = fs.readFileSync(f, 'utf8');
        content = content.replace(/await chromium\.launch\(\{ headless: false \}\);/g, "await chromium.launch({ headless: false, args: ['--start-maximized'] });");
        content = content.replace(/viewport: \{ width: 1920, height: 1080 \}/g, 'viewport: null');
        content = content.replace(/viewport: \{ width: 1280, height: 720 \}/g, 'viewport: null');
        fs.writeFileSync(f, content);
        console.log('Updated ' + f);
    }
});
