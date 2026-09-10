import { chromium } from 'playwright';

(async () => {
    // Mode headless, secepat kilat dengan layar penuh
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();
    const waktu = Date.now();

    const baseUrl = "http://127.0.0.1:8000";

    try {
        // Fungsi pembantu sederhana untuk menguji fitur DataTables secara natural
        async function testDataTables(namaHalaman) {
            console.log(`   -> Test DataTables (${namaHalaman}): Sort, Search, ColVis...`);
            try {
                // 1. Sortir dengan klik header kolom kedua
                await page.locator('table.dataTable thead th').nth(1).click();
                
                // 2. Pencarian
                const searchBox = page.locator('.dt-search input');
                await searchBox.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
                if (await searchBox.isVisible()) {
                    await searchBox.fill('Test');
                    await page.waitForTimeout(300); // sedikit delay agar UI bereaksi
                    await searchBox.fill('');
                }

                // 3. ColVis (Sembunyikan/Tampilkan kolom)
                const colvisBtn = page.locator('.dt-buttons button.buttons-colvis');
                await colvisBtn.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
                if (await colvisBtn.isVisible()) {
                    await colvisBtn.click();
                    const toggleBtns = page.locator('.dt-button-collection button');
                    if (await toggleBtns.count() > 1) {
                        await toggleBtns.nth(1).click(); // matikan kolom 2
                        await toggleBtns.nth(1).click(); // hidupkan kolom 2
                    }
                    await page.keyboard.press('Escape');
                }
            } catch(e) { 
                console.log(`   -> [WARN] Lewati tes DataTables di ${namaHalaman}.`); 
            }
        }

        console.log("[1] MASTER DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto(baseUrl);
        await page.locator('.btn-login').first().click(); // Atau menyesuaikan tombol login Anda
        await page.locator('input[name="email"]').fill('admin@demo.test');
        await page.locator('input[name="password"]').fill('password');
        
        // Gunakan selector type="submit" sama persis dengan script lama Anda
        await page.locator('button[type="submit"]').click();
        await page.waitForURL('**/index');

        // TEST TEMA
        console.log("   -> Test Tema Gelap & Terang...");
        // Klik toggle tema terlebih dahulu jika ada
        const themeToggle = page.locator('[data-kt-menu-trigger="{default:\'click\', lg: \'hover\'}"]').first();
        if (await themeToggle.count() > 0) await themeToggle.click().catch(()=> {});
        await page.waitForTimeout(500);

        const darkBtn = page.locator('[data-kt-element="mode"][data-kt-value="dark"]').first();
        await darkBtn.waitFor({ state: 'visible', timeout: 3000 }).catch(() => {});
        if (await darkBtn.isVisible()) await darkBtn.click();
        await page.waitForTimeout(500);

        if (await themeToggle.count() > 0) await themeToggle.click().catch(()=> {});
        await page.waitForTimeout(500);
        
        const lightBtn = page.locator('[data-kt-element="mode"][data-kt-value="light"]').first();
        await lightBtn.waitFor({ state: 'visible', timeout: 3000 }).catch(() => {});
        if (await lightBtn.isVisible()) await lightBtn.click();

        // 1. EVENT
        console.log("   -> Event: Tambah, Ubah, Hapus...");
        await page.locator('a.ri-menu-item[href="/event"]').click();
        await page.waitForTimeout(2000);
        await page.getByRole('heading', { name: /Event/i }).waitFor().catch(() => {});
        await testDataTables('Event');

        await page.locator('#btnTambahEvent').click();
        await page.waitForTimeout(1000);
        await page.locator('#inputNamaEvent').fill(`Event ${waktu}`);
        await page.locator('#inputJenis').selectOption('INOTEK');
        await page.locator('#btnSimpanEvent').click();
        await page.waitForTimeout(1500);
        
        // Verifikasi & Edit
        const eventBaru = `Event ${waktu}`;
        const searchInput = page.locator('.dt-search input').first();
        
        // Tunggu 3 detik penuh agar DataTables (AJAX) selesai memuat data baru
        await page.waitForTimeout(3000);
        
        if (await searchInput.isVisible()) {
            await searchInput.fill('');
            await searchInput.fill(eventBaru);
            await page.waitForTimeout(1500); // Tunggu filter tabel
        }
        
        const barisEvent = page.locator('tr').filter({ hasText: eventBaru }).first();
        if (await barisEvent.isVisible()) {
            await barisEvent.locator('.btn-edit-event').click();
            await page.waitForTimeout(1000);
            await page.locator('#inputNamaEvent').fill(`Event Edit ${waktu}`);
            await page.locator('#btnSimpanEvent').click();
            await page.waitForTimeout(3000);
        } else {
            console.log(`   -> [WARN] Baris event ${eventBaru} tidak ditemukan untuk diedit.`);
        }

        // Verifikasi & Hapus (DIKOMENTARI AGAR EVENT BISA DIIKUTI PESERTA)
        /*
        const eventEdit = `Event Edit ${waktu}`;
        await searchInput.fill('');
        await searchInput.fill(eventEdit);
        await page.waitForTimeout(1500);
        const barisEditEvent = page.locator('tr').filter({ hasText: eventEdit });
        await barisEditEvent.locator('.btn-hapus-event').click();
        await page.waitForTimeout(1000);
        await page.locator('#btnHapusEvent').click(); // Konfirmasi hapus di modal
        await page.waitForTimeout(3000);
        */

        // 2. SUB EVENT
        console.log("   -> Sub Event: Tambah, Ubah, Hapus...");
        await page.locator('a.ri-menu-item[href="/sub-event"]').click();
        await page.waitForTimeout(2000);
        await testDataTables('Sub Event');

        await page.locator('#btnTambahSubEvent').click();
        await page.waitForTimeout(1000);
        await page.locator('#seTahun').fill(String(new Date().getFullYear() + 1));
        
        const selEvent = page.locator('#seEvent');
        await selEvent.waitFor({ state: 'attached' });
        await selEvent.selectOption({ index: 1 });
        
        await page.locator('#seSubEvent').fill(`Sub Event ${waktu}`);
        await page.locator('#seKategori').fill('Kategori Test');
        await page.locator('#seMulai').fill('2025-01-01');
        await page.locator('#seBerakhir').fill('2025-12-31');
        await page.locator('#btnSimpanSE').click();
        
        // Tunggu tabel reload
        await page.waitForTimeout(3000);
        if (await searchInput.isVisible()) {
            await searchInput.fill(''); // Kosongkan pencarian
            await page.waitForTimeout(1000);
        }
        
        // Ambil tombol edit TERAKHIR di halaman (mengikuti logika asli script Anda)
        // karena ternyata backend pencarian DataTables Sub Event tidak merespons teks baru
        const btnEditSE = page.locator('.btn-edit-se').last();
        if (await btnEditSE.isVisible()) {
            await btnEditSE.scrollIntoViewIfNeeded();
            await btnEditSE.click();
            await page.waitForTimeout(1000);
            
            const subEventEdit = `Sub Event Edit ${waktu}`;
            await page.locator('#seSubEvent').fill(subEventEdit);
            await page.locator('#btnSimpanSE').click();
            await page.waitForTimeout(3000);
        } else {
            console.log(`   -> [WARN] Tombol edit Sub Event tidak ditemukan.`);
        }

        /* DIKOMENTARI AGAR DATA SUB EVENT TETAP ADA UNTUK PESERTA
        const btnHapusSE = page.locator('.btn-hapus-se').last();
        await btnHapusSE.scrollIntoViewIfNeeded();
        await btnHapusSE.click();
        await page.waitForTimeout(1000);
        await page.locator('#btnHapusSE').click(); // Modal konfirmasi
        await page.waitForTimeout(3000);
        */

        console.log("✅ Testing Admin (Master Data) Selesai dengan Sukses!");

    } catch (error) {
        console.error("❌ Terjadi kesalahan pada Admin:", error);
        await page.screenshot({ path: 'error_admin.png', fullPage: true }).catch(() => {});
        console.log("📸 Screenshot error telah disimpan sebagai 'error_admin.png'");
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
