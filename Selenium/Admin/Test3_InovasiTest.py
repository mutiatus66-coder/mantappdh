import os
import sys
import time

# Load helpers and resolve venv path before importing selenium
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
import helpers
from helpers import sleep, test_data_tables, take_screenshot_session, clear_input, demo_toggle_theme

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def test3_inovasi():
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
        print("[3] INOVASI DATA (Selenium Python - Interaksi Lengkap)")

        print("   -> Login Admin (dengan highlight penuh)...")
        helpers.login_admin_with_highlights(driver, baseUrl)

        # Demonstrasi Toggle Theme
        print("   -> Demonstrasi Toggle Theme...")
        demo_toggle_theme(driver, 'Admin_Inovasi')

        # 1. RIWAYAT INOVASI
        print("   -> Riwayat Inovasi (Detail Interaksi)...")
        execute_script_click('a.ri-menu-item[href="/inovasi/riwayat"]')
        sleep(2)
        take_screenshot_session(driver, 'Test3_RiwayatInovasi_Index')
        test_data_tables(driver, 'Riwayat Inovasi')

        print("      -> Menekan 'Lihat Usulan' pada sub-event...")
        click_by_text('Lihat Usulan')
        sleep(2)
        take_screenshot_session(driver, 'Test3_RiwayatUsulan_Detail')

        print("      -> Uji DataTables Riwayat Usulan...")
        test_data_tables(driver, 'Riwayat Usulan', 'Diskominfo')

        print("      -> Uji pencarian spesifik di Riwayat Usulan...")
        search_input_riwayat = driver.find_elements(By.CSS_SELECTOR, '.dt-search input, .dataTables_filter input')
        if search_input_riwayat:
            clear_input(driver, search_input_riwayat[0])
            search_input_riwayat[0].send_keys('Diskominfo')
            sleep(1)
            take_screenshot_session(driver, 'Test3_RiwayatUsulan_Search')
            clear_input(driver, search_input_riwayat[0])
            sleep(1)

        print("      -> Uji interaksi tombol + / responsive DataTables...")
        try:
            dtr_control = driver.find_elements(By.CSS_SELECTOR, 'td.dt-control, td.dtr-control')
            if dtr_control:
                dtr_control[0].click()
                sleep(1)
        except Exception:
            print("         -> (Tombol + tidak aktif / layar cukup lebar)")

        print("      -> Uji klik tautan dokumen / media (Surat, Proposal, Gambar, Video)...")
        try:
            doc_links = driver.find_elements(By.CSS_SELECTOR, 'a[href*="storage"], a[href*="youtube"]')
            if doc_links:
                print(f"         -> Ditemukan {len(doc_links)} tautan dokumen/media.")
                helpers.highlight_element(driver, doc_links[0], duration=1.0)
                sleep(0.5)
        except Exception:
            pass

        print("      -> Menekan tombol Kembali ke Riwayat Inovasi...")
        click_by_text('Kembali')
        sleep(1.5)

        # 2. REKAP NILAI
        print("   -> Rekap Nilai (Detail Interaksi & Ekspor)...")
        execute_script_click('a.ri-menu-item[href="/inovasi/rekap-nilai"]')
        sleep(2)
        take_screenshot_session(driver, 'Test3_RekapNilai_Index')
        test_data_tables(driver, 'Rekap Nilai')

        print("      -> Menekan 'Lihat Nilai' pada sub-event...")
        click_by_text('Lihat Nilai')
        sleep(3)
        take_screenshot_session(driver, 'Test3_RekapPendaftar_Detail')

        print("      -> Uji DataTables Rekap Pendaftar...")
        test_data_tables(driver, 'Rekap Nilai', 'Diskominfo')

        print("      -> Uji sorting kolom Nilai Total...")
        try:
            th_nilai_total = driver.find_elements(By.XPATH, "//th[contains(normalize-space(), 'Nilai Total')]")
            if th_nilai_total:
                helpers.highlight_and_click(driver, th_nilai_total[0], duration=1.0)
                sleep(1)
                helpers.highlight_and_click(driver, th_nilai_total[0], duration=1.0)
                sleep(1)
                take_screenshot_session(driver, 'Test3_RekapPendaftar_SortNilai')
        except Exception:
            pass

        print("      -> Uji Ekspor PDF...")
        try:
            btn_pdf = driver.find_elements(By.CSS_SELECTOR, '.buttons-pdf')
            if btn_pdf:
                helpers.highlight_and_click(driver, btn_pdf[0], duration=1.5)
                sleep(2)
                take_screenshot_session(driver, 'Test3_Export_PDF')
        except Exception:
            print("         -> Tombol PDF tidak ditemukan")

        print("      -> Uji Ekspor Excel...")
        try:
            btn_excel = driver.find_elements(By.CSS_SELECTOR, '.buttons-excel')
            if btn_excel:
                helpers.highlight_and_click(driver, btn_excel[0], duration=1.5)
                sleep(2)
                take_screenshot_session(driver, 'Test3_Export_Excel')
        except Exception:
            print("         -> Tombol Excel tidak ditemukan")

        print("      -> Uji Ekspor Print...")
        try:
            btn_print = driver.find_elements(By.CSS_SELECTOR, '.buttons-print')
            if btn_print:
                helpers.highlight_element(driver, btn_print[0], duration=1.0)
                sleep(0.5)
        except Exception:
            pass

        print("      -> Menekan tombol Kembali ke Rekap Nilai...")
        click_by_text('Kembali')
        sleep(1.5)

        # 3. AUDIT LOG (RIWAYAT HALAMAN)
        print("   -> Buka Audit Log / Riwayat Halaman...")
        try:
            btn_hist = driver.find_element(By.CSS_SELECTOR, 'button[onclick="toggleHistoryPanel()"], .btn-history')
            helpers.highlight_and_click(driver, btn_hist, duration=1.0)
        except Exception:
            driver.execute_script("if(typeof window.toggleHistoryPanel === 'function') window.toggleHistoryPanel();")
        sleep(2)
        take_screenshot_session(driver, 'Test3_History_Panel')
        driver.execute_script("if(typeof window.closeHistoryPanel === 'function') window.closeHistoryPanel();")
        sleep(1)

        # LOGOUT
        print("   -> Logout Admin...")
        try:
            avatar = driver.find_element(By.CSS_SELECTOR, '#kt_header_user_menu_toggle .symbol, .cursor-pointer.symbol')
            helpers.highlight_and_click(driver, avatar, duration=0.8)
            sleep(1)
            sign_out = driver.find_element(By.XPATH, "//a[contains(normalize-space(), 'Sign Out') or contains(normalize-space(), 'Keluar')]")
            helpers.highlight_and_click(driver, sign_out, duration=0.8)
            sleep(3)
        except Exception:
            pass

        print("✅ INOVASI TEST SELESAI (Seluruh Interaksi Berhasil Didemonstrasikan)")

    except Exception as e:
        print("❌ Error:", e)
        sys.exit(1)
    finally:
        driver.quit()

if __name__ == '__main__':
    test3_inovasi()
