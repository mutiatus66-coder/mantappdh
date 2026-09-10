import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const page = await context.newPage();
    const baseUrl = "http://127.0.0.1:8000";

    try {
        console.log("[4] PENILAIAN DATA (Playwright)");
        
        console.log("   -> Login Admin...");
        await page.goto(baseUrl);
        await page.locator('.btn-login').first().click();
        await page.locator('input[name="email"]').fill('admin@demo.test');
        await page.locator('input[name="password"]').fill('password');
        await page.locator('button[type="submit"]').click();
        await page.waitForURL('**/index');

        // PENILAIAN TAHAP 1
        console.log("   -> Penilaian Tahap 1 (Simpan Kurasi)...");
        await page.goto(baseUrl + '/penilaian/tahap-1');
        await page.waitForTimeout(2000);
        
        // Cari card "Lihat Nilai Verifikasi" yang punya progress > 0% (ada data penilaian)
        // Jika tidak ada yang > 0%, klik yang pertama saja
        const verifikasiLink = await page.evaluate(() => {
            const cards = document.querySelectorAll('.ec-card');
            let bestHref = null;
            let firstHref = null;
            for (const card of cards) {
                const link = card.querySelector('a.btn.btn-info');
                if (!link) continue;
                if (!firstHref) firstHref = link.getAttribute('href');
                // Scan ALL progress labels to find the one with "X / Y" format
                const labels = card.querySelectorAll('.ec-progress-label');
                for (const labelEl of labels) {
                    const match = labelEl.textContent.match(/(\d+)\s*\/\s*(\d+)/);
                    if (match && parseInt(match[2]) > 0) {
                        bestHref = link.getAttribute('href');
                        break;
                    }
                }
                if (bestHref) break;
            }
            return bestHref || firstHref;
        });
        
        if (verifikasiLink) {
            const tahap1Url = verifikasiLink.startsWith('http') ? verifikasiLink : baseUrl + verifikasiLink;
            await page.goto(tahap1Url);
            await page.waitForTimeout(2000);
            
            // Force-centang SEMUA checkbox yang enabled (baik Umum maupun Pelajar)
            const checkedCount = await page.evaluate(() => {
                const checkboxes = document.querySelectorAll('.chk-row:not([disabled])');
                let count = 0;
                checkboxes.forEach(cb => {
                    if (!cb.checked) { cb.checked = true; cb.dispatchEvent(new Event('change', { bubbles: true })); }
                    count++;
                });
                return count;
            });
            console.log(`   -> (${checkedCount} checkbox dicentang untuk kurasi)`);
            await page.waitForTimeout(500);

            if (checkedCount > 0) {
                // Klik tombol simpan pertama (Umum)
                await page.evaluate(() => {
                    const btn = document.querySelector('.btn-rv-simpan');
                    if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
                });
                // Tunggu fetch + location.reload (1.5s) + halaman reload
                await page.waitForTimeout(5000);
                
                // Setelah reload, cek apakah ada tab Pelajar dan simpan juga
                const tabPelajarExists = await page.locator('#tab-pelajar').count();
                if (tabPelajarExists > 0 && await page.locator('#tab-pelajar').isVisible()) {
                    await page.locator('#tab-pelajar').click();
                    await page.waitForTimeout(1000);
                    
                    const checkedPelajar = await page.evaluate(() => {
                        const panel = document.getElementById('panel-pelajar');
                        if (!panel) return 0;
                        let count = 0;
                        panel.querySelectorAll('.chk-row:not([disabled])').forEach(cb => {
                            if (!cb.checked) { cb.checked = true; cb.dispatchEvent(new Event('change', { bubbles: true })); }
                            count++;
                        });
                        return count;
                    });
                    
                    if (checkedPelajar > 0) {
                        await page.evaluate(() => {
                            const panel = document.getElementById('panel-pelajar');
                            if (!panel) return;
                            const btn = panel.querySelector('.btn-rv-simpan');
                            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
                        });
                        await page.waitForTimeout(5000);
                    }
                }
            } else {
                console.log("   -> (Tidak ada checkbox yang enabled, belum semua penilai menilai)");
            }
        } else {
            console.log("   -> (Tombol Lihat Nilai Verifikasi tidak ditemukan)");
        }

        // PENILAIAN TAHAP 2
        console.log("   -> Penilaian Tahap 2 (Auto Ranking)...");
        await page.goto(baseUrl + '/penilaian/tahap-2');
        await page.waitForTimeout(2000);
        
        // Sama seperti Tahap 1: cari card yang punya data
        const nominatorLink = await page.evaluate(() => {
            const cards = document.querySelectorAll('.ec-card');
            let bestHref = null;
            let firstHref = null;
            for (const card of cards) {
                const link = card.querySelector('a.btn.btn-info');
                if (!link) continue;
                if (!firstHref) firstHref = link.getAttribute('href');
                const labels = card.querySelectorAll('.ec-progress-label');
                for (const labelEl of labels) {
                    const match = labelEl.textContent.match(/(\d+)\s*\/\s*(\d+)/);
                    if (match && parseInt(match[2]) > 0) {
                        bestHref = link.getAttribute('href');
                        break;
                    }
                }
                if (bestHref) break;
            }
            return bestHref || firstHref;
        });
        
        if (nominatorLink) {
            const tahap2Url = nominatorLink.startsWith('http') ? nominatorLink : baseUrl + nominatorLink;
            await page.goto(tahap2Url);
            await page.waitForTimeout(2000);
            
            const btnAutoRanking = page.locator('.btn-auto-ranking').first();
            if (await btnAutoRanking.count() > 0) {
                await page.evaluate(() => {
                    let btn = document.querySelector('.btn-auto-ranking'); 
                    if(btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
                });
                await page.waitForTimeout(2000);
            }

            const btnSimpanRanking = page.locator('.btn-simpan-ranking').first();
            if (await btnSimpanRanking.count() > 0) {
                await page.evaluate(() => {
                    let btn2 = document.querySelector('.btn-simpan-ranking'); 
                    if(btn2) { btn2.scrollIntoView({block:'center'}); btn2.click(); }
                });
                await page.waitForTimeout(2000);
            }
        } else {
            console.log("   -> (Tidak ada card Tahap 2 dengan data nominator)");
        }

        // AUDIT LOG (RIWAYAT HALAMAN)
        console.log("   -> Buka Audit Log / Riwayat Halaman...");
        const btnHistory = page.locator('button[onclick*="toggleHistoryPanel"]');
        await btnHistory.waitFor({ state: 'visible', timeout: 5000 }).catch(() => {});
        if (await btnHistory.isVisible()) {
            await btnHistory.click();
            await page.waitForTimeout(1000); // Tunggu animasi
            await page.evaluate(() => {
                if(typeof window.closeHistoryPanel === 'function') window.closeHistoryPanel();
            });
            await page.waitForTimeout(500);
        }

        // LOGOUT
        console.log("   -> Logout...");
        await page.goto(baseUrl);
        await page.waitForTimeout(2000);
        await page.evaluate(() => {
            let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); 
            if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }
        });
        await page.waitForTimeout(1000);
        await page.evaluate(() => {
            let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out') || a.textContent.includes('Log Out')); 
            if(signOut) { signOut.click(); }
        });
        await page.waitForTimeout(2000);

        console.log("✅ PENILAIAN TEST SELESAI");

    } catch (e) {
        console.error("❌ Error:", e);
        process.exit(1);
    } finally {
        await browser.close();
    }
})();
