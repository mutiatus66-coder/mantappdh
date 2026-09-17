import os
import sys
import time

# Load helpers and resolve venv path before importing selenium
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
import helpers
from helpers import (
    sleep, test_data_tables, take_screenshot_session,
    click_smart_link, clear_input, demo_toggle_theme
)

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def run_penilai_workflow():
    options = Options()
    options.add_argument('--window-size=1920,1080')
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--remote-allow-origins=*')
    options.add_argument('--disable-gpu')
    options.set_capability('acceptInsecureCerts', True)
    if os.environ.get('HEADLESS') == 'true':
        options.add_argument('--headless=new')

    driver = webdriver.Chrome(options=options)
    driver.set_page_load_timeout(30)
    baseUrl = os.environ.get('BASE_URL', 'https://mantappdh.test')
    emailPenilai = "ahmad.fauzi@example.com"
    passwordPenilai = "password"

    def press_button(text, required=True):
        xpath = f"//button[contains(normalize-space(), '{text}')] | //input[@type='submit' and @value='{text}'] | //input[@type='button' and @value='{text}']"
        timeout = 10 if required else 3
        for _ in range(3):
            try:
                btn = WebDriverWait(driver, timeout).until(EC.presence_of_element_located((By.XPATH, xpath)))
                helpers.highlight_and_click(driver, btn, duration=0.8)
                sleep(1.5)
                return True
            except Exception as err:
                if not required:
                    print(f"   -> (Tombol '{text}' tidak ditemukan, dilewati)")
                    return False
                sleep(0.5)
        if not required:
            print(f"   -> (Tombol '{text}' tidak ditemukan, dilewati)")
            return False
        raise Exception(f"Gagal menekan tombol {text} setelah retries.")

    def click_link(text):
        xpath = f"//a[contains(normalize-space(), '{text}')]"
        for _ in range(3):
            try:
                link = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, xpath)))
                helpers.highlight_and_click(driver, link, duration=0.8)
                sleep(1.5)
                return
            except Exception:
                sleep(0.5)
        raise Exception(f"Gagal mengklik link {text} setelah retries.")

    try:
        print("1. Membuka landing page...")
        driver.get(baseUrl)
        sleep(3)  # Delay agar penonton video panduan dapat melihat tampilan landing page secara jelas

        print("2. Menekan tombol Login...")
        try:
            btn_login = WebDriverWait(driver, 5).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, '.btn-login, a[href*="/login"], a.nav-link[href*="/login"]'))
            )
            helpers.highlight_and_click(driver, btn_login, duration=1.5)
            sleep(2.5)  # Delay agar perpindahan halaman login terlihat jelas
        except Exception:
            driver.get(f"{baseUrl}/login")
            sleep(2.5)

        print("3. Memasukkan kredensial login penilai...")
        inp_email = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, 'email')))
        helpers.highlight_element(driver, inp_email, duration=0.8)
        inp_email.send_keys(emailPenilai)
        sleep(0.8)

        inp_pass = driver.find_element(By.NAME, 'password')
        helpers.highlight_element(driver, inp_pass, duration=0.8)
        inp_pass.send_keys(passwordPenilai)
        sleep(0.8)

        print("   -> Menekan tombol Masuk...")
        press_button('Masuk')
        sleep(3)
        take_screenshot_session(driver, 'penilai_Masuk')

        # Uji demonstrasi toggle theme
        demo_toggle_theme(driver, 'Penilai')

        print("4. Ke halaman Rekap Nilai di awal melalui sidebar (Uji DataTables)...")
        click_link('Rekap Nilai')
        sleep(2)

        print("5. Menekan Lihat Nilai (Uji Sorting, Search, ColVis)...")
        click_smart_link(driver, 'Lihat Nilai')
        sleep(2)
        test_data_tables(driver, 'Rekap Nilai Awal', 'Diskominfo')

        print("6. Menekan tombol Kembali...")
        click_link('Kembali')
        sleep(2)

        print("7. Ke halaman Penilaian Tahap 1 melalui sidebar...")
        click_link('Penilaian Tahap 1')
        sleep(2)

        print("8. Menekan Lihat Nilai Verifikasi...")
        click_smart_link(driver, 'Lihat Nilai Verifikasi')
        sleep(2)
        test_data_tables(driver, 'Penilaian Tahap 1', 'BPBD')

        print("6. Memberi nilai kepada inovator...")
        sleep(1)
        btn_nilai = driver.find_elements(By.CSS_SELECTOR, '.btn-input-nilai')
        loop_count = min(3, len(btn_nilai))

        for i in range(loop_count):
            try:
                btn_nilai = driver.find_elements(By.CSS_SELECTOR, '.btn-input-nilai')
                if i < len(btn_nilai) and btn_nilai[i]:
                    helpers.highlight_and_click(driver, btn_nilai[i], duration=0.8)
                    sleep(1.5)

                    substansi_containers = driver.find_elements(By.CSS_SELECTOR, '.modal.show .mb-4')
                    if not substansi_containers:
                        substansi_containers = driver.find_elements(By.CSS_SELECTOR, '.mb-4')

                    for sub_idx, sub_cont in enumerate(substansi_containers):
                        try:
                            # Isi 1 input per substansi (misal opsi ke-5 / range 85-100 dengan nilai 100)
                            helpers.isi_satu_input_per_substansi(
                                driver,
                                container_substansi=sub_cont,
                                index_target_input=4,
                                nilai_baru="100",
                                batal=False
                            )
                        except Exception as err_sub:
                            print(f"   -> (Gagal mengisi substansi ke-{sub_idx+1}: {err_sub})")

                    press_button('Simpan Nilai', required=False)
                    sleep(1.5)

                take_screenshot_session(driver, f'penilai_Tahap1_SimpanNilai_Inovator_{i+1}')

                btn_catatan = driver.find_elements(By.CSS_SELECTOR, '.btn-catatan')
                if i < len(btn_catatan) and btn_catatan[i]:
                    helpers.highlight_and_click(driver, btn_catatan[i], duration=0.8)
                    sleep(1.5)

                    textareas = driver.find_elements(By.CSS_SELECTOR, 'textarea.form-control')
                    success = False
                    for ta in textareas:
                        try:
                            if ta.is_displayed():
                                helpers.highlight_element(driver, ta, duration=0.4)
                                driver.execute_script("arguments[0].focus();", ta)
                                ta.clear()
                                ta.send_keys(f'Catatan otomatis dari Selenium untuk inovator ke-{i+1}')
                                success = True
                                break
                        except Exception:
                            pass

                    press_button('Simpan Catatan', required=False)
                    sleep(1.5)

                take_screenshot_session(driver, f'penilai_Tahap1_SimpanCatatan_Inovator_{i+1}')
            except Exception as item_err:
                print(f"   -> (Penanganan pengisian nilai inovator ke-{i+1}: {str(item_err).splitlines()[0]})")

        print("7. Filter Total Nilai (Klik header Total Nilai)...")
        try:
            th_total_nilai = driver.find_element(By.XPATH, "//th[contains(normalize-space(), 'Total Nilai')]")
            helpers.highlight_and_click(driver, th_total_nilai, duration=1.0)
            sleep(1.5)
        except Exception:
            pass

        print("8. Menekan check box select all (chk-all)...")
        try:
            chk_all = WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '.chk-all')))
            helpers.highlight_and_click(driver, chk_all, duration=1.0)
            sleep(1.5)
        except Exception:
            pass

        print("9. Menekan tombol Simpan di Tahap 1...")
        press_button('Simpan', required=False)
        sleep(3)
        take_screenshot_session(driver, 'penilai_Tahap1_SimpanFinal')

        print("10. Menekan tombol Kembali...")
        click_link('Kembali')
        sleep(2)

        print("11. Ke halaman Penilaian Tahap 2 melalui sidebar...")
        click_link('Penilaian Tahap 2')
        sleep(2)

        print("12. Menekan Lihat Nilai Nominator...")
        click_smart_link(driver, 'Lihat Nilai Nominator')
        sleep(2)
        test_data_tables(driver, 'Penilaian Tahap 2', 'LaporKita')

        print("13. Menekan tombol Ranking...")
        press_button('Ranking', required=False)
        sleep(2)
        take_screenshot_session(driver, 'penilai_Tahap2_Ranking')

        print("14. Menekan tombol Simpan Ranking...")
        press_button('Simpan Ranking', required=False)
        sleep(2)

        print("15. Menekan tombol Kembali...")
        click_link('Kembali')
        sleep(2)

        # ── HALAMAN TERAKHIR: REKAP NILAI (EKSPOR PDF & EXCEL) ─────────────
        print("16. Ke halaman Rekap Nilai melalui sidebar (Halaman Terakhir)...")
        click_link('Rekap Nilai')
        sleep(2)

        print("17. Menekan Lihat Nilai pada sub-event Rekap Nilai...")
        click_smart_link(driver, 'Lihat Nilai')
        sleep(2)
        test_data_tables(driver, 'Rekap Nilai', 'Diskominfo')

        print("18. Mencoba export PDF di Rekap Nilai (Dengan Highlight Cyan Pulse)...")
        try:
            btn_pdf = WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '.buttons-pdf')))
            helpers.highlight_and_click(driver, btn_pdf, duration=1.5)
            sleep(2.5)
            take_screenshot_session(driver, 'penilai_RekapNilai_ExportPDF')
        except Exception as e:
            print("   -> (Tombol PDF tidak ditemukan:", str(e).splitlines()[0], ")")

        print("19. Mencoba export Excel di Rekap Nilai (Dengan Highlight Cyan Pulse)...")
        try:
            btn_excel = WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '.buttons-excel')))
            helpers.highlight_and_click(driver, btn_excel, duration=1.5)
            sleep(2.5)
            take_screenshot_session(driver, 'penilai_RekapNilai_ExportExcel')
        except Exception as e:
            print("   -> (Tombol Excel tidak ditemukan:", str(e).splitlines()[0], ")")

        print("20. Berhenti di tampilan Rekap Nilai terakhir kalinya...")
        sleep(3.5)  # Jeda 3.5 detik agar tampilan halaman Rekap Nilai terakhir tercatat utuh di video panduan
        take_screenshot_session(driver, 'penilai_RekapNilai_Terakhir')

        print("21. Log Out...")
        try:
            profile_btn = WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, '.cursor-pointer.symbol')))
            driver.execute_script("arguments[0].click();", profile_btn)
            sleep(1)
            sign_out_btn = WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.XPATH, "//a[contains(normalize-space(), 'Sign Out') or contains(normalize-space(), 'Keluar')]")))
            helpers.highlight_element(driver, sign_out_btn, duration=0.8)
            driver.execute_script("arguments[0].click();", sign_out_btn)
            sleep(2)
        except Exception as e:
            print("   -> (Log Out selesai:", str(e).splitlines()[0], ")")

        print("✅ Workflow Penilai E2E (Selenium Python) Selesai dengan Sukses!")

    except Exception as err:
        print("❌ Terjadi kesalahan:", err)
        sys.exit(1)
    finally:
        driver.quit()

if __name__ == '__main__':
    run_penilai_workflow()
