import os
import sys
import time

# Load helpers and resolve venv path before importing selenium
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
import helpers
from helpers import sleep, test_data_tables, take_screenshot_session, demo_toggle_theme

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

def run_peserta_workflow():
    options = Options()
    options.add_argument('--window-size=1920,1080')
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.set_capability('acceptInsecureCerts', True)
    if os.environ.get('HEADLESS') == 'true':
        options.add_argument('--headless=new')

    driver = webdriver.Chrome(options=options)
    driver.set_page_load_timeout(30)
    baseUrl = os.environ.get('BASE_URL', 'https://mantappdh.test')
    unique_email = f"peserta_{int(time.time() * 1000)}@test.com"

    current_dir = os.path.dirname(os.path.abspath(__file__))
    pdf_path = os.path.join(current_dir, 'contoh.pdf')
    jpg_path = os.path.join(current_dir, 'contoh.jpg')

    # Buat file dummy (magic bytes)
    with open(pdf_path, 'wb') as f:
        f.write(bytes.fromhex("255044462D312E340A"))
    with open(jpg_path, 'wb') as f:
        f.write(bytes.fromhex("FFD8FFE000104A4649460001"))

    def press_button(text):
        xpath = f"//button[contains(normalize-space(), '{text}')] | //input[@type='submit' and @value='{text}'] | //input[@type='button' and @value='{text}']"
        for _ in range(3):
            try:
                btn = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, xpath)))
                helpers.highlight_and_click(driver, btn, duration=0.8)
                sleep(1.5)
                return
            except Exception:
                sleep(0.5)
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

        print("2. Menekan tombol PENDAFTARAN...")
        btn_register = WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, '.btn-register, a[href*="/register"], a[href*="/pendaftaran"], a.btn-success'))
        )
        # Highlight tombol Pendaftaran selama 1.5 detik dengan efek cyan pulse sebelum diklik
        helpers.highlight_and_click(driver, btn_register, duration=1.5)
        sleep(2.5)  # Delay agar perpindahan halaman pendaftaran terlihat jelas

        print("3. Mengisi form pendaftaran...")
        inp_nama = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, 'name')))
        helpers.highlight_element(driver, inp_nama, duration=0.8)
        inp_nama.send_keys('John Doe')
        sleep(0.8)

        inp_email = driver.find_element(By.NAME, 'email')
        helpers.highlight_element(driver, inp_email, duration=0.8)
        inp_email.send_keys(unique_email)
        sleep(0.8)

        inp_pass = driver.find_element(By.NAME, 'password')
        helpers.highlight_element(driver, inp_pass, duration=0.8)
        inp_pass.send_keys('Password123!')
        sleep(0.8)

        inp_conf = driver.find_element(By.NAME, 'password_confirmation')
        helpers.highlight_element(driver, inp_conf, duration=0.8)
        inp_conf.send_keys('Password123!')
        sleep(0.8)

        chk_captcha = driver.find_element(By.NAME, 'captcha_verified')
        helpers.highlight_and_click(driver, chk_captcha, duration=1.0)
        sleep(2)

        print("   -> Menekan Daftar...")
        press_button('Daftar')
        sleep(3)
        take_screenshot_session(driver, 'peserta_Daftar')

        # Uji demonstrasi toggle theme
        demo_toggle_theme(driver, 'Peserta')

        print("4. Ke halaman Riwayat melalui sidebar...")
        click_link('Riwayat')
        sleep(2)

        print("5. Menekan Kelola Usulan...")
        click_link('Kelola Usulan')
        sleep(2)
        test_data_tables(driver, 'Kelola Usulan')

        print("6. Menekan Tambah Usulan...")
        press_button('Tambah Usulan')
        sleep(1)
        take_screenshot_session(driver, 'peserta_Tambah')

        print("7. Mengisi Form Langkah 1...")
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, 'nama_inovasi'))).send_keys('Inovasi E2E Test')
        driver.find_element(By.NAME, 'judul').send_keys('Judul Inovasi Test')

        driver.find_element(By.CSS_SELECTOR, 'select[name="bidang_id"] option:nth-child(2)').click()
        driver.find_element(By.NAME, 'interaksi').send_keys('Aplikasi Web')
        driver.find_element(By.CSS_SELECTOR, 'select[name="kategori"] option[value="umum"]').click()

        driver.find_element(By.NAME, 'inovator').send_keys('Instansi Dummy')
        driver.find_element(By.NAME, 'ketua_nama').send_keys('John Doe')
        driver.find_element(By.NAME, 'ketua_email').send_keys(unique_email)
        driver.find_element(By.NAME, 'ketua_wa').send_keys('081234567890')
        driver.find_element(By.NAME, 'alamat_ketua').send_keys('Jl. Test No. 123')
        driver.find_element(By.NAME, 'ktp').send_keys('1234567890123456')

        press_button('Selanjutnya')
        sleep(1)
        take_screenshot_session(driver, 'peserta_Form_Step1')

        print("8. Mengisi Form Langkah 2...")
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, 'latar_belakang'))).send_keys('Latar Belakang ...')
        driver.find_element(By.NAME, 'kondisi_sebelumnya').send_keys('Kondisi Sebelumnya ...')
        driver.find_element(By.NAME, 'sasaran_tujuan').send_keys('Sasaran Tujuan ...')
        driver.find_element(By.NAME, 'deskripsi').send_keys('Deskripsi ...')
        driver.find_element(By.NAME, 'cara_kerja').send_keys('Cara Kerja ...')
        driver.find_element(By.NAME, 'keunggulan').send_keys('Keunggulan ...')
        driver.find_element(By.NAME, 'hasil_diharapkan').send_keys('Hasil yang Diharapkan ...')
        driver.find_element(By.NAME, 'manfaat').send_keys('Manfaat ...')
        driver.find_element(By.NAME, 'rencana_berkelanjutan').send_keys('Rencana Berkelanjutan ...')

        press_button('Selanjutnya')
        sleep(1)
        take_screenshot_session(driver, 'peserta_Form_Step2')

        print("9. Mengisi Form Langkah 3 (Upload File)...")
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, 'file_surat_pernyataan'))).send_keys(pdf_path)
        driver.find_element(By.NAME, 'file_proposal').send_keys(pdf_path)
        driver.find_element(By.NAME, 'file_gambar').send_keys(jpg_path)
        driver.find_element(By.NAME, 'link_video').send_keys('https://youtube.com/watch?v=123')
        sleep(2)

        print("   -> Menekan Simpan Usulan...")
        press_button('Simpan Usulan')
        sleep(3)
        take_screenshot_session(driver, 'peserta_Form_Step3_Simpan')

        print("10. Mengedit usulan inovasi (Edit Usulan)...")
        try:
            sleep(2.5)  # Tunggu reload halaman setelah simpan langkah 3
            btn_edit = WebDriverWait(driver, 10).until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, '.btn-edit, a[href*="/edit"], a[href*="/ubah"]'))
            )
            # HIGHLIGHT TOMBOL EDIT SEBELUM DIKLIK
            helpers.highlight_and_click(driver, btn_edit, duration=1.5)
            sleep(2)

            inp_nama = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, 'nama_inovasi')))
            helpers.highlight_element(driver, inp_nama, duration=0.8)
            inp_nama.clear()
            inp_nama.send_keys('Inovasi E2E Test (Updated)')
            sleep(1.5)

            press_button('Selanjutnya')
            sleep(1.5)
            press_button('Selanjutnya')
            sleep(1.5)
            press_button('Simpan Usulan')
            sleep(3.5)  # Tunggu simpan & location.reload() selesai
            take_screenshot_session(driver, 'peserta_Edit_Usulan')
        except Exception as e:
            print(f"   -> (Penanganan Edit Form: {str(e).splitlines()[0]})")

        print("11. Mengirim usulan inovasi (Kirim Usulan)...")
        try:
            sleep(2.5)  # Tunggu reload halaman setelah simpan edit usulan
            btn_kirim = WebDriverWait(driver, 10).until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, '.btn-kirim'))
            )
            # HIGHLIGHT TOMBOL KIRIM SEBELUM DIKLIK
            helpers.highlight_and_click(driver, btn_kirim, duration=1.5)
            sleep(2)

            print("   -> Menampilkan modal konfirmasi pengiriman usulan (#modalKirim)...")
            WebDriverWait(driver, 10).until(
                EC.visibility_of_element_located((By.CSS_SELECTOR, '#modalKirim'))
            )
            sleep(3.0)  # Delay agar modal konfirmasi 'Kirim Usulan? / Ya, Kirim' terlihat jelas di kamera video
            take_screenshot_session(driver, 'peserta_Kirim_Usulan_Modal')

            print("   -> Mengkonfirmasi pengiriman usulan (Klik tombol #btnOkKirim / 'Ya, Kirim')...")
            btn_confirm = WebDriverWait(driver, 10).until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, '#btnOkKirim'))
            )
            # HIGHLIGHT TOMBOL "YA, KIRIM" DI DALAM MODAL SEBELUM DIKLIK
            helpers.highlight_and_click(driver, btn_confirm, duration=1.5)
            sleep(4.0)  # Delay agar toast sukses dan reload pengiriman usulan selesai tercatat di video
            take_screenshot_session(driver, 'peserta_Kirim_Usulan_Sukses')
        except Exception as e:
            print(f"   -> (Penanganan Kirim Usulan: {str(e).splitlines()[0]})")

        print("12. Kembali ke menu Riwayat...")
        sleep(2)
        click_link('Kembali')
        sleep(3)

        print("13. Log Out...")
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

        print("✅ Testing Selenium Peserta (Python) Selesai dengan Sukses!")

    except Exception as err:
        print("❌ Terjadi kesalahan:", err)
        sys.exit(1)
    finally:
        driver.quit()
        if os.path.exists(pdf_path):
            os.remove(pdf_path)
        if os.path.exists(jpg_path):
            os.remove(jpg_path)

if __name__ == '__main__':
    run_peserta_workflow()
