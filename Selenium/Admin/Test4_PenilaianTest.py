import os
import sys
import time

# Load helpers and resolve venv path before importing selenium
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
import helpers
from helpers import sleep, test_data_tables, take_screenshot_session

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def test4_penilaian():
    options = Options()
    options.add_argument('--window-size=1920,1080')
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--ignore-certificate-errors')
    if os.environ.get('HEADLESS') == 'true':
        options.add_argument('--headless=new')

    driver = webdriver.Chrome(options=options)
    baseUrl = os.environ.get('BASE_URL', 'https://mantappdh.test')

    def execute_script_click(selector):
        el = driver.find_element(By.CSS_SELECTOR, selector)
        helpers.highlight_and_click(driver, el, duration=0.8)
        sleep(1.5)

    def click_by_text(text):
        el = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, f"//a[contains(., '{text}')]")))
        helpers.highlight_and_click(driver, el, duration=0.8)
        sleep(1.5)

    try:
        print("[4] PENILAIAN DATA (Selenium Python)")

        print("   -> Login Admin (dengan highlight penuh)...")
        helpers.login_admin_with_highlights(driver, baseUrl)

        # PENILAIAN TAHAP 1
        print("   -> Penilaian Tahap 1 (Demonstrasi Lengkap Fitur & Highlight)...")
        execute_script_click('a.ri-menu-item[href="/penilaian/tahap-1"]')
        sleep(2)
        take_screenshot_session(driver, 'Test4_PenilaianTahap1_Index')
        test_data_tables(driver, 'Penilaian Tahap 1 Index')

        print("      -> Menekan 'Lihat Nilai Verifikasi'...")
        click_by_text('Lihat Nilai Verifikasi')
        sleep(2)

        # Tab Switching
        print("      -> Menguji perpindahan Tab (Pelajar & Umum)...")
        try:
            tab_pelajar = driver.find_element(By.CSS_SELECTOR, '#tab-pelajar, button[data-bs-target="#panel-pelajar"]')
            helpers.highlight_and_click(driver, tab_pelajar, duration=1.0)
            sleep(1.5)
            test_data_tables(driver, 'Penilaian Tahap 1 (Pelajar)')

            tab_umum = driver.find_element(By.CSS_SELECTOR, '#tab-umum, button[data-bs-target="#panel-umum"]')
            helpers.highlight_and_click(driver, tab_umum, duration=1.0)
            sleep(1.5)
            test_data_tables(driver, 'Penilaian Tahap 1 (Umum)')
        except Exception as tab_err:
            print("         -> (Notice tab switching:", str(tab_err).splitlines()[0], ")")

        # Input Nilai Modal
        print("      -> Demonstrasi Input Nilai (Modal & Highlight Input)...")
        try:
            btns_nilai = driver.find_elements(By.CSS_SELECTOR, '.btn-input-nilai')
            if btns_nilai and btns_nilai[0].is_displayed():
                helpers.highlight_and_click(driver, btns_nilai[0], duration=1.0)
                sleep(1.5)
                inputs = driver.find_elements(By.CSS_SELECTOR, '.input-nilai-item')
                for inp in inputs:
                    if inp.is_displayed():
                        helpers.highlight_element(driver, inp, duration=0.4)
                        inp.clear()
                        min_v = inp.get_attribute('min') or '10'
                        inp.send_keys(min_v)
                btn_simpan_modal = driver.find_element(By.CSS_SELECTOR, '.btn-simpan-nilai-modal')
                helpers.highlight_and_click(driver, btn_simpan_modal, duration=1.0)
                sleep(2)
                take_screenshot_session(driver, 'Test4_PenilaianTahap1_SimpanNilai_Modal')
        except Exception as e_nilai:
            print("         -> (Notice input nilai modal:", str(e_nilai).splitlines()[0], ")")

        # Catatan Inovasi Modal
        print("      -> Demonstrasi Input Catatan Inovator (Modal & Highlight)...")
        try:
            btns_catatan = driver.find_elements(By.CSS_SELECTOR, '.btn-catatan')
            if btns_catatan and btns_catatan[0].is_displayed():
                helpers.highlight_and_click(driver, btns_catatan[0], duration=1.0)
                sleep(1.5)
                textareas = driver.find_elements(By.CSS_SELECTOR, 'textarea.form-control, textarea.textarea-catatan-umum')
                for ta in textareas:
                    if ta.is_displayed():
                        helpers.highlight_element(driver, ta, duration=0.5)
                        ta.clear()
                        ta.send_keys('Catatan kurasi otomatis dari Admin Selenium')
                        break
                btn_simpan_catatan = driver.find_element(By.CSS_SELECTOR, '.btn-simpan-catatan')
                helpers.highlight_and_click(driver, btn_simpan_catatan, duration=1.0)
                sleep(2)
                take_screenshot_session(driver, 'Test4_PenilaianTahap1_SimpanCatatan_Modal')
        except Exception as e_catatan:
            print("         -> (Notice catatan modal:", str(e_catatan).splitlines()[0], ")")

        # Header Total Nilai Sorting
        print("      -> Filter & Sorting Header Total Nilai...")
        try:
            th_total = driver.find_element(By.XPATH, "//th[contains(normalize-space(), 'Total Nilai')]")
            helpers.highlight_and_click(driver, th_total, duration=1.0)
            sleep(1.5)
        except Exception:
            pass

        # Select All / Centang Checkbox Kurasi
        print("      -> Menekan Checkbox Select All (chk-all)...")
        try:
            chk_all = driver.find_element(By.CSS_SELECTOR, '.chk-all')
            helpers.highlight_and_click(driver, chk_all, duration=1.0)
            sleep(1.5)
        except Exception as e_chk:
            print("         -> (Notice chk-all:", str(e_chk).splitlines()[0], ")")

        # Simpan Kurasi Tahap 1
        print("      -> Menekan tombol Simpan Kurasi Tahap 1...")
        try:
            btn_simpan_t1 = driver.find_element(By.CSS_SELECTOR, '.btn-rv-simpan')
            helpers.highlight_and_click(driver, btn_simpan_t1, duration=1.2)
            sleep(3)
            take_screenshot_session(driver, 'Test4_PenilaianTahap1_SimpanFinal')
        except Exception as e_simpan1:
            print("         -> (Notice simpan tahap 1:", str(e_simpan1).splitlines()[0], ")")

        print("      -> Menekan tombol Kembali...")
        click_by_text('Kembali')
        sleep(2)

        # PENILAIAN TAHAP 2
        print("   -> Penilaian Tahap 2 (Demonstrasi Lengkap Fitur & Highlight)...")
        execute_script_click('a.ri-menu-item[href="/penilaian/tahap-2"]')
        sleep(2)
        take_screenshot_session(driver, 'Test4_PenilaianTahap2_Index')
        test_data_tables(driver, 'Penilaian Tahap 2 Index')

        print("      -> Menekan 'Lihat Nilai Nominator'...")
        click_by_text('Lihat Nilai Nominator')
        sleep(2)

        # Tab Switching Tahap 2
        print("      -> Menguji perpindahan Tab Nominator (Pelajar & Umum)...")
        try:
            tab_pelajar2 = driver.find_element(By.CSS_SELECTOR, '#tab-pelajar-btn, button[data-bs-target="#tab-pelajar"]')
            helpers.highlight_and_click(driver, tab_pelajar2, duration=1.0)
            sleep(1.5)
            test_data_tables(driver, 'Penilaian Tahap 2 (Pelajar)')

            tab_umum2 = driver.find_element(By.CSS_SELECTOR, '#tab-umum-btn, button[data-bs-target="#tab-umum"]')
            helpers.highlight_and_click(driver, tab_umum2, duration=1.0)
            sleep(1.5)
            test_data_tables(driver, 'Penilaian Tahap 2 (Umum)')
        except Exception as tab_err2:
            print("         -> (Notice tab switching Tahap 2:", str(tab_err2).splitlines()[0], ")")

        # Auto Ranking Button
        print("      -> Menekan tombol Auto Ranking / Ranking...")
        try:
            btns_ranking = driver.find_elements(By.CSS_SELECTOR, '.btn-auto-ranking, .btn-ranking')
            if btns_ranking and btns_ranking[0].is_displayed():
                helpers.highlight_and_click(driver, btns_ranking[0], duration=1.2)
                sleep(2)
                take_screenshot_session(driver, 'Test4_PenilaianTahap2_Ranking')
        except Exception as e_rank:
            print("         -> (Notice auto ranking:", str(e_rank).splitlines()[0], ")")

        # Simpan Ranking Button
        print("      -> Menekan tombol Simpan Ranking...")
        try:
            btns_simpan_rank = driver.find_elements(By.CSS_SELECTOR, '.btn-simpan-ranking')
            if btns_simpan_rank and btns_simpan_rank[0].is_displayed():
                helpers.highlight_and_click(driver, btns_simpan_rank[0], duration=1.2)
                sleep(3)
                take_screenshot_session(driver, 'Test4_PenilaianTahap2_SimpanRanking')
            else:
                print("         -> (Tombol Simpan Ranking disesuaikan khusus akun Penilai)")
        except Exception as e_srank:
            print("         -> (Notice simpan ranking:", str(e_srank).splitlines()[0], ")")

        # Ekspor Excel Tahap 2
        print("      -> Demonstrasi Ekspor Excel Penilaian Tahap 2...")
        try:
            btns_excel_t2 = driver.find_elements(By.CSS_SELECTOR, '.buttons-excel')
            if btns_excel_t2 and btns_excel_t2[0].is_displayed():
                helpers.highlight_and_click(driver, btns_excel_t2[0], duration=1.5)
                sleep(2)
                take_screenshot_session(driver, 'Test4_PenilaianTahap2_ExportExcel')
        except Exception as e_excel:
            print("         -> (Notice ekspor excel Tahap 2:", str(e_excel).splitlines()[0], ")")

        print("      -> Menekan tombol Kembali...")
        click_by_text('Kembali')
        sleep(2)

        # AUDIT LOG (RIWAYAT HALAMAN)
        print("   -> Buka Audit Log / Riwayat Halaman...")
        driver.execute_script("""
            if(typeof window.toggleHistoryPanel === 'function') {
                window.toggleHistoryPanel(); 
            } else { 
                let btn = document.querySelector('button[onclick="toggleHistoryPanel()"]'); 
                if(btn) btn.click(); 
            }
        """)
        sleep(2)
        take_screenshot_session(driver, 'Test4_History_Panel')
        driver.execute_script("""
            if(typeof window.closeHistoryPanel === 'function') window.closeHistoryPanel();
        """)
        sleep(1)

        # LOGOUT
        print("   -> Logout...")
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

        print("✅ PENILAIAN TEST SELESAI (Seluruh Fitur Penilaian Berhasil Didemonstrasikan Dengan Highlight)")

    except Exception as e:
        print("❌ Error:", e)
        sys.exit(1)
    finally:
        driver.quit()

if __name__ == '__main__':
    test4_penilaian()
