import os
import sys
import time

# Load helpers and resolve venv path before importing selenium
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
import helpers
from helpers import sleep, test_data_tables, take_screenshot_session, take_screenshot_on_error, clear_input

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def test2_indikator():
    options = Options()
    options.add_argument('--window-size=1920,1080')
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--ignore-certificate-errors')
    if os.environ.get('HEADLESS') == 'true':
        options.add_argument('--headless=new')
        options.add_argument('--disable-gpu')

    driver = webdriver.Chrome(options=options)
    baseUrl = os.environ.get('BASE_URL', 'https://mantappdh.test')

    def execute_script_click(selector):
        el = driver.find_element(By.CSS_SELECTOR, selector)
        driver.execute_script("document.querySelectorAll('.dt-button-background').forEach(b => b.remove());")
        helpers.highlight_and_click(driver, el, duration=0.8)
        sleep(1.5)

    def navigate_to(path):
        try:
            links = driver.find_elements(By.CSS_SELECTOR, f'a.ri-menu-item[href="{path}"]')
            if links and links[0].is_displayed():
                helpers.highlight_and_click(driver, links[0], duration=0.8)
                sleep(2)
            else:
                driver.get(baseUrl + path)
                sleep(2)
        except Exception:
            driver.get(baseUrl + path)
            sleep(2)

    try:
        print("[2] INDIKATOR DATA (Selenium Python - Seluruh Fungsi & Halaman)")

        # 1. LOGIN ADMIN
        print("   -> Login Admin (dengan highlight penuh)...")
        helpers.login_admin_with_highlights(driver, baseUrl)

        # 2. INDIKATOR TAHAP 1 — LIST & FORMULASI
        print("   -> [Tahap 1] Navigasi Halaman Utama...")
        navigate_to('/indikator/tahap-1')
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap1')))
        take_screenshot_session(driver, 'Test2_Tahap1_Index')

        print("   -> [Tahap 1] Uji DataTables Sub Event...")
        test_data_tables(driver, 'Indikator Tahap 1')

        print("   -> [Tahap 1] Uji Formulasi Bobot Nilai...")
        driver.execute_script("""
            let trs = Array.from(document.querySelectorAll('#tabelTahap1 tbody tr'));
            let targetTr = trs.find(tr => tr.innerText.includes('INOTEK 2025')) || trs[0];
            let btn = targetTr ? targetTr.querySelector('.btn-open-formulasi1') : null;
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(1)

        formulasi1_visible = driver.execute_script("return document.querySelector('#modalFormulasi1') !== null;")
        if formulasi1_visible:
            driver.execute_script("""
                let mak = document.getElementById('inputNilaiMakalah');
                let sub = document.getElementById('inputNilaiSubstansi');
                if (mak && sub) {
                    mak.value = '40';
                    mak.dispatchEvent(new Event('input', { bubbles: true }));
                    sub.value = '60';
                    sub.dispatchEvent(new Event('input', { bubbles: true }));
                }
            """)
            sleep(0.5)
            take_screenshot_session(driver, 'Test2_Tahap1_Formulasi_Modal')
            driver.execute_script("""
                let btn = document.getElementById('btnSimpan1');
                if (btn && !btn.disabled) btn.click();
            """)
            sleep(2)
            take_screenshot_session(driver, 'Test2_Tahap1_Formulasi_Saved')

        # 3. INDIKATOR TAHAP 1 — DETAIL INOVASI (CRUD INDIKATOR)
        print("   -> [Tahap 1] Masuk ke Detail Inovasi...")
        driver.execute_script("""
            let trs = Array.from(document.querySelectorAll('#tabelTahap1 tbody tr'));
            let targetTr = trs.find(tr => tr.innerText.includes('INOTEK 2025')) || trs[0];
            let btnDetail = targetTr ? targetTr.querySelector('a.btn-primary[href*="/inovasi"]') : null;
            if (btnDetail) {
                btnDetail.scrollIntoView({block:'center'});
                btnDetail.click();
            }
        """)
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelDetailInovasi')))
        take_screenshot_session(driver, 'Test2_Tahap1_DetailInovasi_Index')

        print("   -> [Tahap 1] Uji DataTables Detail Inovasi...")
        test_data_tables(driver, 'Detail Inovasi')

        # Tambah Indikator Baru
        print("   -> [Tahap 1] Tambah Indikator Inovasi...")
        execute_script_click('#btnTambahIndikator')
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalIndikator.show')))
        sleep(0.4)

        input_nama_indikator = driver.find_element(By.ID, 'inputNamaIndikator')
        clear_input(driver, input_nama_indikator)
        input_nama_indikator.send_keys('Indikator Uji Coba Selenium')

        select_jenis = driver.find_element(By.ID, 'selectJenis')
        select_jenis.send_keys('substansi')
        sleep(0.5)
        take_screenshot_session(driver, 'Test2_Tahap1_TambahIndikator_Modal')

        execute_script_click('#btnSimpanIndikator')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelDetailInovasi')))
        take_screenshot_session(driver, 'Test2_Tahap1_TambahIndikator_Success')

        # Ubah Indikator
        print("   -> [Tahap 1] Ubah Indikator Inovasi...")
        search_inovasi = driver.find_elements(By.CSS_SELECTOR, '.dt-search input, .dataTables_filter input')
        if search_inovasi:
            clear_input(driver, search_inovasi[0])
            search_inovasi[0].send_keys('Indikator Uji Coba Selenium')
            sleep(0.8)

        driver.execute_script("""
            let btn = Array.from(document.querySelectorAll('.btn-edit-indikator'))
                .find(b => b.dataset.indikator && b.dataset.indikator.includes('Indikator Uji Coba Selenium'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalIndikator.show')))
        sleep(0.4)

        input_nama_indikator = driver.find_element(By.ID, 'inputNamaIndikator')
        clear_input(driver, input_nama_indikator)
        input_nama_indikator.send_keys('Indikator Uji Coba Selenium (Updated)')

        select_jenis = driver.find_element(By.ID, 'selectJenis')
        select_jenis.send_keys('makalah')
        sleep(0.5)
        take_screenshot_session(driver, 'Test2_Tahap1_UbahIndikator_Modal')

        execute_script_click('#btnSimpanIndikator')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelDetailInovasi')))
        take_screenshot_session(driver, 'Test2_Tahap1_UbahIndikator_Success')

        # 4. INDIKATOR TAHAP 1 — DETAIL KETERANGAN (CRUD PARAMETER)
        print("   -> [Tahap 1] Masuk ke Detail Keterangan Parameter...")
        search_inovasi = driver.find_elements(By.CSS_SELECTOR, '.dt-search input, .dataTables_filter input')
        if search_inovasi:
            clear_input(driver, search_inovasi[0])
            search_inovasi[0].send_keys('Indikator Uji Coba Selenium')
            sleep(0.8)

        driver.execute_script("""
            let trs = Array.from(document.querySelectorAll('#tabelDetailInovasi tbody tr'));
            let tr = trs.find(r => r.innerText.includes('Indikator Uji Coba Selenium'));
            if (tr) {
                let link = tr.querySelector('a.btn-primary[href*="/detail/"]');
                if (link) { link.scrollIntoView({block:'center'}); link.click(); }
            }
        """)
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelKeterangan')))
        take_screenshot_session(driver, 'Test2_Tahap1_DetailKeterangan_Index')

        print("   -> [Tahap 1] Uji DataTables Keterangan...")
        test_data_tables(driver, 'Detail Keterangan')

        # Tambah Keterangan
        print("   -> [Tahap 1] Tambah Keterangan Parameter...")
        execute_script_click('#btnTambahKeterangan')
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalKeterangan.show')))
        sleep(0.4)

        input_ket = driver.find_element(By.ID, 'inputKeterangan')
        clear_input(driver, input_ket)
        input_ket.send_keys('Keterangan Parameter Uji Coba')

        input_min = driver.find_element(By.ID, 'inputNilaiMinimal')
        clear_input(driver, input_min)
        input_min.send_keys('10')

        input_max = driver.find_element(By.ID, 'inputNilaiMaksimal')
        clear_input(driver, input_max)
        input_max.send_keys('90')
        sleep(0.5)
        take_screenshot_session(driver, 'Test2_Tahap1_TambahKeterangan_Modal')

        execute_script_click('#formKeterangan button[type="submit"]')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelKeterangan')))
        take_screenshot_session(driver, 'Test2_Tahap1_TambahKeterangan_Success')

        # Ubah Keterangan
        print("   -> [Tahap 1] Ubah Keterangan Parameter...")
        driver.execute_script("""
            let btn = Array.from(document.querySelectorAll('.btn-edit-keterangan'))
                .find(b => b.dataset.keterangan && b.dataset.keterangan.includes('Keterangan Parameter Uji Coba'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalKeterangan.show')))
        sleep(0.4)

        input_ket = driver.find_element(By.ID, 'inputKeterangan')
        clear_input(driver, input_ket)
        input_ket.send_keys('Keterangan Parameter Uji Coba (Updated)')

        input_min = driver.find_element(By.ID, 'inputNilaiMinimal')
        clear_input(driver, input_min)
        input_min.send_keys('20')

        input_max = driver.find_element(By.ID, 'inputNilaiMaksimal')
        clear_input(driver, input_max)
        input_max.send_keys('95')
        sleep(0.5)
        take_screenshot_session(driver, 'Test2_Tahap1_UbahKeterangan_Modal')

        execute_script_click('#formKeterangan button[type="submit"]')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelKeterangan')))
        take_screenshot_session(driver, 'Test2_Tahap1_UbahKeterangan_Success')

        # Hapus Keterangan
        print("   -> [Tahap 1] Hapus Keterangan Parameter...")
        driver.execute_script("""
            let btn = Array.from(document.querySelectorAll('.btn-hapus-keterangan'))
                .find(b => b.dataset.nama && b.dataset.nama.includes('Keterangan Parameter Uji Coba'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalHapusKeterangan.show')))
        sleep(0.4)
        take_screenshot_session(driver, 'Test2_Tahap1_HapusKeterangan_Modal')
        execute_script_click('#formHapusKeterangan button[type="submit"]')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelKeterangan')))
        take_screenshot_session(driver, 'Test2_Tahap1_HapusKeterangan_Success')

        # Navigasi Kembali ke Detail Inovasi
        print("   -> [Tahap 1] Navigasi Kembali ke Detail Inovasi...")
        driver.execute_script("""
            let back = document.querySelector('a.btn-dark[href*="/inovasi"]') ||
                       Array.from(document.querySelectorAll('a')).find(a => a.innerText.includes('Kembali'));
            if (back) { back.scrollIntoView({block:'center'}); back.click(); }
        """)
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelDetailInovasi')))

        # 5. INDIKATOR TAHAP 1 — HAPUS INDIKATOR TEST & KEMBALI
        print("   -> [Tahap 1] Hapus Indikator Test...")
        search_inovasi = driver.find_elements(By.CSS_SELECTOR, '.dt-search input, .dataTables_filter input')
        if search_inovasi:
            clear_input(driver, search_inovasi[0])
            search_inovasi[0].send_keys('Indikator Uji Coba Selenium')
            sleep(0.8)

        driver.execute_script("""
            let btn = Array.from(document.querySelectorAll('.btn-hapus-indikator'))
                .find(b => b.dataset.nama && b.dataset.nama.includes('Indikator Uji Coba Selenium'));
            if (btn) { btn.scrollIntoView({block:'center'}); btn.click(); }
        """)
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalHapusIndikator.show')))
        sleep(0.4)
        take_screenshot_session(driver, 'Test2_Tahap1_HapusIndikator_Modal')
        execute_script_click('#formHapusIndikator button[type="submit"]')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelDetailInovasi')))
        take_screenshot_session(driver, 'Test2_Tahap1_HapusIndikator_Success')

        # Navigasi Kembali ke Tahap 1 List
        print("   -> [Tahap 1] Navigasi Kembali ke Halaman Indikator Tahap 1...")
        driver.execute_script("""
            let back = document.querySelector('a.btn-dark[href*="/indikator/tahap-1"]') ||
                       Array.from(document.querySelectorAll('a')).find(a => a.innerText.includes('Kembali'));
            if (back) { back.scrollIntoView({block:'center'}); back.click(); }
        """)
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap1')))

        # 6. INDIKATOR TAHAP 2 — LIST & FORMULASI
        print("   -> [Tahap 2] Navigasi Halaman Indikator Tahap 2...")
        navigate_to('/indikator/tahap-2')
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap2')))
        take_screenshot_session(driver, 'Test2_Tahap2_Index')

        print("   -> [Tahap 2] Uji DataTables Sub Event...")
        test_data_tables(driver, 'Indikator Tahap 2')

        print("   -> [Tahap 2] Uji Formulasi Bobot Nilai...")
        try:
            btn_form2 = driver.find_element(By.CSS_SELECTOR, '.btn-open-formulasi')
            helpers.highlight_and_click(driver, btn_form2, duration=1.0)
            sleep(1)

            inv = driver.find_element(By.ID, 'inputNilaiInovasi')
            per = driver.find_element(By.ID, 'inputNilaiPeragaan')
            helpers.highlight_element(driver, inv, duration=0.5)
            inv.clear()
            inv.send_keys('50')
            helpers.highlight_element(driver, per, duration=0.5)
            per.clear()
            per.send_keys('50')
            sleep(0.5)
            take_screenshot_session(driver, 'Test2_Tahap2_Formulasi_Modal')

            btn_simpan2 = driver.find_element(By.ID, 'btnSimpan2')
            helpers.highlight_and_click(driver, btn_simpan2, duration=1.0)
            sleep(2)
            take_screenshot_session(driver, 'Test2_Tahap2_Formulasi_Saved')
        except Exception as e_form2:
            print("         -> (Notice formulasi 2:", str(e_form2).splitlines()[0], ")")

        # 7. INDIKATOR TAHAP 2 — DETAIL INDIKATOR (CRUD)
        print("   -> [Tahap 2] Masuk ke Detail Indikator Tahap 2...")
        btn_detail2 = driver.find_element(By.CSS_SELECTOR, '#tabelTahap2 tbody a.btn-primary[href*="/indikator"]')
        helpers.highlight_and_click(driver, btn_detail2, duration=1.0)
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap2Detail')))
        take_screenshot_session(driver, 'Test2_Tahap2_Detail_Index')

        print("   -> [Tahap 2] Uji DataTables Detail Indikator Tahap 2...")
        test_data_tables(driver, 'Detail Indikator Tahap 2')

        # Tambah Indikator Tahap 2
        print("   -> [Tahap 2] Tambah Indikator Nominator...")
        execute_script_click('#btnTambahIndikator')
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalIndikator.show')))
        sleep(0.4)

        input_nama_indikator = driver.find_element(By.ID, 'inputNamaIndikator')
        helpers.highlight_element(driver, input_nama_indikator, duration=0.5)
        clear_input(driver, input_nama_indikator)
        input_nama_indikator.send_keys('Indikator Tahap 2 Selenium')

        select_jenis2 = driver.find_element(By.ID, 'inputJenis')
        helpers.highlight_element(driver, select_jenis2, duration=0.5)
        select_jenis2.send_keys('Subtansi Inovasi')

        input_ket = driver.find_element(By.ID, 'inputKeterangan')
        helpers.highlight_element(driver, input_ket, duration=0.5)
        clear_input(driver, input_ket)
        input_ket.send_keys('Keterangan Uji Coba Tahap 2')

        input_min = driver.find_element(By.ID, 'inputNilaiMinimal')
        helpers.highlight_element(driver, input_min, duration=0.5)
        clear_input(driver, input_min)
        input_min.send_keys('50')

        input_max = driver.find_element(By.ID, 'inputNilaiMaksimal')
        helpers.highlight_element(driver, input_max, duration=0.5)
        clear_input(driver, input_max)
        input_max.send_keys('100')
        sleep(0.5)
        take_screenshot_session(driver, 'Test2_Tahap2_TambahIndikator_Modal')

        execute_script_click('#formIndikator button[type="submit"]')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap2Detail')))
        take_screenshot_session(driver, 'Test2_Tahap2_TambahIndikator_Success')

        # Ubah Indikator Tahap 2
        print("   -> [Tahap 2] Ubah Indikator Nominator...")
        search_tahap2 = driver.find_elements(By.CSS_SELECTOR, '.dt-search input, .dataTables_filter input')
        if search_tahap2:
            helpers.highlight_element(driver, search_tahap2[0], duration=0.6)
            clear_input(driver, search_tahap2[0])
            search_tahap2[0].send_keys('Indikator Tahap 2 Selenium')
            sleep(1)

        btn_edit2 = driver.find_element(By.CSS_SELECTOR, '.btn-edit-indikator')
        helpers.highlight_and_click(driver, btn_edit2, duration=1.0)
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalIndikator.show')))
        sleep(0.4)

        input_nama_indikator = driver.find_element(By.ID, 'inputNamaIndikator')
        helpers.highlight_element(driver, input_nama_indikator, duration=0.5)
        clear_input(driver, input_nama_indikator)
        input_nama_indikator.send_keys('Indikator Tahap 2 Selenium (Updated)')

        select_jenis2 = driver.find_element(By.ID, 'inputJenis')
        helpers.highlight_element(driver, select_jenis2, duration=0.5)
        select_jenis2.send_keys('Peragaan')

        input_ket = driver.find_element(By.ID, 'inputKeterangan')
        helpers.highlight_element(driver, input_ket, duration=0.5)
        clear_input(driver, input_ket)
        input_ket.send_keys('Keterangan Uji Coba Tahap 2 (Updated)')

        input_min = driver.find_element(By.ID, 'inputNilaiMinimal')
        helpers.highlight_element(driver, input_min, duration=0.5)
        clear_input(driver, input_min)
        input_min.send_keys('60')

        input_max = driver.find_element(By.ID, 'inputNilaiMaksimal')
        helpers.highlight_element(driver, input_max, duration=0.5)
        clear_input(driver, input_max)
        input_max.send_keys('95')
        sleep(0.5)
        take_screenshot_session(driver, 'Test2_Tahap2_UbahIndikator_Modal')

        execute_script_click('#formIndikator button[type="submit"]')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap2Detail')))
        take_screenshot_session(driver, 'Test2_Tahap2_UbahIndikator_Success')

        # Hapus Indikator Tahap 2
        print("   -> [Tahap 2] Hapus Indikator Nominator...")
        search_tahap2 = driver.find_elements(By.CSS_SELECTOR, '.dt-search input, .dataTables_filter input')
        if search_tahap2:
            helpers.highlight_element(driver, search_tahap2[0], duration=0.6)
            clear_input(driver, search_tahap2[0])
            search_tahap2[0].send_keys('Indikator Tahap 2 Selenium')
            sleep(1)

        btn_hapus2 = driver.find_element(By.CSS_SELECTOR, '.btn-hapus-indikator')
        helpers.highlight_and_click(driver, btn_hapus2, duration=1.0)
        sleep(0.8)
        WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#modalHapus.show')))
        sleep(0.4)
        take_screenshot_session(driver, 'Test2_Tahap2_HapusIndikator_Modal')
        execute_script_click('#formHapus button[type="submit"]')
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap2Detail')))
        take_screenshot_session(driver, 'Test2_Tahap2_HapusIndikator_Success')

        # Navigasi Kembali ke Tahap 2 List
        print("   -> [Tahap 2] Navigasi Kembali ke Halaman Indikator Tahap 2...")
        btn_back_t2 = driver.find_element(By.CSS_SELECTOR, 'a.btn-dark[href*="/indikator/tahap-2"], a[href*="/indikator/tahap-2"]')
        helpers.highlight_and_click(driver, btn_back_t2, duration=1.0)
        sleep(2)
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, 'tabelTahap2')))

        # 8. LOGOUT
        print("   -> Logout Admin...")
        driver.execute_script("""
            let avatar = document.querySelector('#kt_header_user_menu_toggle .symbol') || document.querySelector('.cursor-pointer.symbol'); 
            if(avatar) { avatar.scrollIntoView({block:'center'}); avatar.click(); }
        """)
        sleep(1)
        driver.execute_script("""
            let signOut = Array.from(document.querySelectorAll('a')).find(a => a.textContent.includes('Sign Out')); 
            if(signOut) { signOut.click(); }
        """)
        sleep(3)

        print("✅ INDIKATOR TEST SELESAI (Semua Halaman & Fitur Berhasil Didemonstrasikan)")

    except Exception as e:
        print("❌ Error:", e)
        take_screenshot_on_error(driver, 'Test2_IndikatorTest_Error')
        sys.exit(1)
    finally:
        driver.quit()

if __name__ == '__main__':
    test2_indikator()
